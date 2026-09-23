<form class="cp-search" method="GET" action="{{route('search')}}" data-cp-search-form>
<div class="cp-search-category" data-cp-category-select>
<button type="button" class="cp-search-category-btn" data-cp-category-toggle aria-expanded="false"><span class="cp-search-category-label">{{ $headerCategories->firstWhere('id',(int)request('category'))?->name ?? 'All' }}</span><span class="cp-search-category-chevron">⌄</span></button>
<div class="cp-search-category-menu" data-cp-category-menu><button type="button" class="cp-search-category-option @if(!request('category')) is-selected @endif" data-category-value="">All Categories</button>
@foreach(($headerCategories??collect()) as $category)<button type="button" class="cp-search-category-option @if((int)request('category')===$category->id) is-selected @endif" data-category-value="{{$category->id}}">{{$category->name}}</button>@endforeach</div>
<input type="hidden" name="category" value="{{request('category')}}"></div>
<input name="q" type="search" value="{{request('q')}}" placeholder="Search Chacha Prime" autocomplete="off"><button type="submit" aria-label="Search"><span class="cp-search-icon" aria-hidden="true"></span></button>
</form>