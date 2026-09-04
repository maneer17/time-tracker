<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Enums\OrganizationRole;

return new class extends Migration
{
    public function up(): void
    {
        $orgId = DB::table('organizations')->insertGetId([ //this method insert a row and it returns the incremented new id value
                'name'        => " Default Organization",
                'description' => null,
                'settings'    => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        foreach (DB::table('users')->get() as $user) {
            // 2. membership — they own their personal org
            DB::table('organization_user')->insert([
                'user_id'         => $user->id,
                'organization_id' => $orgId,
                'role'            => OrganizationRole::Owner->value,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. stamp their existing data with this org (raw, fast)
            DB::table('time_entries')->where('user_id', $user->id)->update(['organization_id' => $orgId]);
            DB::table('channels')->where('user_id', $user->id)->update(['organization_id' => $orgId]);
            DB::table('import_batches')->where('user_id', $user->id)->update(['organization_id' => $orgId]);
        }
    }

    public function down(): void
    {
        // Null the foreign keys, then remove the personal orgs + memberships.
        DB::table('time_entries')->update(['organization_id' => null]);
        DB::table('channels')->update(['organization_id' => null]);
        DB::table('import_batches')->update(['organization_id' => null]);
        DB::table('organization_user')->delete();
        DB::table('organizations')->delete();
    }
};