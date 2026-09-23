<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SupportService;

class AdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $status=$request->get('status');
        $type=$request->get('type');
        $department=$request->get('department');

        $query=DB::table('support_conversations as c')
            ->leftJoin('users as cu','cu.id','=','c.customer_id')
            ->leftJoin('vendors as v','v.id','=','c.vendor_id')
            ->leftJoin('users as vu','vu.id','=','v.user_id')
            ->leftJoin('users as a','a.id','=','c.assigned_to')
            ->select('c.*','cu.name as customer_name','cu.email as customer_email','v.business_name as vendor_name','vu.name as vendor_user_name','a.name as assigned_name')
            ->when($status,fn($q)=>$q->where('c.status',$status))
            ->when($type,fn($q)=>$q->where('c.type',$type))
            ->when($department,fn($q)=>$q->where('c.department',$department));

        $conversations=$query->latest('c.last_message_at')->paginate(30)->withQueryString();
        $stats=[
            'open'=>DB::table('support_conversations')->whereIn('status',['open','pending'])->count(),
            'ai'=>DB::table('support_conversations')->where('status','ai_handled')->count(),
            'vendor'=>DB::table('support_conversations')->where('type','vendor_support')->whereIn('status',['open','pending'])->count(),
            'customer'=>DB::table('support_conversations')->where('type','customer_support')->whereIn('status',['open','pending'])->count(),
        ];
        $staff=DB::table('users')->whereIn('role',['super_admin','admin','staff'])->where('status','active')->orderBy('name')->get(['id','name','role']);
        $channels=app(SupportService::class)->channels();

        return view('admin.support',compact('conversations','stats','staff','channels','status','type','department'));
    }

    public function show(Request $request, int $id, SupportService $support)
    {
        $conversation=DB::table('support_conversations as c')
            ->leftJoin('users as cu','cu.id','=','c.customer_id')
            ->leftJoin('vendors as v','v.id','=','c.vendor_id')
            ->leftJoin('users as vu','vu.id','=','v.user_id')
            ->leftJoin('users as a','a.id','=','c.assigned_to')
            ->where('c.id',$id)
            ->select('c.*','cu.name as customer_name','cu.email as customer_email','v.business_name as vendor_name','vu.name as vendor_user_name','a.name as assigned_name')
            ->firstOrFail();

        DB::table('support_messages')->where('conversation_id',$id)->whereIn('sender_role',['customer','vendor'])->update(['read_at'=>now()]);
        $messages=$support->messages($id,250);
        $staff=DB::table('users')->whereIn('role',['super_admin','admin','staff'])->where('status','active')->orderBy('name')->get(['id','name','role']);
        $channels=$support->channels();

        return view('admin.support-thread',compact('conversation','messages','staff','channels'));
    }

    public function message(Request $request, int $id, SupportService $support)
    {
        $data=$request->validate(['body'=>'required|string|max:8000']);
        $conversation=DB::table('support_conversations')->where('id',$id)->firstOrFail();
        $support->addMessage($id,$request->user()->id,'admin',$data['body']);
        DB::table('support_conversations')->where('id',$id)->update(['status'=>'pending','updated_at'=>now()]);
        return back();
    }

    public function status(Request $request, int $id)
    {
        $data=$request->validate(['status'=>'required|in:open,pending,closed,ai_handled','priority'=>'nullable|in:low,normal,high,urgent']);
        DB::table('support_conversations')->where('id',$id)->update([
            'status'=>$data['status'],
            'priority'=>$data['priority'] ?? 'normal',
            'updated_at'=>now(),
        ]);
        return back()->with('success','Support case updated.');
    }

    public function assign(Request $request, int $id)
    {
        $data=$request->validate(['assigned_to'=>'nullable|exists:users,id']);
        if($data['assigned_to'] && !DB::table('users')->where('id',$data['assigned_to'])->whereIn('role',['super_admin','admin','staff'])->exists()){
            abort(422,'Only admin/staff can be assigned.');
        }
        DB::table('support_conversations')->where('id',$id)->update(['assigned_to'=>$data['assigned_to'] ?? null,'updated_at'=>now()]);
        return back()->with('success','Assignment updated.');
    }

    public function settings(Request $request)
    {
        $data=$request->validate([
            'whatsapp'=>'nullable|string|max:120',
            'wechat'=>'nullable|string|max:190',
            'email'=>'required|email|max:190',
        ]);
        foreach(['whatsapp'=>'support_whatsapp','wechat'=>'support_wechat','email'=>'support_email'] as $input=>$key){
            DB::table('settings')->updateOrInsert(
                ['setting_key'=>$key],
                ['setting_value'=>$data[$input] ?? '','value_type'=>'string','is_public'=>1,'updated_at'=>now()]
            );
        }
        return back()->with('success','Support contact channels saved.');
    }
}
