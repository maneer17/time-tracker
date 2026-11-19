<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * The container runs `db:seed` on every start, so only seed a database that is still empty;
     * otherwise each restart adds more users and then fails on the duplicate test@example.com.
     */
    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        $this->call(UserSeeder::class);
        $this->call(TimeEntrySeeder::class);
        $this->call(ChannelSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(InvitationSeeder::class);
    }
}
