@extends('admin.layouts.master')

@section('main-content')
    
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Shops') }}</h1>
            {{ Breadcrumbs::render('shop/view') }}
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-plus-square"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Total Order') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $total_order }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="far fa-paper-plane"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Order Pending') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $pending_order }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="far fa-star"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Order Process') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $process_order }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Order Completed') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $completed_order }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body card-profile">
                            <img class="profile-user-img img-responsive img-circle" src="{{ $shop->images }}" alt="User profile picture">
                            <h3 class="text-center">{{ $shop->name }}</h3>
                            <p class="text-center">
                                {{ $shop->address }}
                            </p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body card-profile">
                            <img class="profile-user-img img-responsive img-circle" src="{{ $user->images }}" alt="User profile picture">
                            <h3 class="text-center">{{ $user->name }}</h3>
                            <p class="text-center">
                                @php
                                    $rolesID = $user->roles;
                                    echo trans('user_roles.'.$rolesID);
                                @endphp
                            </p>

                            <ul class="list-group">
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Username') }}</span> <span class="float-right">{{ $user->name }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Phone') }}</span> <span class="float-right">{{ $user->phone }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Email') }}</span> <span class="float-right">{{ $user->email }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Address') }}</span> <span class="float-right profile-list-group-item-addresss">{{ $user->address }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Credit') }}</span> <span class="float-right profile-list-group-item-addresss">{{ currencyFormat($user->balance->balance) }}</span></li>
                            </ul>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-8 col-md-8 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="profile-desc">
                                <div class="single-profile">
                                    <p><b>{{ __('Name') }}: </b> {{ $shop->name}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Location') }}: </b> {{ $shop->location->name ?? null}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Area') }}: </b> {{ $shop->area->name ?? null}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Delivery Charge') }}: </b> {{ $shop->delivery_charge}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Latitude') }}: </b> {{ $shop->lat}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Longitude') }}: </b> {{ $shop->long}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Current Status') }}: </b> {{ trans('current_statuses.'.$shop->current_status) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Status') }}: </b> {{ trans('statuses.'.$shop->status) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Opening Time') }}: </b> {{ date('h:i A', strtotime($shop->opening_time)) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Closing Time') }}: </b> {{ date('h:i A', strtotime($shop->closing_time)) }}</p>
                                </div>
                                <div class="single-full-profile">
                                    <p><b>{{ __('Address') }}: </b> {{ $shop->address}}</p>
                                </div>
                                <div class="single-full-profile">
                                    <p><b>{{ __('Description') }}: </b> {!! $shop->description !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
