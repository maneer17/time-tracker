<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\TimeEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
