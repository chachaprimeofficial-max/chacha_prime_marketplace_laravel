<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SupportService;

class SupportController extends Controller
{
    public function customer(Request $request)
    {
        $user = $request->user();
        $conversationId = $request->integer('conversation');
        $conversation = $conversationId
            ? DB::table('support_conversations')->where('id',$conversationId)->where('customer_id',$user->id)->first()
            : DB::table('support_conversations')->where('customer_id',$user->id)->latest('last_message_at')->first();

        $conversations = DB::table('support_conversations')
            ->where('customer_id',$user->id)
            ->latest('last_message_at')->limit(30)->get();

        $messages = $conversation ? app(SupportService::class)->messages((int)$conversation->id,120) : collect();
        $channels = app(SupportService::class)->channels();

        return view('customer.support', compact('conversation','conversations','messages','channels'));
    }

    public function startCustomerSupport(Request $request, SupportService $support)
    {
        $data = $request->validate(['subject'=>'nullable|string|max:190','message'=>'required|string|max:5000']);
        $id = $support->createConversation([
            'type'=>'customer_support',
            'customer_id'=>$request->user()->id,
            'subject'=>$data['subject'] ?: 'Customer Support',
            'department'=>'customer_support',
            'status'=>'open',
            'priority'=>'normal',
            'ai_handled'=>0,
        ]);
        $support->addMessage($id,$request->user()->id,'customer',$data['message']);
        return redirect()->route('customer.support',['conversation'=>$id])->with('success','Support case opened.');
    }

    public function customerMessage(Request $request, int $id, SupportService $support)
    {
        $data=$request->validate(['body'=>'required|string|max:5000']);
        $conversation=DB::table('support_conversations')->where('id',$id)->where('customer_id',$request->user()->id)->firstOrFail();
        abort_if($conversation->status==='closed',422,'This conversation is closed. Open a new support case.');
        $support->addMessage($id,$request->user()->id,'customer',$data['body']);
        DB::table('support_conversations')->where('id',$id)->update(['status'=>'open','updated_at'=>now()]);
        return back();
    }

    public function startVendorConversation(Request $request, int $vendorId, SupportService $support)
    {
        $data=$request->validate(['subject'=>'required|string|max:190','message'=>'required|string|max:5000','order_id'=>'nullable|integer']);
        $vendor=Vendor::findOrFail($vendorId);
        $orderId=$data['order_id'] ?? null;
        if($orderId){
            $valid=DB::table('order_items')->where('order_id',$orderId)->where('vendor_id',$vendor->id)
                ->whereExists(fn($q)=>$q->from('orders')->whereColumn('orders.id','order_items.order_id')->where('orders.user_id',$request->user()->id))->exists();
            abort_unless($valid,403);
        }
        $id=$support->openCustomerVendor($request->user()->id,$vendor->id,$orderId,$data['subject']);
        $support->addMessage($id,$request->user()->id,'customer',$data['message']);
        return redirect()->route('customer.support',['conversation'=>$id])->with('success','Seller message sent.');
    }

    public function vendorMessages(Request $request, SupportService $support)
    {
        $vendor=Vendor::where('user_id',$request->user()->id)->firstOrFail();
        $supportConversation=$support->conversationForVendor($vendor->id);
        $supportMessages=$supportConversation ? $support->messages((int)$supportConversation->id,120) : collect();

        $customerConversations=DB::table('support_conversations as c')
            ->leftJoin('users as u','u.id','=','c.customer_id')
            ->where('c.vendor_id',$vendor->id)->where('c.type','customer_vendor')
            ->select('c.*','u.name as customer_name','u.email as customer_email')
            ->latest('c.last_message_at')->limit(50)->get();

        $selectedId=$request->integer('customer_conversation');
        $customerConversation=$selectedId
            ? DB::table('support_conversations')->where('id',$selectedId)->where('vendor_id',$vendor->id)->where('type','customer_vendor')->first()
            : $customerConversations->first();
        $customerMessages=$customerConversation ? $support->messages((int)$customerConversation->id,120) : collect();

        return view('vendor.messages',compact('vendor','supportConversation','supportMessages','customerConversations','customerConversation','customerMessages'));
    }

    public function vendorSupportStart(Request $request, SupportService $support)
    {
        $data=$request->validate(['subject'=>'required|string|max:190','message'=>'required|string|max:5000']);
        $vendor=Vendor::where('user_id',$request->user()->id)->firstOrFail();
        $id=$support->createConversation([
            'type'=>'vendor_support','vendor_id'=>$vendor->id,'subject'=>$data['subject'],
            'department'=>'vendor_support','status'=>'open','priority'=>'normal','ai_handled'=>0,
        ]);
        $support->addMessage($id,$request->user()->id,'vendor',$data['message']);
        return redirect()->route('vendor.messages')->with('success','Support case opened with Chacha Prime.');
    }

    public function vendorSupportMessage(Request $request, int $id, SupportService $support)
    {
        $data=$request->validate(['body'=>'required|string|max:5000']);
        $vendor=Vendor::where('user_id',$request->user()->id)->firstOrFail();
        $conversation=DB::table('support_conversations')->where('id',$id)->where('vendor_id',$vendor->id)->where('type','vendor_support')->firstOrFail();
        abort_if($conversation->status==='closed',422,'This support case is closed.');
        $support->addMessage($id,$request->user()->id,'vendor',$data['body']);
        DB::table('support_conversations')->where('id',$id)->update(['status'=>'open','updated_at'=>now()]);
        return back();
    }

    public function vendorCustomerMessage(Request $request, int $id, SupportService $support)
    {
        $data=$request->validate(['body'=>'required|string|max:5000']);
        $vendor=Vendor::where('user_id',$request->user()->id)->firstOrFail();
        $conversation=DB::table('support_conversations')->where('id',$id)->where('vendor_id',$vendor->id)->where('type','customer_vendor')->firstOrFail();
        abort_if($conversation->status==='closed',422,'This conversation is closed.');
        $support->addMessage($id,$request->user()->id,'vendor',$data['body']);
        DB::table('support_conversations')->where('id',$id)->update(['status'=>'open','updated_at'=>now()]);
        return back()->with('customer_conversation',$id);
    }
}
