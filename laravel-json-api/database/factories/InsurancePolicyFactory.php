<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsurancePolicy>
 */
class InsurancePolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'policy_no' => $this->faker->unique()->randomNumber(),
            'customer_id' => Customer::factory(),
            'policy_type' => $this->faker->randomElement(['Health', 'Life', 'Auto', 'Property', 'Travel']),
            'premium_amount' => $this->faker->numberBetween(1000, 100000),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}
