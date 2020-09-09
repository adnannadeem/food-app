@extends('admin.layouts.master')

@section('main-content')
	
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Delivery Boys') }}</h1>
            {{ Breadcrumbs::render('delivery-boys/view') }}
        </div>

        <div class="section-body">
            <h2 class="section-title">{{ $user->name }}</h2>
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card profile-widget">
                        <div class="profile-widget-header">
                            <img alt="image" src="{{ $user->images }}" class="rounded-circle profile-picture">
                        </div>
                        <div class="profile-widget-description">
                            <dl class="row">
                            	<dt class="col-sm-4">{{ __('Name') }}</dt>
                                <dd class="col-sm-8">{{ $user->name }}</dd>
                                <dt class="col-sm-4">{{ __('Phone') }}</dt>
                                <dd class="col-sm-8">{{ $user->phone }}</dd>
                                <dt class="col-sm-4">{{ __('Email') }}</dt>
                                <dd class="col-sm-8">{{ $user->email }}</dd>
                                <dt class="col-sm-4">{{ __('Username') }}</dt>
                                <dd class="col-sm-8">{{ $user->username }}</dd>
                                <dt class="col-sm-4">{{ __('Address') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $user->address }}
                                </dd>
                                <dt class="col-sm-4">{{ __('Status') }}</dt>
                                <dd class="col-sm-8">
                                    @php
						        		$status = $user->status;
						        		echo trans('user_statuses.'.$status);
						        	@endphp
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                	<div class="card">
                        <div class="card-body">
		                    <div class="table-responsive">
		                        <table class="table table-striped" id="maintable" data-url="{{ route("admin.deliveryboys.get-order-history") }}" data-deliveryboyid="{{ $user->id }}"> 
		                            <thead>
		                            <tr>
		                                <th>{{ __('Code') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
		                            </tr>
		                            </thead>
		                        </table>
		                    </div>
		                </div>
		            </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/deliveryboy/view.js') }}"></script>
@endsection