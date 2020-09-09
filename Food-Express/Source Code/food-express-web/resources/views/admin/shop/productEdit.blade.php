@extends('admin.layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
@endsection

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>{{ __('Shop Products') }}</h1>
        {{ Breadcrumbs::render('shop-product-edit', $shop) }}
    </div>
    <div class="section-body">
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.shop.products.update', [$shop, $shopproduct]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="product_type">{{ __('Product Type') }}</label> <span class="text-danger">*</span>
                                    <select id="product_type" name="product_type" class="form-control @error('product_type') is-invalid @enderror">
                                        <option {{ old('product_type', $product_type)=="0"? 'selected':''}} value="">{{ __('Select Product Type') }}</option>
                                        <option {{ old('product_type', $product_type)=="5"? 'selected':''}} value="5">{{ __('Single') }}</option>
                                        <option {{ old('product_type', $product_type)=="10"? 'selected':''}} value="10">{{ __('Variation') }}</option>
                                    </select>
                                    @error('product_type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="product_id">{{ __('Products') }}</label> <span class="text-danger">*</span>
                                    <select id="product_id" name="product_id" class="form-control @error('product_id') is-invalid @enderror">
                                        <option value="">{{ __('Select Product') }}</option>
                                        @if(!blank($products))
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old('product_id', $shopproduct->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="single-product">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="unit_price">{{ __('Price') }}</label> <span
                                        class="text-danger">*</span>
                                    <input type="number" id="unit_price" name="unit_price"
                                        class="form-control @error('unit_price') is-invalid @enderror"
                                        value="{{ old('unit_price', $shopproduct->unit_price) }}">
                                    @error('unit_price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="quantity">{{ __('Quantity') }}</label> <span
                                        class="text-danger">*</span>
                                    <input type="number" id="quantity" name="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        value="{{ old('quantity', $shopproduct->quantity) }}">
                                    @error('quantity')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-variation">
                            <div class="col-sm-12">
                                <h2>Product Variations </h2>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!blank(session('variation')))
                                                @foreach(session('variation') as $variation)
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="variation[<?=$variation?>][name]" placeholder="Name" class="form-control form-control-sm @error("variation.$variation.name") is-invalid @enderror" value="{{ old("variation.$variation.name") }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("variation.$variation.price") is-invalid @enderror" value="{{ old("variation.$variation.price") }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][quantity]" placeholder="Quantity" class="form-control form-control-sm change-productquantity @error("variation.$variation.quantity") is-invalid @enderror" value="{{ old("variation.$variation.quantity") }}">
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm removeBtn">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @elseif(!blank($product_variations))
                                                @foreach($product_variations as $product_variation)
                                                    <?php
                                                        $variation = $product_variation->id;
                                                        $loopindex = $loop->index + 1;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="variation[<?=$variation?>][name]" placeholder="Name" class="form-control form-control-sm @error("variation.$variation.name") is-invalid @enderror" value="{{ old("variation.$variation.name", $product_variation->name) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("variation.$variation.price") is-invalid @enderror" value="{{ old("variation.$variation.price", $product_variation->price) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][quantity]" placeholder="Quantity" class="form-control form-control-sm change-productquantity @error("variation.$variation.quantity") is-invalid @enderror" value="{{ old("variation.$variation.quantity", $product_variation->quantity) }}">
                                                        </td>
                                                        <td>
                                                            @if($product_variations->count() ==  $loopindex ) 
                                                                <button class="btn btn-primary btn-sm" id="variation-add">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-danger btn-sm removeBtn">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>
                                                        <input type="text" name="variation[1][name]" placeholder="Name" class="form-control form-control-sm @error("variation.1.name") is-invalid @enderror" value="{{ old("variation.1.name") }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="variation[1][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("variation.1.price") is-invalid @enderror" value="{{ old("variation.1.price") }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="variation[1][quantity]" placeholder="Quantity" class="form-control form-control-sm change-productquantity @error("variation.1.quantity") is-invalid @enderror" value="{{ old("variation.1.quantity") }}">
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" id="variation-add">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-option">
                            <div class="col-sm-12">
                                <h2>Product Options </h2>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!blank(session('option')))
                                                @foreach(session('option') as $option)
                                                    <?php
                                                        if($option == 1) {
                                                            continue;
                                                        }
                                                    ?>
                                                     <tr>
                                                        <td>
                                                            <input type="text" name="option[<?=$option?>][name]" placeholder="Name" class="form-control form-control-sm @error("option.$option.name") is-invalid @enderror" value="{{ old("option.$option.name") }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="option[<?=$option?>][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("option.$option.price") is-invalid @enderror" value="{{ old("option.$option.price") }}">
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm removeBtn">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                             @elseif(!blank($product_options))
                                                <?php
                                                    $option = count($product_options);
                                                    $product_option = [];
                                                ?>
                                                @foreach($product_options as $product_option)
                                                    <?php if($option == 1) {
                                                        continue;
                                                    } ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="option[<?=$option?>][name]" placeholder="Name" class="form-control form-control-sm @error("option.$option.name") is-invalid @enderror" value="{{ old("option.$option.name", $product_option->name) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="option[<?=$option?>][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("option.$option.price") is-invalid @enderror" value="{{ old("option.$option.price", $product_option->price) }}">
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm removeBtn">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php $option--; ?>
                                                @endforeach
                                            @endif

                                            <?php
                                                $productName     = $product_option->name ?? '';
                                                $productPrice    = $product_option->price ?? '';
                                            ?>

                                            <tr>
                                                <td>
                                                    <input type="text" name="option[1][name]" placeholder="Name" class="form-control form-control-sm @error("option.1.name") is-invalid @enderror" value="{{ old('option.1.name', $productName) }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="option[1][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("option.1.price") is-invalid @enderror" value="{{ old('option.1.price', $productPrice) }}">
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" id="option-add">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <button class="btn btn-primary" id="saveShopProduct" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        var product_variation_count  = <?=!blank(session('variation')) ? count(session('variation')) : ($product_variations->count() == 0 ? 1 : $product_variations->count())?>;
        var product_option_count     = <?=!blank(session('option')) ? count(session('option')) : $product_options->count()?>;
        var get_product_type         = <?=$product_type?>; 
        var product_type             = '<?=old('product_type')?>'; 
    </script>
    <script src="{{ asset('js/shop/productEdit.js') }}"></script>
@endsection
