@extends('layout.site')

@section('content')
    <h1>Интернет-магазин</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ducimus eveniet...</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet asperiores corporis...</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aspernatur assumenda...</p>

    @if($new->count())
        <h2>Новинки</h2>
        <div class="row">
            @foreach($new as $item)
                @include('catalog.part.product', ['product' => $item])
            @endforeach
        </div>
    @endif

    @if($hit->count())
        <h2>Лидеры продаж</h2>
        <div class="row">
            @foreach($hit as $item)
                @include('catalog.part.product', ['product' => $item])
            @endforeach
        </div>
    @endif

    @if($sale->count())
        <h2>Распродажа</h2>
        <div class="row">
            @foreach($sale as $item)
                @include('catalog.part.product', ['product' => $item])
            @endforeach
        </div>
    @endif
@endsection
