@extends('admin.layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
@endsection

@section('main-content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Shop Products') }}</h1>
            {{ Breadcrumbs::render('shop/shop-product') }}
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('admin.shop.products.create', $shop) }}" class="btn btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> {{ __('Add Shop Product') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="maintable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('levels.product') }}</th>
                                            <th>{{ __('levels.price') }}</th>
                                            <th>{{ __('levels.quantity') }}</th>
                                            <th>{{ __('levels.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!blank($shopProducts))
                                            @foreach($shopProducts as $shopproduct)
                                                <tr>
                                                    <td>{{ optional($shopproduct->product)->name}}</td>
                                                    <td>{{ $shopproduct->unit_price}}</td>
                                                    <td>{{ $shopproduct->quantity}}</td>
                                                    <td>
                                                        <a href="{{ route('admin.shop.shopproduct.edit', [$shop, $shopproduct]) }}" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>
                                                        <form class="delete float-left pl-2" action="{{ route('admin.shop.shopproduct.delete', [$shop, $shopproduct]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-icon btn-danger" 
                                                            data-toggle="tooltip" data-placement="top" title="Delete"
                                                            ><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
