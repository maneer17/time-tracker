<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Channel;
use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    $channels = Channel::all();
    $channels->each(function($channel) {
        $users = User::where('id', '!=', $channel->user_id)
                     ->inRandomOrder()
                     ->take(3)
                     ->get();

        $users->each(function($user) use ($channel) {
            Member::firstOrCreate([
                'user_id' => $user->id,
                'channel_id' => $channel->id
            ]);
        });
    });
}
}
