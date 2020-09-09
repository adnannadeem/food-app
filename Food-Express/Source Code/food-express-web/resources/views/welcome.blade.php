@extends('layouts.app')
@section('nav')
    <nav class="navbar navbar-main navbar-expand-lg navbar-light border-bottom">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav" aria-controls="main_nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="main_nav">
                <x-category/>
            </div>
        </div>
    </nav>
@endsection
@section('main-content')

    <!-- ========================= SECTION INTRO ========================= -->
    <section class="section-intro padding-y-sm">
        <div class="container">

            <div class="intro-banner-wrap">
                <img src="{{ asset('frontend/images/banners/1.jpg') }}" class="img-fluid rounded">
            </div>

        </div> <!-- container //  -->
    </section>
    <!-- ========================= SECTION INTRO END// ========================= -->


    <!-- ========================= SECTION FEATURE ========================= -->
    <section class="section-content padding-y-sm">
        <div class="container">
            <article class="card card-body">


                <div class="row">
                    <div class="col-md-4">
                        <figure class="item-feature">
                            <span class="text-primary"><i class="fa fa-2x fa-truck"></i></span>
                            <figcaption class="pt-3">
                                <h5 class="title">Fast delivery</h5>
                                <p>Dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                    tempor incididunt ut labore </p>
                            </figcaption>
                        </figure> <!-- iconbox // -->
                    </div><!-- col // -->
                    <div class="col-md-4">
                        <figure  class="item-feature">
                            <span class="text-primary"><i class="fa fa-2x fa-landmark"></i></span>
                            <figcaption class="pt-3">
                                <h5 class="title">Creative Strategy</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                </p>
                            </figcaption>
                        </figure> <!-- iconbox // -->
                    </div><!-- col // -->
                    <div class="col-md-4">
                        <figure  class="item-feature">
                            <span class="text-primary"><i class="fa fa-2x fa-lock"></i></span>
                            <figcaption class="pt-3">
                                <h5 class="title">High secured </h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                </p>
                            </figcaption>
                        </figure> <!-- iconbox // -->
                    </div> <!-- col // -->
                </div>
            </article>

        </div> <!-- container .//  -->
    </section>
    <!-- ========================= SECTION FEATURE END// ========================= -->


    <!-- ========================= SECTION CONTENT ========================= -->
    <section class="section-content">
        <div class="container">

            <header class="section-heading">
                <h3 class="section-title">Popular products</h3>
            </header><!-- sect-heading -->


            <div class="row">
                @if(!blank($shopProducts))
                    @foreach($shopProducts as $shopProduct)
                        <div class="col-md-3">
                            <div class="card card-product-grid">
                                <a href="{{ route('shop.product', [$shopProduct->shop->slug, $shopProduct->product->slug]) }}" class="img-wrap"> <img src="{{ $shopProduct->product->images }}"> </a>
                                <figcaption class="info-wrap">
                                    <a href="{{ route('shop.product', [$shopProduct->shop->slug, $shopProduct->product->slug]) }}" class="title">{{ $shopProduct->product->name }}</a>
                                    {{-- <div class="rating-wrap">
                                        <ul class="rating-stars">
                                            <li style="width:80%" class="stars-active">
                                                <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                            </li>
                                            <li>
                                                <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                            </li>
                                        </ul>
                                        <span class="label-rating text-muted"> 34 reviws</span>
                                    </div> --}}
                                    <div class="price mt-1">{{ currencyFormat($shopProduct->product->unit_price) }}</div> <!-- price-wrap.// -->
                                </figcaption>
                            </div>
                        </div> <!-- col.// -->
                    @endforeach
                @endif
            </div> <!-- row.// -->
        </div> <!-- container .//  -->
    </section>
    <!-- ========================= SECTION CONTENT END// ========================= -->

    <!-- ========================= SECTION  ========================= -->
    <section class="section-name bg padding-y-sm">
        <div class="container">
            <header class="section-heading">
                <h3 class="section-title">Our Brands</h3>
            </header><!-- sect-heading -->

            <div class="row">
                <div class="col-md-2 col-6">
                    <figure class="box item-logo">
                        <a href="#"><img src="{{ asset('frontend/images/logos/logo1.png') }}"></a>
                        <figcaption class="border-top pt-2">36 Products</figcaption>
                    </figure> <!-- item-logo.// -->
                </div> <!-- col.// -->
                <div class="col-md-2  col-6">
                    <figure class="box item-logo">
                        <a href="#"><img src="{{ asset('frontend/images/logos/logo2.png') }}"></a>
                        <figcaption class="border-top pt-2">980 Products</figcaption>
                    </figure> <!-- item-logo.// -->
                </div> <!-- col.// -->
                <div class="col-md-2  col-6">
                    <figure class="box item-logo">
                        <a href="#"><img src="{{ asset('frontend/images/logos/logo3.png') }}"></a>
                        <figcaption class="border-top pt-2">25 Products</figcaption>
                    </figure> <!-- item-logo.// -->
                </div> <!-- col.// -->
                <div class="col-md-2  col-6">
                    <figure class="box item-logo">
                        <a href="#"><img src="{{ asset('frontend/images/logos/logo4.png') }}"></a>
                        <figcaption class="border-top pt-2">72 Products</figcaption>
                    </figure> <!-- item-logo.// -->
                </div> <!-- col.// -->
                <div class="col-md-2  col-6">
                    <figure class="box item-logo">
                        <a href="#"><img src="{{ asset('frontend/images/logos/logo5.png') }}"></a>
                        <figcaption class="border-top pt-2">41 Products</figcaption>
                    </figure> <!-- item-logo.// -->
                </div> <!-- col.// -->
                <div class="col-md-2  col-6">
                    <figure class="box item-logo">
                        <a href="#"><img src="{{ asset('frontend/images/logos/logo2.png') }}"></a>
                        <figcaption class="border-top pt-2">12 Products</figcaption>
                    </figure> <!-- item-logo.// -->
                </div> <!-- col.// -->
            </div> <!-- row.// -->
        </div><!-- container // -->
    </section>
    <!-- ========================= SECTION  END// ========================= -->



    <!-- ========================= SECTION  ========================= -->
    <section class="section-name padding-y">
        <div class="container">

            <h3 class="mb-3">Download app demo text</h3>

            <a href="#"><img src="{{ asset('frontend/images/misc/appstore.png') }}" height="40"></a>
            <a href="#"><img src="{{ asset('frontend/images/misc/appstore.png') }}" height="40"></a>

        </div><!-- container // -->
    </section>
    <!-- ========================= SECTION  END// ======================= -->


@endsection
