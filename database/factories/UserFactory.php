<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rnd = [0,1,2];
        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password,
            'web_token' => Str::uuid(),
            'jenis_pengguna' => $this->faker->randomElement($rnd),
            'jenis_akun' => $this->faker->randomElement($rnd),
            'email_verified_at' => now(),
            'tgl_langganan_akhir' => date('Y/m/d',strtotime('12/12/2021')),
            'uuid' => Str::uuid(),
            'created_at' => now()
        ];
    }
}
