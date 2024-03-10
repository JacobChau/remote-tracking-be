<?php

namespace Database\Factories;

use App\Enums\LinkAccessType;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkSetting>
 */
class LinkSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hash' => $this->faker->word,
            'is_enabled' => $this->faker->boolean,
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
            'access_type' => LinkAccessType::getRandomValue(),
            'meeting_id' => Meeting::factory(),
        ];
    }
}
