<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  if(!Schema::hasTable('countries')){
   Schema::create('countries',function(Blueprint $t){$t->id();$t->string('code',2)->unique();$t->string('name',100);$t->string('flag',10)->nullable();$t->char('currency_code',3)->default('USD');$t->boolean('active')->default(true);$t->unsignedInteger('sort_order')->default(0);$t->timestamps();$t->index(['active','sort_order']);});
  }
  if(!Schema::hasColumn('users','country_id')) Schema::table('users',fn(Blueprint $t)=>$t->foreignId('country_id')->nullable()->after('locale')->constrained('countries')->nullOnDelete());
  if(!Schema::hasColumn('orders','country_id')) Schema::table('orders',fn(Blueprint $t)=>$t->foreignId('country_id')->nullable()->after('user_id')->constrained('countries')->nullOnDelete());
  if(!Schema::hasColumn('products','country_id')) Schema::table('products',fn(Blueprint $t)=>$t->foreignId('country_id')->nullable()->after('vendor_id')->constrained('countries')->nullOnDelete());
  if(!Schema::hasTable('product_marketplaces')){
   Schema::create('product_marketplaces',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained('products')->cascadeOnDelete();$t->foreignId('country_id')->constrained('countries')->cascadeOnDelete();$t->char('currency',3)->nullable();$t->decimal('retail_price',18,2)->nullable();$t->decimal('wholesale_price',18,2)->nullable();$t->decimal('shipping_price',18,2)->default(0);$t->decimal('tax_rate',8,4)->default(0);$t->boolean('active')->default(true);$t->unsignedDecimal('stock',18,3)->nullable();$t->timestamps();$t->unique(['product_id','country_id']);$t->index(['country_id','active']);});
  }
  if(Schema::hasTable('countries')){
   $rows=[
    ['PK','Pakistan','🇵🇰','PKR',1],['CN','China','🇨🇳','CNY',2],['AE','United Arab Emirates','🇦🇪','AED',3],['SA','Saudi Arabia','🇸🇦','SAR',4],['US','United States','🇺🇸','USD',5],['GB','United Kingdom','🇬🇧','GBP',6],['CA','Canada','🇨🇦','CAD',7],['AU','Australia','🇦🇺','AUD',8],['DE','Germany','🇩🇪','EUR',9],['FR','France','🇫🇷','EUR',10],['IT','Italy','🇮🇹','EUR',11],['ES','Spain','🇪🇸','EUR',12],['TR','Türkiye','🇹🇷','TRY',13],['MY','Malaysia','🇲🇾','MYR',14],['SG','Singapore','🇸🇬','SGD',15],['JP','Japan','🇯🇵','JPY',16],['KR','South Korea','🇰🇷','KRW',17],['IN','India','🇮🇳','INR',18],['BD','Bangladesh','🇧🇩','BDT',19],['QA','Qatar','🇶🇦','QAR',20],['KW','Kuwait','🇰🇼','KWD',21],['OM','Oman','🇴🇲','OMR',22],['BH','Bahrain','🇧🇭','BHD',23],['ZA','South Africa','🇿🇦','ZAR',24],['NL','Netherlands','🇳🇱','EUR',25],['BE','Belgium','🇧🇪','EUR',26],['AT','Austria','🇦🇹','EUR',27],['SE','Sweden','🇸🇪','SEK',28],['NO','Norway','🇳🇴','NOK',29],['DK','Denmark','🇩🇰','DKK',30]
   ];
   foreach($rows as [$code,$name,$flag,$currency,$sort]) DB::table('countries')->updateOrInsert(['code'=>$code],['name'=>$name,'flag'=>$flag,'currency_code'=>$currency,'active'=>1,'sort_order'=>$sort,'updated_at'=>now(),'created_at'=>now()]);
  }
 }
 public function down(): void {
  if(Schema::hasTable('product_marketplaces')) Schema::drop('product_marketplaces');
  if(Schema::hasColumn('orders','country_id')) Schema::table('orders',fn(Blueprint $t)=>$t->dropConstrainedForeignId('country_id'));
  if(Schema::hasColumn('products','country_id')) Schema::table('products',fn(Blueprint $t)=>$t->dropConstrainedForeignId('country_id'));
  if(Schema::hasColumn('users','country_id')) Schema::table('users',fn(Blueprint $t)=>$t->dropConstrainedForeignId('country_id'));
  Schema::dropIfExists('countries');
 }
};
