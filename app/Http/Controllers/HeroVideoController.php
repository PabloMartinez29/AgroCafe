<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HeroVideoController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $path = $this->path();

        abort_unless($path !== null, 404);

        return response()->file($path, [
            'Content-Type' => 'video/mp4',
            'Content-Disposition' => 'inline',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    private function path(): ?string
    {
        foreach ([
            public_path('video/gemini_generated_video_079d102d.mp4'),
            storage_path('app/videos/gemini_generated_video_079d102d.mp4'),
        ] as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
