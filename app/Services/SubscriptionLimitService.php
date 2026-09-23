<?php
namespace AppServices;
use IlluminateSupportFacadesDB;
use IlluminateSupportCarbon;
use RuntimeException;

class SubscriptionLimitService
{
    public function planFor(int $userId): ?object
    {
        return DB::table('user_subscriptions as us')
            ->join('subscription_plans as sp','sp.id','=','us.plan_id')
            ->where('us.user_id',$userId)->where('us.status','active')->where('sp.status',1)
            ->where(function($q){$q->whereNull('us.starts_at')->orWhere('us.starts_at','<=',now());})
            ->where(function($q){$q->whereNull('us.ends_at')->orWhere('us.ends_at','>=',now());})
            ->select('sp.*','us.id as subscription_id','us.ends_at')->latest('us.id')->first();
    }
    public function usage(int $userId): object
    {
        $period=now()->format('Y-m');
        $row=DB::table('subscription_usage')->where('user_id',$userId)->where('period_key',$period)->first();
        if(!$row){DB::table('subscription_usage')->insert(['user_id'=>$userId,'period_key'=>$period,'created_at'=>now(),'updated_at'=>now()]);$row=DB::table('subscription_usage')->where('user_id',$userId)->where('period_key',$period)->first();}
        return $row;
    }
    public function assertWithin(int $userId,string $metric,int $increment=1): void
    {
        $plan=$this->planFor($userId);
        if(!$plan) throw new RuntimeException('No active subscription plan is assigned to this account.');
        $limit=$plan->{$metric.'_limit'} ?? null;
        if($limit===null) return;
        $used=(int)($this->usage($userId)->{$metric.'_used'} ?? 0);
        if($used+$increment>(int)$limit) throw new RuntimeException(ucwords(str_replace('_',' ',$metric)).' limit reached for your '.$plan->name.' plan.');
    }
    public function consume(int $userId,string $metric,int $increment=1): void
    {
        $this->assertWithin($userId,$metric,$increment);
        $period=now()->format('Y-m');
        DB::table('subscription_usage')->updateOrInsert(['user_id'=>$userId,'period_key'=>$period],['updated_at'=>now()]);
        DB::table('subscription_usage')->where('user_id',$userId)->where('period_key',$period)->increment($metric.'_used',$increment,['updated_at'=>now()]);
    }
    public function snapshot(int $userId): array
    {
        $plan=$this->planFor($userId); $usage=$this->usage($userId);
        return ['plan'=>$plan,'usage'=>$usage];
    }
}
