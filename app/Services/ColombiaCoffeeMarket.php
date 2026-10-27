<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ColombiaCoffeeMarket
{
    private const CACHE_KEY = 'colombia-coffee-market';

    private const LAST_CACHE_KEY = 'colombia-coffee-market-last';

    /**
     * Precio vigente del café en Colombia: verde, seco y pasilla.
     * Usa el mismo valor que publica la Federación Nacional de Cafeteros
     * y lo renueva en cuanto ese precio cambia.
     *
     * @return array<string, mixed>|null
     */
    public function current(): ?array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            return $cached;
        }

        try {
            $fresh = $this->fetch();
        } catch (Throwable $exception) {
            Log::warning('No se pudo consultar el precio del café en la Federación.', [
                'message' => $exception->getMessage(),
            ]);
            $fresh = null;
        }

        $last = Cache::get(self::LAST_CACHE_KEY);

        if (is_array($fresh) && is_array($last)) {
            $fresh = $this->fillMissingTypes($fresh, $last);
        }

        if (is_array($fresh)) {
            Cache::put(self::CACHE_KEY, $fresh, now()->addSeconds(30));
            Cache::put(self::LAST_CACHE_KEY, $fresh, now()->addDays(7));

            return $fresh;
        }

        return is_array($last) ? $last : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetch(): array
    {
        $homepage = null;

        try {
            $html = $this->download((string) config('services.fnc.homepage'));
            $homepage = $this->parseHomepage($html);
        } catch (Throwable $exception) {
            Log::warning('No se pudo leer el precio interno de referencia.', [
                'message' => $exception->getMessage(),
            ]);
        }

        $bulletinUrl = $homepage['bulletin_url'] ?? (string) config('services.fnc.bulletin');
        $bulletin = null;

        try {
            $pdf = $this->download($bulletinUrl);
            $bulletin = $this->parseBulletin($this->extractPdfText($pdf));
        } catch (Throwable $exception) {
            Log::warning('No se pudo leer el boletín de precios del café.', [
                'message' => $exception->getMessage(),
            ]);
        }

        if ($homepage === null && $bulletin === null) {
            throw new \RuntimeException('No se pudo leer el precio del mercado.');
        }

        return $this->pricesFromParts($homepage, $bulletin, $bulletinUrl);
    }

    /**
     * @param  array{price: int, date: string|null, bulletin_url: string|null}|null  $homepage
     * @param  array{verde_per_kg: int, pasilla_per_kg: int, seco_carga: int, date: string|null}|null  $bulletin
     * @return array<string, mixed>
     */
    private function pricesFromParts(?array $homepage, ?array $bulletin, string $bulletinUrl): array
    {
        $secoCarga = $homepage['price'] ?? $bulletin['seco_carga'] ?? null;
        $secoDate = $homepage !== null ? $homepage['date'] : ($bulletin['date'] ?? null);
        $bulletinDate = $bulletin['date'] ?? null;

        $types = [
            $this->typePayload(
                'verde',
                'Verde',
                'Café excelso',
                $bulletin['verde_per_kg'] ?? null,
                'kg',
                'por kilogramo',
                null,
                $bulletinDate,
            ),
            $this->typePayload(
                'seco',
                'Seco',
                'Pergamino seco',
                $secoCarga,
                'carga',
                'carga de 125 kg',
                $secoCarga !== null ? (int) round($secoCarga / 125) : null,
                $secoDate,
            ),
            $this->typePayload(
                'pasilla',
                'Pasilla',
                'Pasilla',
                $bulletin['pasilla_per_kg'] ?? null,
                'kg',
                'por kilogramo',
                null,
                $bulletinDate,
            ),
        ];

        return [
            'source' => 'Federación Nacional de Cafeteros de Colombia',
            'source_url' => (string) config('services.fnc.homepage'),
            'bulletin_url' => $bulletinUrl,
            'fetched_at' => now()->timezone('America/Bogota')->toIso8601String(),
            'note' => $this->note($secoDate, $bulletinDate, $bulletin !== null),
            'types' => $types,
        ];
    }

    /**
     * @param  array<string, mixed>  $fresh
     * @param  array<string, mixed>  $last
     * @return array<string, mixed>
     */
    private function fillMissingTypes(array $fresh, array $last): array
    {
        $previous = collect($last['types'] ?? [])->keyBy('key');

        $fresh['types'] = collect($fresh['types'] ?? [])->map(function (array $type) use ($previous): array {
            if ($type['price'] !== null || ! $previous->has($type['key'])) {
                return $type;
            }

            return $previous->get($type['key']);
        })->all();

        return $fresh;
    }

    /**
     * @return array{key: string, name: string, description: string, price: int|null, unit: string, unit_label: string, price_per_kg: int|null, date: string|null, date_label: string|null}
     */
    private function typePayload(
        string $key,
        string $name,
        string $description,
        ?int $price,
        string $unit,
        string $unitLabel,
        ?int $pricePerKg,
        ?string $date,
    ): array {
        return [
            'key' => $key,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'unit' => $unit,
            'unit_label' => $unitLabel,
            'price_per_kg' => $pricePerKg,
            'date' => $date,
            'date_label' => $this->dateLabel($date),
        ];
    }

    private function note(?string $secoDate, ?string $bulletinDate, bool $hasBulletin): string
    {
        if (! $hasBulletin) {
            return 'El café seco es el precio interno de referencia. Verde y pasilla aparecen con el último boletín del mercado.';
        }

        if ($secoDate !== null && $bulletinDate !== null && $secoDate !== $bulletinDate) {
            return 'El café seco es el precio interno de hoy. Verde y pasilla salen del último boletín publicado por el mercado.';
        }

        return 'Verde, seco y pasilla son el mismo precio que publica el mercado.';
    }

    /**
     * @return array{price: int, date: string|null, bulletin_url: string|null}|null
     */
    private function parseHomepage(string $html): ?array
    {
        if (! preg_match('/Precio interno de referencia:\s*\$?\s*([\d.]+)/', $html, $priceMatch)) {
            return null;
        }

        $date = null;

        if (preg_match('/Fecha:\s*(?:<\/strong>\s*)?(\d{4}-\d{2}-\d{2})/', $html, $dateMatch)) {
            $date = $dateMatch[1];
        }

        $bulletinUrl = null;

        if (preg_match('/href="(https:\/\/federaciondecafeteros\.org\/wp-content\/uploads\/[^"]*precio_cafe\.pdf)"/', $html, $urlMatch)) {
            $bulletinUrl = $urlMatch[1];
        }

        return [
            'price' => (int) $this->parseAmount($priceMatch[1]),
            'date' => $date,
            'bulletin_url' => $bulletinUrl,
        ];
    }

    /**
     * @return array{verde_per_kg: int, pasilla_per_kg: int, seco_carga: int, date: string|null}|null
     */
    private function parseBulletin(string $text): ?array
    {
        if (! preg_match('/pergamino seco FR\s*94\s+([\d][\d.,]*)/i', $text, $cargaMatch)) {
            return null;
        }

        if (! preg_match('/pasilla\s+contenida en el pergamino\s+([\d][\d.,]*)/i', $text, $pasillaMatch)) {
            return null;
        }

        if (! preg_match('/(?:^|\n)\s*94\s+([\d.]+)\s+([\d.]+)\s+([\d][\d,]*)\s+([\d][\d,]*)\s+([\d][\d,]*)/', $text, $row)) {
            return null;
        }

        $excelsoKg = (float) $row[1];

        if ($excelsoKg <= 0) {
            return null;
        }

        return [
            'verde_per_kg' => (int) round($this->parseAmount($row[3]) / $excelsoKg),
            'pasilla_per_kg' => (int) round($this->parseAmount($pasillaMatch[1])),
            'seco_carga' => (int) round($this->parseAmount($cargaMatch[1])),
            'date' => $this->parseBulletinDate($text),
        ];
    }

    private function extractPdfText(string $pdf): string
    {
        $lengths = [];

        if (preg_match_all('/(\d+)\s+0\s+obj\s+(\d+)\s+endobj/', $pdf, $lengthObjects, PREG_SET_ORDER)) {
            foreach ($lengthObjects as $object) {
                $lengths[(int) $object[1]] = (int) $object[2];
            }
        }

        $text = '';

        if (! preg_match_all('/\/Length\s+(\d+)\s+0\s+R\s*>>\s*stream\r?\n/', $pdf, $streams, PREG_OFFSET_CAPTURE)) {
            return $text;
        }

        foreach ($streams[1] as $index => $lengthReference) {
            $length = $lengths[(int) $lengthReference[0]] ?? null;

            if ($length === null) {
                continue;
            }

            $start = $streams[0][$index][1] + strlen($streams[0][$index][0]);
            $stream = substr($pdf, $start, $length);

            if (! str_contains($stream, 'Tj') || ! preg_match_all('/<([0-9A-Fa-f]+)>/', $stream, $hexes)) {
                continue;
            }

            foreach ($hexes[1] as $hex) {
                $decoded = hex2bin($hex);

                if ($decoded !== false) {
                    $text .= $decoded."\n";
                }
            }
        }

        return mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
    }

    private function parseBulletinDate(string $text): ?string
    {
        $months = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'setiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];

        if (! preg_match('/(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)\s+(\d{1,2})\s*\/\s*(\d{4})/i', $text, $match)) {
            return null;
        }

        $month = $months[strtolower($match[1])] ?? null;

        if ($month === null) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', (int) $match[3], $month, (int) $match[2]);
    }

    private function parseAmount(string $value): float
    {
        $value = trim($value);

        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $value) === 1) {
            return (float) str_replace('.', '', $value);
        }

        if (preg_match('/^\d{1,3}(,\d{3})+$/', $value) === 1) {
            return (float) str_replace(',', '', $value);
        }

        return (float) str_replace(',', '', $value);
    }

    private function dateLabel(?string $date): ?string
    {
        if ($date === null) {
            return null;
        }

        return Carbon::parse($date)->locale('es')->translatedFormat('j \d\e F \d\e Y');
    }

    private function download(string $url): string
    {
        $response = Http::retry(2, 200)
            ->timeout(20)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; AgroCafe/1.0)',
                'Accept' => '*/*',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException('No se pudo descargar el precio del mercado.');
        }

        return $response->body();
    }
}
