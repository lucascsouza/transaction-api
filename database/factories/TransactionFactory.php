<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{

    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payer_id' => $this->faker->randomNumber(),
            'payee_id' => $this->faker->randomNumber(),
            'value' => $this->faker->randomFloat('2', '1', '100')
        ];
    }
}
