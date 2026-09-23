<div class="cp-drawer-overlay" data-cp-overlay></div>
<aside class="cp-drawer" data-cp-drawer aria-hidden="true">
<div class="cp-drawer-head"><strong>☰ Shop by Category</strong><button type="button" data-cp-close-drawer aria-label="Close menu">×</button></div>
<div class="cp-drawer-body">
<div class="cp-drawer-home"><a href="{{route('shop')}}">All Products</a></div>
@foreach(($headerCategories??collect()) as $category)
<div class="cp-category-group" data-cp-category-group>
<button type="button" class="cp-category-title" data-cp-category-trigger aria-expanded="false">
<span>{{$category->name}}</span><span class="cp-category-arrow">›</span>
</button>
@if($category->children && $category->children->count())
<div class="cp-subcategories" data-cp-subcategories>
<a class="cp-view-all" href="{{route('shop')}}?category={{$category->id}}">Shop all {{$category->name}}</a>
@foreach($category->children as $child)
<a href="{{route('shop')}}?category={{$child->id}}">{{$child->name}}</a>
@endforeach
</div>
@else
<a class="cp-category-direct" href="{{route('shop')}}?category={{$category->id}}"></a>
@endif
</div>
@endforeach
</div>
</aside>