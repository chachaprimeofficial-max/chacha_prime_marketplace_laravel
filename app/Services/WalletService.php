<?php
namespace AppServices;
use AppModelsWallet;
use IlluminateSupportFacadesDB;
use RuntimeException;
class WalletService {
 public function credit(Wallet $wallet,float $amount,string $description=''): void { DB::transaction(function() use($wallet,$amount,$description){$wallet->refresh();$wallet->balance=(float)$wallet->balance+$amount;$wallet->save();$wallet->transactions()->create(['type'=>'credit','amount'=>$amount,'balance_after'=>$wallet->balance,'description'=>$description]);});}
 public function debit(Wallet $wallet,float $amount,string $description=''): void { DB::transaction(function() use($wallet,$amount,$description){$wallet->refresh();if((float)$wallet->balance<$amount) throw new RuntimeException('Insufficient marketplace balance.');$wallet->balance=(float)$wallet->balance-$amount;$wallet->save();$wallet->transactions()->create(['type'=>'debit','amount'=>$amount,'balance_after'=>$wallet->balance,'description'=>$description]);});}
}