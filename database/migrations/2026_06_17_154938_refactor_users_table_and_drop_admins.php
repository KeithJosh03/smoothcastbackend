<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modify the 'users' table
        Schema::table('users', function (Blueprint $table) {
            // Make password nullable to allow social login (OAuth) users to register without a password
            $table->string('password')->nullable()->change();
            
            // Ensure the 'role_id' column exists and is constrained to the 'roles' table
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('password');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            }
        });

        // 3. Perform a safe data migration using the DB facade
        // Fetch or create the 'admin' role row from the 'roles' table
        $adminRole = DB::table('roles')->where('role_name', 'admin')->first();
        if (!$adminRole) {
            $adminRoleId = DB::table('roles')->insertGetId([
                'role_name' => 'admin',
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        // Loop through all existing records in the legacy 'admins' table
        $admins = DB::table('admins')->get();
        foreach ($admins as $admin) {
            // Check for email duplicates first to prevent constraint crashes
            $exists = DB::table('users')->where('email', $admin->email)->exists();
            if (!$exists) {
                // Insert admins into the 'users' table
                DB::table('users')->insert([
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'password' => $admin->password,
                    'role_id' => $adminRoleId,
                    'created_at' => $admin->created_at ?? now(),
                    'updated_at' => $admin->updated_at ?? now(),
                ]);
            } else {
                // If the user already exists, update their role_id to ensure they maintain admin access
                DB::table('users')->where('email', $admin->email)->update([
                    'role_id' => $adminRoleId
                ]);
            }
        }

        // 4. Drop the 'admins' table completely after data is successfully copied
        Schema::dropIfExists('admins');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-create the 'admins' table with its original schema
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // 2. Move users who have the 'admin' role back into the 'admins' table
        $adminRole = DB::table('roles')->where('role_name', 'admin')->first();
        if ($adminRole) {
            $adminUsers = DB::table('users')->where('role_id', $adminRole->id)->get();
            foreach ($adminUsers as $user) {
                DB::table('admins')->insert([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password ?? '',
                    'created_at' => $user->created_at ?? now(),
                    'updated_at' => $user->updated_at ?? now(),
                ]);
            }
            
            // Remove those moved users from the 'users' table
            DB::table('users')->where('role_id', $adminRole->id)->delete();
        }

        // 3. Revert the 'users' table changes
        Schema::table('users', function (Blueprint $table) {
            // Set password back to non-nullable (first fill any nulls with empty string to prevent constraint violation)
            DB::table('users')->whereNull('password')->update(['password' => '']);
            $table->string('password')->nullable(false)->change();

        });
    }
};
