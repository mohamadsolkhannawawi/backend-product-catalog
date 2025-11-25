<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration assumes MySQL/MariaDB and is intended for development environments.
        // It will: add temporary uuid columns, populate them, migrate FK references,
        // then swap integer PKs to UUID string PKs named user_id/seller_id/product_id/review_id.

        $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // 1) Add temp uuid columns
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_uuid', 36)->nullable()->unique()->after('id');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->string('seller_uuid', 36)->nullable()->unique()->after('id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('product_uuid', 36)->nullable()->unique()->after('id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('review_uuid', 36)->nullable()->unique()->after('id');
        });

        // 2) Backfill uuid values (use PHP-generated UUIDs to be portable)
        $users = DB::table('users')->select('id')->get();
        foreach ($users as $u) {
            DB::table('users')->where('id', $u->id)->update(['user_uuid' => (string) Str::uuid()]);
        }

        $sellers = DB::table('sellers')->select('id')->get();
        foreach ($sellers as $s) {
            DB::table('sellers')->where('id', $s->id)->update(['seller_uuid' => (string) Str::uuid()]);
        }

        $products = DB::table('products')->select('id')->get();
        foreach ($products as $p) {
            DB::table('products')->where('id', $p->id)->update(['product_uuid' => (string) Str::uuid()]);
        }

        $reviews = DB::table('reviews')->select('id')->get();
        foreach ($reviews as $r) {
            DB::table('reviews')->where('id', $r->id)->update(['review_uuid' => (string) Str::uuid()]);
        }

        // 3) Add new FK columns to referencing tables and populate via mapping
        // Sellers.user_id -> will point to users.user_uuid
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('user_id_new', 36)->nullable()->after('user_id');
        });

        $allSellers = DB::table('sellers')->get();
        foreach ($allSellers as $s) {
            $user = DB::table('users')->where('id', $s->user_id)->first();
            if ($user) {
                DB::table('sellers')->where('id', $s->id)->update(['user_id_new' => $user->user_uuid]);
            }
        }

        // Products.seller_id -> will point to sellers.seller_uuid
        Schema::table('products', function (Blueprint $table) {
            $table->string('seller_id_new', 36)->nullable()->after('seller_id');
        });

        $allProducts = DB::table('products')->get();
        foreach ($allProducts as $p) {
            $seller = DB::table('sellers')->where('id', $p->seller_id)->first();
            if ($seller) {
                DB::table('products')->where('id', $p->id)->update(['seller_id_new' => $seller->seller_uuid]);
            }
        }

        // Reviews.product_id -> will point to products.product_uuid
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('product_id_new', 36)->nullable()->after('product_id');
        });

        $allReviews = DB::table('reviews')->get();
        foreach ($allReviews as $r) {
            $product = DB::table('products')->where('id', $r->product_id)->first();
            if ($product) {
                DB::table('reviews')->where('id', $r->id)->update(['product_id_new' => $product->product_uuid]);
            }
        }

        // Sessions.user_id (if exists) -> map to users.user_uuid
        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->string('user_id_new', 36)->nullable()->after('user_id');
            });

            $allSessions = DB::table('sessions')->get();
            foreach ($allSessions as $s) {
                if ($s->user_id) {
                    $user = DB::table('users')->where('id', $s->user_id)->first();
                    if ($user) {
                        DB::table('sessions')->where('id', $s->id)->update(['user_id_new' => $user->user_uuid]);
                    }
                }
            }
        }

        // 4) Drop foreign keys referencing old integer PKs
        Schema::table('sellers', function (Blueprint $table) {
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) { /* ignore */ }
        });

        Schema::table('products', function (Blueprint $table) {
            try { $table->dropForeign(['seller_id']); } catch (\Throwable $e) { /* ignore */ }
        });

        Schema::table('reviews', function (Blueprint $table) {
            try { $table->dropForeign(['product_id']); } catch (\Throwable $e) { /* ignore */ }
        });

        if (Schema::hasTable('sessions')) {
            if ($driver === 'mysql') {
                Schema::table('sessions', function (Blueprint $table) {
                    try { $table->dropForeign(['user_id']); } catch (\Throwable $e) { /* ignore */ }
                });
            } elseif ($driver === 'pgsql') {
                // PostgreSQL: drop constraint safely if it exists
                DB::statement('ALTER TABLE sessions DROP CONSTRAINT IF EXISTS sessions_user_id_foreign');
            } else {
                // Other drivers (sqlite) - skip dropping via raw statement
            }
        }

        // 5) Replace columns: drop old integer FK columns and rename new ones
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->renameColumn('user_id_new', 'user_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('seller_id');
            $table->renameColumn('seller_id_new', 'seller_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('product_id');
            $table->renameColumn('product_id_new', 'product_id');
        });

        if (Schema::hasTable('sessions')) {
            // Dropping/renaming columns in SQLite can be problematic; only perform
            // drop+rename on MySQL/Postgres. For SQLite tests, skip this step.
            if (in_array($driver, ['mysql', 'pgsql'])) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->dropColumn('user_id');
                    $table->renameColumn('user_id_new', 'user_id');
                });
            } else {
                // For sqlite (tests), just rename new column if possible; otherwise keep both
                try {
                    Schema::table('sessions', function (Blueprint $table) {
                        if (Schema::hasColumn('sessions', 'user_id_new') && !Schema::hasColumn('sessions', 'user_id')) {
                            $table->renameColumn('user_id_new', 'user_id');
                        }
                    });
                } catch (\Throwable $e) {
                    // If rename not supported, leave columns as-is for tests
                }
            }
        }

        // 6) Swap primary keys: drop integer id and use uuid columns as PKs
        // Use driver-specific SQL because MySQL and PostgreSQL differ in DDL syntax.
        if ($driver === 'mysql') {
            // MySQL style
            DB::statement('ALTER TABLE users DROP PRIMARY KEY, DROP COLUMN id');
            DB::statement('ALTER TABLE users CHANGE COLUMN user_uuid user_id CHAR(36) NOT NULL, ADD PRIMARY KEY (user_id)');

            DB::statement('ALTER TABLE sellers DROP PRIMARY KEY, DROP COLUMN id');
            DB::statement('ALTER TABLE sellers CHANGE COLUMN seller_uuid seller_id CHAR(36) NOT NULL, ADD PRIMARY KEY (seller_id)');

            DB::statement('ALTER TABLE products DROP PRIMARY KEY, DROP COLUMN id');
            DB::statement('ALTER TABLE products CHANGE COLUMN product_uuid product_id CHAR(36) NOT NULL, ADD PRIMARY KEY (product_id)');

            DB::statement('ALTER TABLE reviews DROP PRIMARY KEY, DROP COLUMN id');
            DB::statement('ALTER TABLE reviews CHANGE COLUMN review_uuid review_id CHAR(36) NOT NULL, ADD PRIMARY KEY (review_id)');
        } else {
            // PostgreSQL style: drop pkey constraint, drop integer id, rename uuid column, set not null, add primary key
            // users
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_pkey');
            DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS id');
            DB::statement('ALTER TABLE users RENAME COLUMN user_uuid TO user_id');
            DB::statement('ALTER TABLE users ALTER COLUMN user_id SET NOT NULL');
            DB::statement('ALTER TABLE users ADD PRIMARY KEY (user_id)');

            // sellers
            DB::statement('ALTER TABLE sellers DROP CONSTRAINT IF EXISTS sellers_pkey');
            DB::statement('ALTER TABLE sellers DROP COLUMN IF EXISTS id');
            DB::statement('ALTER TABLE sellers RENAME COLUMN seller_uuid TO seller_id');
            DB::statement('ALTER TABLE sellers ALTER COLUMN seller_id SET NOT NULL');
            DB::statement('ALTER TABLE sellers ADD PRIMARY KEY (seller_id)');

            // products
            DB::statement('ALTER TABLE products DROP CONSTRAINT IF EXISTS products_pkey');
            DB::statement('ALTER TABLE products DROP COLUMN IF EXISTS id');
            DB::statement('ALTER TABLE products RENAME COLUMN product_uuid TO product_id');
            DB::statement('ALTER TABLE products ALTER COLUMN product_id SET NOT NULL');
            DB::statement('ALTER TABLE products ADD PRIMARY KEY (product_id)');

            // reviews
            DB::statement('ALTER TABLE reviews DROP CONSTRAINT IF EXISTS reviews_pkey');
            DB::statement('ALTER TABLE reviews DROP COLUMN IF EXISTS id');
            DB::statement('ALTER TABLE reviews RENAME COLUMN review_uuid TO review_id');
            DB::statement('ALTER TABLE reviews ALTER COLUMN review_id SET NOT NULL');
            DB::statement('ALTER TABLE reviews ADD PRIMARY KEY (review_id)');
        }

        // 7) Recreate foreign key constraints
        Schema::table('sellers', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('seller_id')->references('seller_id')->on('sellers')->cascadeOnDelete();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
        });

        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
            });
        }

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this operation in a general way is risky; in dev you can rollback manually if needed.
    }
};
