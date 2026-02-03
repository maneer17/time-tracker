<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();
        //'created_at' => fake()->dateTimeBetween('-2 years'),
         $users = User::all();
        for($i=0;$i<20;$i++){
            $user = $users->random();
            Event::factory()->create([
                'user_id' => $user->id
            ]);
        }
         \App\Models\User::factory(10)->create();
         \App\Models\TimeEntry::factory(5)->create();
        \App\Models\User::factory()->create([
             'name' => 'Test',
            'email' => 'test@example.com',
         ]);
    }
}
