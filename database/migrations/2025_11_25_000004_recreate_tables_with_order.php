<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if ($driver !== 'pgsql') {
            // This migration is written for PostgreSQL table recreation.
            return;
        }

        DB::beginTransaction();
        try {
            // 0) Drop foreign keys that reference the tables we will recreate
            DB::statement('ALTER TABLE IF EXISTS products DROP CONSTRAINT IF EXISTS products_seller_id_foreign');
            DB::statement('ALTER TABLE IF EXISTS reviews DROP CONSTRAINT IF EXISTS reviews_product_id_foreign');
            // Only drop reviews.user_id_foreign if it exists (reviews may be anonymous)
            if (Schema::hasColumn('reviews', 'user_id')) {
                DB::statement('ALTER TABLE IF EXISTS reviews DROP CONSTRAINT IF EXISTS reviews_user_id_foreign');
            }
            DB::statement('ALTER TABLE IF EXISTS sellers DROP CONSTRAINT IF EXISTS sellers_user_id_foreign');
            DB::statement('ALTER TABLE IF EXISTS sessions DROP CONSTRAINT IF EXISTS sessions_user_id_foreign');

            // 1) Create temp_users with desired column order: primary key first
            DB::statement(<<<'SQL'
CREATE TABLE temp_users (
  user_id CHAR(36) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NULL,
  phone VARCHAR(255) NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
SQL
            );
            // copy data
            DB::statement('INSERT INTO temp_users (user_id,name,email,email_verified_at,password,role,phone,remember_token,created_at,updated_at) SELECT user_id,name,email,email_verified_at,password,role,phone,remember_token,created_at,updated_at FROM users');

            // 2) Create temp_sellers with primary key first, then foreign key user_id
            DB::statement(<<<'SQL'
CREATE TABLE temp_sellers (
  seller_id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  store_name VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NULL,
  ktp_number VARCHAR(32) NULL,
  ktp_file_path VARCHAR(255) NULL,
  pic_file_path VARCHAR(255) NULL,
  province_id VARCHAR(10) NULL,
  city_id VARCHAR(10) NULL,
  district_id VARCHAR(10) NULL,
  village_id VARCHAR(10) NULL,
  address TEXT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  store_description TEXT NULL,
  rt VARCHAR(10) NULL,
  rw VARCHAR(10) NULL,
  rejection_reason TEXT NULL,
  verified_at TIMESTAMP NULL,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
SQL
            );
            DB::statement('INSERT INTO temp_sellers (seller_id,user_id,store_name,phone,ktp_number,ktp_file_path,pic_file_path,province_id,city_id,district_id,village_id,address,status,store_description,rt,rw,rejection_reason,verified_at,is_active,created_at,updated_at) SELECT seller_id,user_id,store_name,phone,ktp_number,ktp_file_path,pic_file_path,province_id,city_id,district_id,village_id,address,status,store_description,rt,rw,rejection_reason,verified_at,is_active,created_at,updated_at FROM sellers');

            // 3) Create temp_products with primary key first, then foreign key seller_id
            DB::statement(<<<'SQL'
CREATE TABLE temp_products (
  product_id CHAR(36) PRIMARY KEY,
  seller_id CHAR(36) NOT NULL,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  description TEXT NULL,
  price NUMERIC(12,2) DEFAULT 0,
  stock INTEGER DEFAULT 0,
  category VARCHAR(255) NULL,
  images JSON NULL,
  is_active BOOLEAN DEFAULT true,
  visitor BIGINT DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
SQL
            );
            DB::statement('INSERT INTO temp_products (product_id,seller_id,name,slug,description,price,stock,category,images,is_active,visitor,created_at,updated_at) SELECT product_id,seller_id,name,slug,description,price,stock,category,images,is_active,visitor,created_at,updated_at FROM products');

            // 4) Create temp_reviews with primary key first, then foreign key product_id
            // Note: this project uses public anonymous reviews (no user_id) so include name/email/province_id
            DB::statement(<<<'SQL'
CREATE TABLE temp_reviews (
  review_id CHAR(36) PRIMARY KEY,
  product_id CHAR(36) NOT NULL,
  rating SMALLINT NOT NULL,
  comment TEXT NULL,
  name VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  province_id VARCHAR(10) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
SQL
            );
            DB::statement('INSERT INTO temp_reviews (review_id,product_id,rating,comment,name,email,province_id,created_at,updated_at) SELECT review_id,product_id,rating,comment,name,email,province_id,created_at,updated_at FROM reviews');

            // 5) Drop old tables
            DB::statement('DROP TABLE users CASCADE');
            DB::statement('DROP TABLE sellers CASCADE');
            DB::statement('DROP TABLE products CASCADE');
            DB::statement('DROP TABLE reviews CASCADE');

            // 6) Rename temps to original names
            DB::statement('ALTER TABLE temp_users RENAME TO users');
            DB::statement('ALTER TABLE temp_sellers RENAME TO sellers');
            DB::statement('ALTER TABLE temp_products RENAME TO products');
            DB::statement('ALTER TABLE temp_reviews RENAME TO reviews');

            // 7) Recreate constraints and indexes
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_email_unique UNIQUE (email)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT products_slug_unique UNIQUE (slug)');

            // foreign keys
            DB::statement('ALTER TABLE sellers ADD CONSTRAINT sellers_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE');
            DB::statement('ALTER TABLE products ADD CONSTRAINT products_seller_id_foreign FOREIGN KEY (seller_id) REFERENCES sellers(seller_id) ON DELETE CASCADE');
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE');
            // add reviews.user_id FK only if column exists
            if (Schema::hasColumn('reviews', 'user_id')) {
              DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE');
            }

            // sessions FK if table exists
            if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
                DB::statement('ALTER TABLE sessions ADD CONSTRAINT sessions_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function down(): void
    {
        // Reverting this automated re-create is risky; do manually if needed.
    }
};
