@extends('admin.layout')
@section('title','Vendor Payouts — Chacha Prime')
@section('page_heading','Vendor Payouts')
@section('content')
<div class="panel">
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="section-title" style="margin-top:0"><div><h2>Seller payout queue</h2><p>Reserved wallet amounts remain held until a payout is paid, rejected or cancelled.</p></div></div>
<div class="table-wrap"><table style="min-width:900px"><thead><tr><th>Vendor</th><th>Amount</th><th>Method</th><th>Destination</th><th>Status</th><th>Requested</th><th>Action</th></tr></thead><tbody>
@forelse($payouts as $p)<tr>
<td><strong>{{e($p->business_name)}}</strong><br><small>{{e($p->vendor_user_name)}}</small></td>
<td><strong>{{$p->currency}} {{number_format((float)$p->amount,2)}}</strong></td><td>{{e($p->method ?: '—')}}</td><td>{{e($p->destination ?: '—')}}</td><td><span class="status">{{$p->status}}</span></td><td>{{$p->created_at}}</td>
<td><form method="POST" action="{{route('admin.payouts.update',$p->id)}}" style="display:flex;gap:6px;flex-wrap:wrap">@csrf
@if(in_array($p->status,['requested','processing']))<select name="status" required><option value="processing">Processing</option><option value="paid">Paid</option><option value="rejected">Reject & refund</option><option value="cancelled">Cancel & refund</option></select><input name="notes" placeholder="Optional note"><button class="button button-dark" type="submit">Update</button>@else<span style="color:#64748b;font-size:11px">Finalized</span>@endif
</form></td></tr>
@empty<tr><td colspan="7">No payout requests.</td></tr>@endforelse
</tbody></table></div>{{$payouts->links()}}</div>
@endsection