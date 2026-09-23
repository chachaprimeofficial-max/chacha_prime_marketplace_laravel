<div class="cp-drawer-overlay" data-cp-overlay></div>
<aside class="cp-drawer" data-cp-drawer aria-hidden="true">
    <div class="cp-drawer-head">
        <strong>☰ Shop by Category</strong>
        <button type="button" data-cp-close-drawer aria-label="Close categories">×</button>
    </div>
    <div class="cp-drawer-body">
        @foreach(($headerCategories ?? collect()) as $category)
            <div class="cp-category-group">
                <a class="cp-category-title" href="{{ route('shop') }}?category={{ $category->id }}">{{ $category->name }} <span>›</span></a>
                @if($category->children && $category->children->count())
                    <div class="cp-subcategories">
                        @foreach($category->children as $child)
                            <a href="{{ route('shop') }}?category={{ $child->id }}">{{ $child->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</aside>