<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Add uuid columns if they don't already exist (make idempotent)
        if (! Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable()->after('id');
            });
        }

        if (! Schema::hasColumn('sellers', 'uuid')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable()->after('id');
            });
        }

        if (! Schema::hasColumn('products', 'uuid')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable()->after('id');
            });
        }

        if (! Schema::hasColumn('reviews', 'uuid')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable()->after('id');
            });
        }

        // Fill existing rows with UUIDs
        foreach (DB::table('users')->select('id')->get() as $row) {
            DB::table('users')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        }

        foreach (DB::table('sellers')->select('id')->get() as $row) {
            DB::table('sellers')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        }

        foreach (DB::table('products')->select('id')->get() as $row) {
            DB::table('products')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        }

        foreach (DB::table('reviews')->select('id')->get() as $row) {
            DB::table('reviews')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        }

        // Make uuid columns not nullable after population and add unique constraints.
        if (Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable(false)->change();
            });
            // add unique index if not already present
            if (! $this->indexExists('users', 'uuid')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('uuid');
                });
            }
        }

        if (Schema::hasColumn('sellers', 'uuid')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable(false)->change();
            });
            if (! $this->indexExists('sellers', 'uuid')) {
                Schema::table('sellers', function (Blueprint $table) {
                    $table->unique('uuid');
                });
            }
        }

        if (Schema::hasColumn('products', 'uuid')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable(false)->change();
            });
            if (! $this->indexExists('products', 'uuid')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->unique('uuid');
                });
            }
        }

        if (Schema::hasColumn('reviews', 'uuid')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable(false)->change();
            });
            if (! $this->indexExists('reviews', 'uuid')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->unique('uuid');
                });
            }
        }
    }

    /**
     * Check whether an index on a column exists for the given table.
     * Supports PostgreSQL and MySQL.
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    private function indexExists(string $table, string $column): bool
    {
        $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        // SQLite does not expose INFORMATION_SCHEMA; avoid running index queries there.
        if ($driver === 'sqlite') {
            return false;
        }

        if ($driver === 'pgsql') {
            $rows = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexdef LIKE ?", [$table, "%($column)%"]);
            return count($rows) > 0;
        }

        // MySQL / MariaDB
        $rows = DB::select("SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?", [$table, $column]);
        return count($rows) > 0;
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
