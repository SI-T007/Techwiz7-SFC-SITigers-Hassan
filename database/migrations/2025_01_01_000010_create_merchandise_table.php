<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates: merchandise (showcase only — no shopping cart/payment)
     */
    public function up(): void
    {
        Schema::create('merchandise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 500);
            $table->string('slug', 500)->unique();
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->decimal('price', 10, 2)->nullable();   // Display only — no payment processing
            $table->string('currency', 10)->default('USD');
            $table->string('purchase_url', 1000)->nullable(); // Affiliate or external link
            $table->string('brand', 255)->nullable();
            $table->json('tags')->nullable();               // ["Limited Edition","Pre-Order","Collectible"]
            $table->boolean('is_upcoming')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['available', 'out_of_stock', 'discontinued'])->default('available');
            $table->timestamps();

            $table->index('category_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_upcoming');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchandise');
    }
};
