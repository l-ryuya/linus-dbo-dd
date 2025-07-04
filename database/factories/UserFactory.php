<?php

declare(strict_types=1);

namespace Database\Factories;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        $fakerEn = FakerFactory::create('en_US');

        return [
            'user_code' => null,
            'company_id' => null,
            'latest_dd_id' => null,
            'user_status_type' => null,
            'user_status' => null,
            'last_name_en' => $fakerEn->lastName(),
            'last_name_sl' => $this->faker->lastName(),
            'middle_name_en' => null,
            'middle_name_sl' => null,
            'first_name_en' => $fakerEn->lastName(),
            'first_name_sl' => $this->faker->firstName(),
            'position_en' => $fakerEn->jobTitle(),
            'position_sl' => $this->faker->jobTitle(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => null,
            'roles' => null,
            'remember_token' => null,
            'mobile_phone' => null,
            'nationality_code' => null,
            'gender_type' => null,
            'gender' => null,
            'date_of_birth' => null,
            'place_of_birth_en' => null,
            'place_of_birth_sl' => null,
            'profile_en' => null,
            'profile_sl' => null,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
