<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('country_codes');
            $table->string('locale')->default('en-GB');
            $table->string('currency')->default('GBP');
            $table->string('host')->default('hempehelps.com');
            $table->string('path_prefix')->nullable();
            $table->string('shopify_country')->default('GB');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('shopify_products', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_id')->unique();
            $table->string('handle')->unique();
            $table->string('title');
            $table->string('status')->default('active');
            $table->string('product_type')->nullable();
            $table->string('vendor')->nullable();
            $table->json('tags')->nullable();
            $table->string('featured_image_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shopify_variants', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_id')->unique();
            $table->foreignId('product_id')->constrained('shopify_products')->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->json('option_values')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('shopify_collections', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_id')->unique();
            $table->string('handle')->unique();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('collection_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('shopify_collections')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('shopify_products')->cascadeOnDelete();
            $table->unique(['collection_id', 'product_id']);
        });

        Schema::create('product_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopify_product_id')->constrained('shopify_products')->cascadeOnDelete();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->json('blocks')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path');
            $table->string('to_url');
            $table->unsignedSmallInteger('status')->default(301);
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->timestamps();
            $table->unique(['from_path', 'market_id']);
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('title');
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['slug', 'market_id']);
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['slug', 'market_id']);
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('position')->default(0);
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopify_product_id')->constrained('shopify_products')->cascadeOnDelete();
            $table->foreignId('shopify_variant_id')->nullable()->constrained('shopify_variants')->nullOnDelete();
            $table->string('author_name');
            $table->string('author_email')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('body');
            $table->json('photos')->nullable();
            $table->string('source')->default('admin');
            $table->string('external_id')->nullable()->unique();
            $table->boolean('verified_purchase')->default(false);
            $table->string('status')->default('published');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->timestamps();
            $table->index('shopify_product_id');
        });

        Schema::create('review_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopify_product_id')->unique()->constrained('shopify_products')->cascadeOnDelete();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->timestamps();
        });

        Schema::create('magic_link_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('topic');
            $table->string('external_id')->nullable();
            $table->string('payload_hash')->unique();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('magic_link_tokens');
        Schema::dropIfExists('review_summaries');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('product_contents');
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('shopify_collections');
        Schema::dropIfExists('shopify_variants');
        Schema::dropIfExists('shopify_products');
        Schema::dropIfExists('markets');
    }
};
