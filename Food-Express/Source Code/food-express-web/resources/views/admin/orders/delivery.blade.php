@extends('admin.layouts.master')

@section('main-content')
	
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Orders') }}</h1>
            {{ Breadcrumbs::render('orders/delivery') }}
        </div>

        <div class="section-body">
        	<div class="row">
	   			<div class="col-4 col-md-4 col-lg-4">
			    	<div class="card">
					    <div class="card-body card-profile">
					        <img class="profile-user-img img-responsive img-circle" src="{{ $order->delivery->images }}" alt="User profile picture">
					        <h3 class="text-center">{{ $order->delivery->name }}</h3>
					        <p class="text-center">
					        	@php
					        		$rolesID = $order->delivery->roles;
					        		echo trans('user_roles.'.$rolesID);
					        	@endphp
					        </p>
					    </div>
					    <!-- /.box-body -->
					</div>
				</div>
	   			<div class="col-8 col-md-8 col-lg-8">
			    	<div class="card">
			    		<div class="card-body">
			    			<div class="profile-desc">
			    				<div class="single-profile">
			    					<p><b>{{ __('First Name') }}: </b> {{ $order->delivery->first_name}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Last Name') }}: </b> {{ $order->delivery->last_name}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Email') }}: </b> {{ $order->delivery->email}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Phone') }}: </b> {{ $order->delivery->phone}}</p>
			    				</div>
			    				<div class="single-full-profile">
			    					<p><b>{{ __('Address') }}: </b> {{ $order->delivery->address}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Status') }}: </b> 
										@php
							        		$status = $order->delivery->status;
							        		echo trans('user_statuses.'.$status);
							        	@endphp
			    					</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Username') }}: </b> {{ $order->delivery->username}}</p>
			    				</div>
			    			</div>
			    		</div>
			    	</div>
				</div>
        	</div>
        </div>
    </section>

@endsection
