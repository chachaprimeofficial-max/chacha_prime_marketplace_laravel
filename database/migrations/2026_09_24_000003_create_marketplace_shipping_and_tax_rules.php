<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  if (!Schema::hasTable('shipping_zones')) Schema::create('shipping_zones', function(Blueprint $t){$t->id();$t->foreignId('country_id')->constrained('countries')->cascadeOnDelete();$t->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();$t->string('name',120);$t->boolean('active')->default(true);$t->timestamps();$t->index(['country_id','vendor_id','active']);});
  if (!Schema::hasTable('shipping_methods')) Schema::create('shipping_methods', function(Blueprint $t){$t->id();$t->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();$t->string('name',120);$t->string('code',60);$t->enum('rate_type',['flat','weight','free'])->default('flat');$t->decimal('base_rate',14,2)->default(0);$t->decimal('per_kg_rate',14,2)->default(0);$t->decimal('free_over',14,2)->nullable();$t->decimal('min_weight',12,3)->nullable();$t->decimal('max_weight',12,3)->nullable();$t->unsignedSmallInteger('min_days')->default(2);$t->unsignedSmallInteger('max_days')->default(7);$t->string('currency',10)->default('USD');$t->boolean('active')->default(true);$t->timestamps();$t->unique(['shipping_zone_id','code']);});
  if (!Schema::hasTable('tax_rules')) Schema::create('tax_rules', function(Blueprint $t){$t->id();$t->foreignId('country_id')->constrained('countries')->cascadeOnDelete();$t->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();$t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();$t->string('name',120);$t->decimal('rate',8,4)->default(0);$t->boolean('prices_include_tax')->default(false);$t->boolean('active')->default(true);$t->timestamps();$t->index(['country_id','vendor_id','category_id','active']);});
 }
 public function down(): void {Schema::dropIfExists('tax_rules');Schema::dropIfExists('shipping_methods');Schema::dropIfExists('shipping_zones');}
};