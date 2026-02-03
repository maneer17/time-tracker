<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TimeEntry;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        
        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);
        
        $users = User::all();
        
        for($i = 0; $i < 20; $i++){
            $user = $users->random();
            TimeEntry::factory()->create([
                'user_id' => $user->id
            ]);    
        }
        for($i = 0; $i < 10; $i++){
            $user = User::where('email','test@example.com')->first();
            TimeEntry::factory()->create([
                'user_id' => $user->id
            ]);}
    }
}