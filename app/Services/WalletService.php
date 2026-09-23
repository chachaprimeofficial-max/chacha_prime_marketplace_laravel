<?php
namespace App\Services;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use RuntimeException;
class WalletService {
 public function credit(Wallet $wallet,float $amount,string $description='',?string $referenceType=null,?int $referenceId=null): void {
  if($amount<=0) throw new RuntimeException('Credit amount must be positive.');
  DB::transaction(function()use($wallet,$amount,$description,$referenceType,$referenceId){
   $wallet->refresh(); $wallet->balance=(float)$wallet->balance+$amount; $wallet->save();
   $wallet->transactions()->create(['type'=>'credit','amount'=>$amount,'balance_after'=>$wallet->balance,'reference_type'=>$referenceType,'reference_id'=>$referenceId,'description'=>$description,'created_at'=>now()]);
  });
 }
 public function debit(Wallet $wallet,float $amount,string $description='',?string $referenceType=null,?int $referenceId=null): void {
  if($amount<=0) throw new RuntimeException('Debit amount must be positive.');
  DB::transaction(function()use($wallet,$amount,$description,$referenceType,$referenceId){
   $wallet->refresh(); if((float)$wallet->balance<$amount) throw new RuntimeException('Insufficient marketplace balance.');
   $wallet->balance=(float)$wallet->balance-$amount; $wallet->save();
   $wallet->transactions()->create(['type'=>'debit','amount'=>$amount,'balance_after'=>$wallet->balance,'reference_type'=>$referenceType,'reference_id'=>$referenceId,'description'=>$description,'created_at'=>now()]);
  });
 }
}