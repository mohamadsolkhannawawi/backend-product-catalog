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
            // Only run this on PostgreSQL for now
            return;
        }

        DB::beginTransaction();
        try {
            // Drop FK to allow drop/rename
            DB::statement('ALTER TABLE IF EXISTS reviews DROP CONSTRAINT IF EXISTS reviews_product_id_foreign');

            // Create temporary table with desired column order: review_id, product_id, rating, comment, name, email, phone, province_id, created_at, updated_at
            DB::statement(<<<'SQL'
CREATE TABLE temp_reviews_reorder (
  review_id CHAR(36) PRIMARY KEY,
  product_id CHAR(36) NOT NULL,
  rating SMALLINT NOT NULL,
  comment TEXT NULL,
  name VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  phone VARCHAR(32) NULL,
  province_id VARCHAR(10) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
SQL
            );

            // Copy data from old reviews into new table, matching columns
            DB::statement('INSERT INTO temp_reviews_reorder (review_id,product_id,rating,comment,name,email,phone,province_id,created_at,updated_at) SELECT review_id,product_id,rating,comment,name,email,phone,province_id,created_at,updated_at FROM reviews');

            // Drop old table and rename
            DB::statement('DROP TABLE reviews CASCADE');
            DB::statement('ALTER TABLE temp_reviews_reorder RENAME TO reviews');

            // Recreate indexes and foreign keys
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE');

            // Recreate unique indexes if they exist previously (IF NOT EXISTS)
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS reviews_product_email_unique ON reviews (product_id, email)");
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS reviews_product_phone_unique ON reviews (product_id, phone)");

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function down(): void
    {
        // Reverting column order is risky; do manually if needed.
    }
};
