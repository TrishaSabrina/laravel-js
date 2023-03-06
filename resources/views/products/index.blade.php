@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{route('product.search')}}" method="POST" class="card-header">
            @csrf
            @method('POST')
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                    @error('title')
                       <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <select name="variantid" id="" class="form-control">
                        @forelse($variants as $variant)
                        <option value="{{$variant->id}}"></option>
                        @empty
                        @endforelse
                    </select>
                     @error('variantid')
                       <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                         @error('price_from')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                         @error('price_to')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                     @error('date')
                       <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th width="10%">Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <!-- <td>@upper($product->title) <br> Created at : {{$product->created_at->diffForHumans()}}</td> -->
                            <td>{{$product->description}}</td>
                            <td>
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                    @forelse($product->prices as $price)
                                        <dt class="col-sm-3 pb-0">
                                            <!-- @upper($price->variant_one==null?'':$price->variant_one->variant.'/') @upper($price->variant_two!=null?$price->variant_two->variant.'/':'') @upper($price->variant_three!=null?$price->variant_three->variant.'/':'') -->
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price : {{ number_format($price->price,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock : {{ number_format($price->stock,2) }}</dd>
                                            </dl>
                                        </dd>
                                    @empty
                                    @endforelse

                                </dl>
                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- <tr>
                            <td colspan="5"><h1 class="text-center">Data not found</h1></td>
                        </tr> -->
                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                @if(filled($products))
                    <div class="col-md-6">
                        <p>Showing {{$products->firstItem()}} to {{ $products->lastItem() }} out of {{ $products->total() }}</p>
                    </div>
                    <div class="col-md-2">
                        {{$products->links()}}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection