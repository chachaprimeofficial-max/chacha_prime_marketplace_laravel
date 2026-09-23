<form class="cp-search" method="GET" action="{{ route('search') }}" role="search">
    <select name="category" aria-label="Search category">
        <option value="">All</option>
        @foreach(($headerCategories ?? collect()) as $category)
            <option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search Chacha Prime" autocomplete="off">
    <button type="submit" aria-label="Search">⌕</button>
</form>