<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Channel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        Channel::factory(20)->create([
            'user_id' => fn() => $users->random()->id
        ]);
}
}
