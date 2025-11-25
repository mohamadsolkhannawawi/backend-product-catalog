<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create unique indexes for seller-related fields (idempotent)
        // Will try database-native statements and fall back to safe attempts.
        if (! Schema::hasTable('sellers')) {
            return;
        }

        $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);

        // Determine column names present for KTP (some migrations used nid_number)
        $ktpCol = Schema::hasColumn('sellers', 'ktp_number') ? 'ktp_number' : (Schema::hasColumn('sellers', 'nid_number') ? 'nid_number' : null);

        // store_name
        try {
            if ($driver === 'pgsql') {
                DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS sellers_store_name_unique ON sellers (store_name);");
            } else {
                // MySQL/others: attempt to add unique index, ignore if exists
                DB::statement("ALTER TABLE sellers ADD UNIQUE KEY sellers_store_name_unique (store_name);");
            }
        } catch (\Throwable $e) {
            // ignore (index may already exist or DB doesn't support IF NOT EXISTS)
        }

        // phone: prefer sellers.phone, otherwise enforce users.phone if present
        try {
            if (Schema::hasColumn('sellers', 'phone')) {
                if ($driver === 'pgsql') {
                    DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS sellers_phone_unique ON sellers (phone);");
                } else {
                    DB::statement("ALTER TABLE sellers ADD UNIQUE KEY sellers_phone_unique (phone);");
                }
            }
        } catch (\Throwable $e) {
        }

        // KTP number
        if ($ktpCol) {
            try {
                if ($driver === 'pgsql') {
                    DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS sellers_ktp_unique ON sellers ({$ktpCol});");
                } else {
                    DB::statement("ALTER TABLE sellers ADD UNIQUE KEY sellers_ktp_unique ({$ktpCol});");
                }
            } catch (\Throwable $e) {
            }
        }

        // Also ensure users.phone is unique if present (we store PIC phone on users table)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phone')) {
            try {
                if ($driver === 'pgsql') {
                    DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS users_phone_unique ON users (phone);");
                } else {
                    DB::statement("ALTER TABLE users ADD UNIQUE KEY users_phone_unique (phone);");
                }
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('sellers')) {
            return;
        }

        $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);

        try {
            if ($driver === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS sellers_store_name_unique');
                DB::statement('DROP INDEX IF EXISTS sellers_phone_unique');
                DB::statement('DROP INDEX IF EXISTS sellers_ktp_unique');
                DB::statement('DROP INDEX IF EXISTS users_phone_unique');
            } else {
                DB::statement('ALTER TABLE sellers DROP INDEX IF EXISTS sellers_store_name_unique');
                DB::statement('ALTER TABLE sellers DROP INDEX IF EXISTS sellers_phone_unique');
                DB::statement('ALTER TABLE sellers DROP INDEX IF EXISTS sellers_ktp_unique');
                DB::statement('ALTER TABLE users DROP INDEX IF EXISTS users_phone_unique');
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
