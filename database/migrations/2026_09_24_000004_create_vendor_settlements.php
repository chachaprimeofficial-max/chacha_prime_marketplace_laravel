<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  if(!Schema::hasTable('vendor_settlements')) Schema::create('vendor_settlements',function(Blueprint $t){
   $t->id();$t->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();$t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();$t->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
   $t->decimal('gross_amount',14,2)->default(0);$t->decimal('commission_amount',14,2)->default(0);$t->decimal('shipping_earnings',14,2)->default(0);$t->decimal('refund_amount',14,2)->default(0);$t->decimal('net_amount',14,2)->default(0);
   $t->string('currency',10)->default('USD');$t->enum('status',['pending','eligible','settled','refunded','cancelled'])->default('pending');$t->timestamp('eligible_at')->nullable();$t->timestamp('settled_at')->nullable();$t->timestamps();
   $t->unique('order_item_id');$t->index(['vendor_id','status','eligible_at']);$t->index(['order_id','vendor_id']);
  });
 }
 public function down():void{Schema::dropIfExists('vendor_settlements');}
};