<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  if (Schema::hasTable('return_requests')) {
   Schema::table('return_requests', function(Blueprint $table){
    if (!Schema::hasColumn('return_requests','evidence_images')) $table->json('evidence_images')->nullable()->after('details');
    if (!Schema::hasColumn('return_requests','evidence_videos')) $table->json('evidence_videos')->nullable()->after('evidence_images');
    if (!Schema::hasColumn('return_requests','return_deadline_at')) $table->dateTime('return_deadline_at')->nullable()->after('evidence_videos');
    if (!Schema::hasColumn('return_requests','delivered_at')) $table->dateTime('delivered_at')->nullable()->after('return_deadline_at');
   });
  }
 }
 public function down(): void {
  if (Schema::hasTable('return_requests')) {
   Schema::table('return_requests', function(Blueprint $table){
    $columns=[];
    foreach(['evidence_images','evidence_videos','return_deadline_at','delivered_at'] as $column){ if (Schema::hasColumn('return_requests',$column)) $columns[]=$column; }
    if($columns) $table->dropColumn($columns);
   });
  }
 }
};