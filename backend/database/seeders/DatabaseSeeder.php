<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{TimeEntry, User};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(TimeEntrySeeder::class);
        $this->call(ChannelSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(InvitationSeeder::class);
    
    }
}
