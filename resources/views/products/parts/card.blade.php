<div class="col">
    <div class="card shadow-sm product-card" style="width: 18rem;">
        <div class="product-card-image-wrapper">
            @if($product->thumbnailUrl)
                <img src="{{ $product->thumbnailUrl }}" class="card-img-top w-100 product-card-image" alt="{{ $product->name }}">
            @else
                <div class="card-img-top w-100 product-card-placeholder">No Image</div>
            @endif
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $product->name }}</h5>
            <div class="row">
                <div class="col-12 col-sm-6">Price: </div>
                <div class="col-12 col-sm-6">{{ $product->finalPrice }} $</div>
            </div>
        </div>
        <div class="card-footer">
            <form method="POST" action="{{ route('cart.add', $product) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success my-2">Buy</button>
            </form>
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info my-2">Show</a>
        </div>
    </div>
</div>
