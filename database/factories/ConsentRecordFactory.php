<?php

namespace Database\Factories;

use App\Models\ConsentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsentRecord>
 */
class ConsentRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_type' => null,
            'subject_id' => null,
            'subject_name' => fake()->name(),
            'subject_email' => fake()->safeEmail(),
            'document' => 'terms',
            'document_version' => '1.0',
            'document_hash' => hash('sha256', fake()->sentence()),
            'action' => ConsentRecord::ACTION_GRANTED,
            'channel' => ConsentRecord::CHANNEL_WEB_REGISTER,
            'consent_text' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'created_at' => now()->startOfSecond(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ConsentRecord $record): void {
            $record->record_hash = $record->computeHash();
        });
    }
}
