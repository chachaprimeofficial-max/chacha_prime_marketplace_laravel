@extends('vendor.layout')
@section('title','Seller Center — Chacha Prime')
@push('styles')
<style>
.cp-snapshot{display:grid;grid-template-columns:1fr 1fr;gap:9px}.cp-snapshot div{padding:13px;border:1px solid #e5e7eb;border-radius:11px}.cp-snapshot span{display:block;color:#64748b;font-size:11px}.cp-snapshot b{display:block;font-size:18px;margin-top:5px}
/* Chacha Prime — premium seller center */
 .cp-seller-page{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}
 .cp-seller-page-inner{max-width:1500px;margin:0 auto}
.cp-seller-sidebar{background:#111827;color:#fff;border-radius:18px;padding:16px;position:sticky;top:124px;height:max-content;box-shadow:0 12px 35px #11182718}
.cp-seller-brand{padding:12px 12px 18px;border-bottom:1px solid #ffffff18;margin-bottom:10px}
.cp-seller-brand small{display:block;color:#94a3b8;font-size:10px;font-weight:800;letter-spacing:1.6px}
.cp-seller-brand strong{display:block;font-size:19px;margin-top:5px}
.cp-seller-nav{display:grid;gap:4px}
.cp-seller-nav a{display:flex;align-items:center;gap:10px;color:#cbd5e1;text-decoration:none;padding:10px 12px;border-radius:10px;font-size:13px;font-weight:700}
.cp-seller-nav a:hover,.cp-seller-nav a.is-active{background:#ffffff12;color:#fff}
.cp-seller-nav a.is-active{box-shadow:inset 3px 0 #f59e0b}
.cp-seller-sidebar .cp-seller-help{margin-top:16px;padding:13px;background:#ffffff0b;border:1px solid #ffffff12;border-radius:12px;color:#cbd5e1;font-size:12px;line-height:1.5}
.cp-seller-sidebar .cp-seller-help a{color:#fbbf24;text-decoration:none;font-weight:800}

.cp-seller-top{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:20px 22px;display:flex;justify-content:space-between;gap:18px;align-items:center;box-shadow:0 8px 25px #11182708}
.cp-seller-eyebrow{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#f59e0b}
.cp-seller-top h1{margin:6px 0 4px;font-size:30px;letter-spacing:-.8px;color:#111827}
.cp-seller-top p{margin:0;color:#64748b;font-size:13px}
.cp-seller-actions{display:flex;gap:9px;flex-wrap:wrap}
.cp-seller-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:10px;text-decoration:none;border:1px solid #dbe1e8;font-size:12px;font-weight:800;color:#1f2937;background:#fff}
.cp-seller-btn.primary{background:#f59e0b;border-color:#f59e0b;color:#111827}
.cp-seller-btn:hover{transform:translateY(-1px)}
.cp-seller-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-top:16px}
.cp-seller-stat{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:17px;box-shadow:0 8px 25px #11182706}
.cp-seller-stat .label{color:#64748b;font-size:11px;font-weight:700}
.cp-seller-stat strong{display:block;color:#111827;font-size:26px;margin-top:8px;letter-spacing:-.5px}
.cp-seller-stat small{display:block;color:#94a3b8;margin-top:5px;font-size:11px}
.cp-seller-layout{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(300px,.85fr);gap:16px;margin-top:16px}
.cp-seller-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:20px;box-shadow:0 8px 25px #11182706}
.cp-seller-card h2{margin:0;font-size:18px;color:#111827}
.cp-seller-card .sub{margin:5px 0 18px;color:#64748b;font-size:12px}
.cp-seller-actions-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.cp-seller-action{border:1px solid #e5e7eb;border-radius:13px;padding:14px;text-decoration:none;color:#111827;background:#fafbfc;transition:.18s}
.cp-seller-action:hover{border-color:#f59e0b;background:#fffbeb;transform:translateY(-2px)}
.cp-seller-action b{display:block;font-size:13px}.cp-seller-action span{display:block;color:#64748b;font-size:11px;margin-top:5px}
.cp-seller-form{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.cp-seller-form .full{grid-column:1/-1}
.cp-seller-form label{display:grid;gap:5px;font-size:11px;font-weight:800;color:#475569}
.cp-seller-form input,.cp-seller-form select,.cp-seller-form textarea{width:100%;border:1px solid #dbe1e8;border-radius:9px;padding:10px 11px;font:inherit;font-size:12px;outline:none;background:#fff}
.cp-seller-form input:focus,.cp-seller-form select:focus,.cp-seller-form textarea:focus{border-color:#f59e0b;box-shadow:0 0 0 3px #f59e0b1a}
.cp-seller-form textarea{min-height:78px;resize:vertical}
.cp-seller-submit{grid-column:1/-1;border:0;border-radius:10px;background:#111827;color:#fff;padding:11px 14px;font-weight:800;cursor:pointer}
.cp-seller-submit:hover{background:#273449}
.cp-seller-table{margin-top:16px}
.cp-seller-table-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.cp-seller-table-wrap{overflow:auto}
.cp-seller-table table{width:100%;border-collapse:collapse;min-width:650px}
.cp-seller-table th,.cp-seller-table td{padding:12px 10px;border-bottom:1px solid #eef0f3;text-align:left;font-size:12px}
.cp-seller-table th{color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.7px}
.cp-seller-status{display:inline-flex;padding:5px 8px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:10px;font-weight:800;text-transform:capitalize}
.cp-seller-status.published{background:#ecfdf3;color:#027a48}.cp-seller-status.pending{background:#fffaeb;color:#b54708}.cp-seller-status.archived{background:#fef3f2;color:#b42318}
.cp-seller-notice{margin:0 0 14px;padding:11px 13px;border-radius:10px;background:#ecfdf3;color:#027a48;border:1px solid #abefc6;font-size:12px;font-weight:700}
.cp-seller-error{margin:0 0 14px;padding:11px 13px;border-radius:10px;background:#fef3f2;color:#b42318;border:1px solid #fecdca;font-size:12px}
.cp-seller-bars{display:grid;gap:14px;margin-top:15px}.cp-seller-bar-row{display:grid;gap:7px}.cp-seller-bar-label{display:flex;justify-content:space-between;color:#475569;font-size:11px;font-weight:700}.cp-seller-bar{height:8px;background:#eef2f6;border-radius:999px;overflow:hidden}.cp-seller-bar i{display:block;height:100%;background:#f59e0b;border-radius:999px}
@media(max-width:1100px){.cp-seller-shell{grid-template-columns:1fr}.cp-seller-sidebar{position:relative;top:auto}.cp-seller-nav{display:flex;overflow:auto}.cp-seller-nav a{white-space:nowrap}.cp-seller-help{display:none}.cp-seller-stats{grid-template-columns:repeat(2,1fr)}.cp-seller-layout{grid-template-columns:1fr}}
@media(max-width:700px){.cp-seller-page{padding:12px}.cp-seller-top{align-items:flex-start;flex-direction:column}.cp-seller-top h1{font-size:25px}.cp-seller-stats{grid-template-columns:1fr 1fr}.cp-seller-actions-grid{grid-template-columns:1fr 1fr}.cp-seller-form{grid-template-columns:1fr}.cp-seller-form .full,.cp-seller-submit{grid-column:1}.cp-seller-card{padding:15px}.cp-seller-sidebar{padding:10px}.cp-seller-brand{display:none}}
@media(max-width:450px){.cp-seller-stats{grid-template-columns:1fr}.cp-seller-actions-grid{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<div class="cp-seller-page"><div class="cp-seller-page-inner"><section class="cp-seller-top">
        <div>
          <div class="cp-seller-eyebrow">SELLER CENTER</div>
          <h1>{{ $vendor->business_name }}</h1>
          <p>Manage your catalog, orders, pricing, live commerce and marketplace earnings from one place.</p>
        </div>
        <div class="cp-seller-actions">
          <a class="cp-seller-btn" href="{{route('home')}}">View Store</a>
          <a class="cp-seller-btn primary" href="#quick-add-product">+ Add Product</a>
        </div>
      </section>

      @if(session('success'))<div class="cp-seller-notice">{{session('success')}}</div>@endif
      @if($errors->any())<div class="cp-seller-error">{{implode(' ', $errors->all())}}</div>@endif

      <section class="cp-seller-stats">
        <div class="cp-seller-stat"><span class="label">Total Products</span><strong>{{number_format($stats['products'])}}</strong><small>Across your catalog</small></div>
        <div class="cp-seller-stat"><span class="label">Paid Sales</span><strong>{{number_format((float)$stats['sales'],2)}}</strong><small>Marketplace sales value</small></div>
        <div class="cp-seller-stat"><span class="label">Orders</span><strong>{{number_format($stats['orders'])}}</strong><small>Orders containing your items</small></div>
        <div class="cp-seller-stat"><span class="label">Low Stock</span><strong>{{number_format($stats['low_stock'])}}</strong><small>10 units or less</small></div>
        <div class="cp-seller-stat"><span class="label">Marketplace Reach</span><strong>{{number_format($stats['marketplace_listings'])}}</strong><small>Active country listings</small></div>
        <div class="cp-seller-stat"><span class="label">Open Returns</span><strong>{{number_format($stats['pending_returns'])}}</strong><small>Returns needing attention</small></div>
        <div class="cp-seller-stat"><span class="label">Messages</span><strong>{{number_format($stats['unread_messages'])}}</strong><small>Open customer conversations</small></div>
      </section>

      <section class="cp-seller-layout" style="margin-top:16px"><section class="cp-seller-card"><h2>Store performance</h2><p class="sub">Visual overview of your current catalog and operations.</p><div class="cp-seller-bars"><div class="cp-seller-bar-row"><div class="cp-seller-bar-label"><span>Published products</span><b>{{$stats['published']}} / {{$stats['products']}}</b></div><div class="cp-seller-bar"><i style="width:{{min(100,(int)$stats['products']>0?round($stats['published']/$stats['products']*100):0)}}%"></i></div></div><div class="cp-seller-bar-row"><div class="cp-seller-bar-label"><span>Healthy inventory</span><b>{{$stats['products']-$stats['low_stock']}} / {{$stats['products']}}</b></div><div class="cp-seller-bar"><i style="width:{{min(100,(int)$stats['products']>0?round(($stats['products']-$stats['low_stock'])/$stats['products']*100):0)}}%"></i></div></div><div class="cp-seller-bar-row"><div class="cp-seller-bar-label"><span>Orders</span><b>{{$stats['orders']}}</b></div><div class="cp-seller-bar"><i style="width:{{min(100,max(5,(int)$stats['orders']))}}%"></i></div></div></div></section><section class="cp-seller-card"><h2>Business snapshot</h2><p class="sub">Key numbers from your Seller Center.</p><div class="cp-snapshot"><div><span>Paid sales</span><b>{{$stats['sales']}}</b></div><div><span>Low stock</span><b>{{$stats['low_stock']}}</b></div><div><span>Orders</span><b>{{$stats['orders']}}</b></div><div><span>Products</span><b>{{$stats['products']}}</b></div></div></section></section><div class="cp-seller-layout">
        <section class="cp-seller-card">
          <h2>Seller operations</h2>
          <p class="sub">Everything you need to run your Chacha Prime store.</p>
          <div class="cp-seller-actions-grid">
            <a class="cp-seller-action" href="{{route('vendor.products')}}"><b>Catalog</b><span>Create, edit & publish products →</span></a>
            <a class="cp-seller-action" href="{{route('vendor.orders')}}"><b>Orders</b><span>Process customer orders →</span></a>
            <a class="cp-seller-action" href="{{route('vendor.inventory')}}"><b>Inventory</b><span>Monitor stock levels →</span></a>
            <a class="cp-seller-action" href="{{route('vendor.pricing')}}"><b>B2B / B2C</b><span>Set quantity pricing →</span></a>
            <a class="cp-seller-action" href="{{route('vendor.group-buying')}}"><b>Group Buying</b><span>Create bulk offers →</span></a>
            <a class="cp-seller-action" href="{{route('vendor.live-commerce')}}"><b>Live Commerce</b><span>Schedule YouTube streams →</span></a>
          </div>
          <div class="cp-seller-bars">
            @php $productTotal=max(1,(int)$stats['products']); $publishedPct=min(100,round(((int)$stats['published']/$productTotal)*100)); $stockPct=min(100,round((((int)$stats['products']-(int)$stats['low_stock'])/$productTotal)*100)); @endphp
            <div class="cp-seller-bar-row"><div class="cp-seller-bar-label"><span>Published catalog</span><b>{{$stats['published']}} / {{$stats['products']}}</b></div><div class="cp-seller-bar"><i style="width:{{$publishedPct}}%"></i></div></div>
            <div class="cp-seller-bar-row"><div class="cp-seller-bar-label"><span>Healthy stock</span><b>{{$stats['products']-$stats['low_stock']}} / {{$stats['products']}}</b></div><div class="cp-seller-bar"><i style="width:{{$stockPct}}%"></i></div></div>
          </div>
        </section>

        <section class="cp-seller-card" id="quick-add-product">
          <h2>Quick add product</h2>
          <p class="sub">Submit a new product for admin approval.</p>
          <form class="cp-seller-form" method="POST" action="{{route('vendor.products.store')}}">
            @csrf
            <label class="full">Product name<input name="name" required placeholder="e.g. Premium Wireless Headphones"></label>
            <label>Category<select name="category_id"><option value="">Select category</option>@foreach($categories as $category)<option value="{{$category->id}}">{{$category->name}}</option>@endforeach</select></label>
            <label>SKU<input name="sku" placeholder="Optional — auto generated"></label>
            <label>Retail price<input name="retail_price" type="number" step="0.01" min="0" required placeholder="0.00"></label>
            <label>Cost price<input name="cost_price" type="number" step="0.01" min="0" placeholder="0.00"></label>
            <label>Currency<select name="currency"><option value="USD">USD</option><option value="PKR">PKR</option><option value="CNY">CNY</option><option value="AED">AED</option><option value="EUR">EUR</option></select></label>
            <label>Primary marketplace country<select name="country_id" required><option value="">Select country</option>@foreach($countries as $country)<option value="{{$country->id}}">{{$country->name}} ({{$country->currency_code}})</option>@endforeach</select></label>
            <label>Fulfillment<select name="fulfillment_type" required><option value="seller">Seller fulfilled</option><option value="marketplace">Marketplace fulfillment</option></select></label>
            <label>Sold by<select name="sold_by_type" required><option value="seller">Seller</option><option value="marketplace_fba">Marketplace fulfillment</option></select></label>
            <label>Stock<input name="stock" type="number" step="0.001" min="0" required placeholder="0"></label>
            <label class="full">Short description<textarea name="short_description" placeholder="A short customer-facing description"></textarea></label>
            <button class="cp-seller-submit" type="submit">Submit Product for Approval</button>
          </form>
        </section>
      </div>

      <section class="cp-seller-card cp-seller-table" style="margin-top:16px">
        <div class="cp-seller-table-head"><div><h2>Recent orders</h2><p class="sub">Latest customer orders containing your products.</p></div><a class="cp-seller-btn" href="{{route('vendor.orders')}}">Manage orders</a></div>
        <div class="cp-seller-table-wrap"><table><thead><tr><th>Order</th><th>Amount</th><th>Status</th><th>Fulfillment</th><th>Created</th></tr></thead><tbody>
        @forelse($recentOrders as $order)<tr><td><strong>#{{$order->order_number}}</strong></td><td>{{$order->currency}} {{number_format((float)$order->grand_total,2)}}</td><td><span class="cp-seller-status {{$order->status}}">{{$order->status}}</span></td><td><span class="cp-seller-status {{$order->fulfillment_status}}">{{$order->fulfillment_status}}</span></td><td>{{IlluminateSupportCarbon::parse($order->created_at)->format('d M Y, H:i')}}</td></tr>@empty<tr><td colspan="5">No orders yet.</td></tr>@endforelse
        </tbody></table></div>
      </section>

      <section class="cp-seller-card cp-seller-table" id="recent-products">
        <div class="cp-seller-table-head"><div><h2>Recent products</h2><p class="sub">Latest products in your catalog.</p></div><a class="cp-seller-btn" href="{{route('vendor.products')}}">View all products</a></div>
        <div class="cp-seller-table-wrap">
          <table>
            <thead><tr><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($recentProducts as $p)
              <tr>
                <td><strong>{{\Illuminate\Support\Str::limit($p->name,42)}}</strong></td>
                <td>{{$p->sku}}</td>
                <td>{{$p->currency}} {{number_format((float)$p->retail_price,2)}}</td>
                <td>{{$p->stock}}</td>
                <td><span class="cp-seller-status {{$p->status}}">{{$p->status}}</span></td>
                <td><a href="{{route('vendor.products.edit',$p->id)}}" style="color:#b45309;font-weight:800;text-decoration:none">Edit</a></td>
              </tr>
            @empty
              <tr><td colspan="6">No products yet. Use Quick add product above to create your first listing.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </section>
</div>
@endsection