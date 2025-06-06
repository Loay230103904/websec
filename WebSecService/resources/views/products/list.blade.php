@extends('layouts.master')
@section('title', 'Test Page')
@section('content')

<!-- عرض رصيد العميل فقط إذا كان مسجل الدخول -->
<div class="row mt-2">
    <div class="col col-12">
        @if(auth()->check())
            <h4>Your Credit: <span class="badge bg-primary">{{ auth()->user()->credit }}</span></h4>
        @else
            <h4 class="alert alert-warning alert-dismissible fade show" role="alert">You are not logged in.</h4>
        @endif
    </div>
</div>



<!-- رسائل النجاح أو الفشل -->
@if(session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mt-2">
        {{ session('error') }}
    </div>
@endif

<div class="row mt-2">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    <div class="col col-2">
        @can('add_products')
        <a href="{{route('products_edit')}}" class="btn btn-success form-control">Add Product</a>
        @endcan
    </div>
</div>

<!-- فلترة المنتجات -->
<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="numeric" class="form-control" placeholder="Min Price" value="{{ request()->min_price }}"/>
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="numeric" class="form-control" placeholder="Max Price" value="{{ request()->max_price }}"/>
        </div>
        <div class="col col-sm-2">
        <select name="order_by" class="form-select">
    <option value="" {{ request()->order_by == "" ? "selected" : "" }} disabled>Order By</option>
    <option value="name" {{ request()->order_by == "name" ? "selected" : "" }}>Name</option>
    <option value="price" {{ request()->order_by == "price" ? "selected" : "" }}>Price</option>
    <option value="popularity" {{ request()->order_by == "popularity" ? "selected" : "" }}>Popularity</option>
</select>

        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" {{ request()->order_direction==""?"selected":"" }} disabled>Order Direction</option>
                <option value="ASC" {{ request()->order_direction=="ASC"?"selected":"" }}>ASC</option>
                <option value="DESC" {{ request()->order_direction=="DESC"?"selected":"" }}>DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>

<!-- عرض المنتجات -->
@foreach($products as $product)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col col-sm-12 col-lg-4">
                    <img src="{{asset("images/$product->photo")}}" class="img-thumbnail" alt="{{$product->name}}" width="100%">
                </div>
                <div class="col col-sm-12 col-lg-8 mt-3">
                    <div class="row mb-2">
                        <div class="col-8">
                            <h3>{{$product->name}}</h3>
                        </div>
                        <div class="col col-2">
                            @can('edit_products')
                                <a href="{{route('products_edit', $product->id)}}" class="btn btn-success form-control">Edit</a>
                            @endcan
                        </div>
                        <div class="col col-2">
                            @can('delete_products')
                                <a href="{{route('products_delete', $product->id)}}" class="btn btn-danger form-control">Delete</a>
                            @endcan
                        </div>
                    </div>
                    <!-- نموذج شراء المنتج -->
                    <form action="{{ route('products.purchase', $product->id) }}" method="POST">
                        @csrf
                        <div class="row mb-2">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">Buy</button>
                            </div>
                        </div>
                    </form>
                    <!-- عرض تفاصيل المنتج -->
                    <table class="table table-striped">
                        <tr><th width="20%">Name</th><td>{{$product->name}}</td></tr>
                        <tr><th>Model</th><td>{{$product->model}}</td></tr>
                        <tr><th>Code</th><td>{{$product->code}}</td></tr>
                        <tr><th>Price</th><td>{{$product->price}}</td></tr>
                        <tr><th>Description</th><td>{{$product->description}}</td></tr>
                        <tr><th>Likes</th><td>{{$product->likes}}</td></tr>

                    </table>
                    <!-- Like Button -->
                    @if(auth()->check())
                        @if(auth()->user()->hasPurchased($product->id))
                            <form action="{{ route('products.like', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Like</button>
                            </form>
                        @endif
                    @endif

@if(auth()->check() && auth()->user()->can('add_review') && auth()->user()->hasPurchased($product->id))
    <form action="{{ route('products.review_submit', $product->id) }}" method="POST" class="mt-3">
        @csrf
        <div class="mb-2">
            <label for="review">Leave a Review</label>
            <textarea name="review" class="form-control" required></textarea>
        </div>
        <div class="mb-2">
            <label for="rating">Rating (1-5)</label>
            <input type="number" name="rating" class="form-control" min="1" max="5">
        </div>
        <button type="submit" class="btn btn-warning">Add Review</button>
    </form>
@endif


                </div>
            </div>
        </div>
    </div>
@endforeach


@endsection
