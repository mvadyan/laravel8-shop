<ul>
    @foreach ($items->where('parent_id', $parent) as $item)
        <li>
            @if (count($items->where('parent_id', $item->id)))
                <span class="badge badge-dark">
                <i class="fa fa-plus"></i>
            </span>
            @endif
            <a href="{{ route('catalog.category', [$item->slug]) }}">{{ $item->name }}</a>
            @if (count($items->where('parent_id', $item->id)))
                @include('layout.part.branch', ['parent' => $item->id])
            @endif
        </li>
    @endforeach
</ul>
