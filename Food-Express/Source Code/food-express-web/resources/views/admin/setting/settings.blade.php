@extends('admin.layouts.master')

@section('main-content')

<section class="section">
    <div class="section-header">
        <h1>{{ __('Settings') }}</h1>
        {{ Breadcrumbs::render('payment-setting') }}
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{--  <div class="card-header">
                    <h4>{{ __('All Settings') }}</h4>
            </div> --}}
            <div class="card-body">


                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item"> <a
                            class="nav-link {{ (old('gateway') == 'site') ? 'active' : ((old('gateway') == '') ? 'active' : '') }}"
                            id="site" data-toggle="pill" href="#sitetab" role="tab" aria-controls="sitetab"
                            aria-selected="true">{{ __('Site Setting') }}</a></li>
                    <li class="nav-item"> <a class="nav-link {{ (old('gateway') == 'sms') ? 'active' : '' }}" id="sms"
                            data-toggle="pill" href="#smstab" role="tab" aria-controls="smstab"
                            aria-selected="false">{{ __('SMS Setting') }}</a></li>
                    <li class="nav-item"> <a class="nav-link {{ (old('gateway2') == 'payment') ? 'active' : '' }}"
                            id="payment" data-toggle="pill" href="#paymenttab" role="tab" aria-controls="paymenttab"
                            aria-selected="false">{{ __('Payment Setting') }}</a></li>
                    <li class="nav-item"> <a class="nav-link {{ (old('gateway') == 'email') ? 'active' : '' }}"
                            id="email" data-toggle="pill" href="#emailtab" role="tab" aria-controls="emailtab"
                            aria-selected="false">{{ __('Email Setting') }}</a></li>
                    <li class="nav-item"> <a class="nav-link {{ (old('gateway') == 'razorpay') ? 'active' : '' }}"
                            id="razorpay" data-toggle="pill" href="#notificationtab" role="tab"
                            aria-controls="notificationtab" aria-selected="false">{{ __('Notification Setting') }}</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link {{ (old('gateway1') == 'social') ? 'active' : '' }}"
                            id="social" data-toggle="pill" href="#socialtab" role="tab" aria-controls="socialtab"
                            aria-selected="false">{{ __('Social Login Setting') }}</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link {{ (old('gateway') == 'otp') ? 'active' : '' }}" id="otp"
                            data-toggle="pill" href="#otptab" role="tab" aria-controls="otptab"
                            aria-selected="false">{{ __('Otp Setting') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card" id="settings-card">
            <div class="card-body">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade {{ (old('gateway') == 'site') ? 'show active' : ((old('gateway') == '') ? 'show active' : '') }}"
                        id="sitetab" role="tabpanel" aria-labelledby="stripe">
                        <h4 class="paymentheader">{{ __('Site Setting') }}</h4>
                        <form class="form-horizontal" role="form" method="POST"
                            action="{{ route('admin.setting.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                {{--  <div class="card-header">
                                    <h4>{{ __('Site Setting') }}</h4>
                            </div> --}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="site_name">{{ __('levels.site_name') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="site_name" id="site_name" type="text"
                                                class="form-control @error('site_name') is-invalid @enderror"
                                                value="{{ old('site_name', setting('site_name')) }}">
                                            @error('site_name')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="currency_name">{{ __('levels.currency_name') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="currency_name" id="currency_name" type="text"
                                                class="form-control @error('currency_name') is-invalid @enderror"
                                                value="{{ old('currency_name', setting('currency_name')) }}">
                                            @error('currency_name')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="geolocation_distance_radius">{{ __('levels.geolocation_distance_radius') }}</label>
                                            <span class="text-danger">*</span>
                                            <input name="geolocation_distance_radius" id="geolocation_distance_radius"
                                                type="text"
                                                class="form-control @error('geolocation_distance_radius') is-invalid @enderror"
                                                value="{{ old('geolocation_distance_radius', setting('geolocation_distance_radius')) }}">
                                            @error('geolocation_distance_radius')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="site_description">{{ __('levels.description') }}</label> <span
                                                class="text-danger">*</span>
                                            <textarea name="site_description" id="site_description" cols="30" rows="3"
                                                class="form-control small-textarea-height @error('site_description') is-invalid @enderror">{{ old('site_description', setting('site_description')) }}</textarea>
                                            @error('site_description')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="customFile">{{ __('levels.site_logo') }}</label>
                                            <div class="custom-file">
                                                <input name="site_logo" type="file"
                                                    class="custom-file-input @error('site_logo') is-invalid @enderror"
                                                    id="customFile" onchange="readURL(this);">
                                                <label class="custom-file-label" for="customFile">Choose
                                                    file</label>
                                            </div>
                                            @error('site_logo')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                            @if(setting('site_logo'))
                                            <img class="img-thumbnail image-width mt-4 mb-3" id="previewImage"
                                                src="{{ asset('images/'.setting('site_logo')) }}"
                                                alt="Food Express Logo" />
                                            @else
                                            <img class="img-thumbnail image-width mt-4 mb-3" id="previewImage"
                                                src="{{ asset('images/logo.png') }}" alt="Food Express Logo" />
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="timezone">{{ __('levels.timezone') }}</label> <span
                                                class="text-danger">*</span>
                                            <?php
                                        $className = 'form-control';
                                        if($errors->first('timezone')) {
                                            $className = 'form-control is-invalid';
                                        }
                                            echo Timezonelist::create('timezone', setting('timezone') , ['class'=> $className]); ?>
                                            @error('timezone')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="site_footer">{{ __('levels.site_footer') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="site_footer" id="site_footer"
                                                class="form-control @error('site_footer') is-invalid @enderror"
                                                value="{{ old('site_footer', setting('site_footer')) }}">
                                            @error('site_footer')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="currency_code">{{ __('levels.currency_code') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="currency_code" id="currency_code" type="text"
                                                class="form-control @error('currency_code') is-invalid @enderror"
                                                value="{{ old('currency_code', setting('currency_code')) }}">
                                            @error('currency_code')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="order_commission_percentage">{{ __('levels.order_commission_percentage') }}</label>
                                            <span class="text-danger">*</span>
                                            <input name="order_commission_percentage" id="order_commission_percentage"
                                                type="number" min="0" max="100"
                                                class="form-control @error('order_commission_percentage') is-invalid @enderror"
                                                value="{{ old('order_commission_percentage', setting('order_commission_percentage')) }}">
                                            @error('order_commission_percentage')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="customer_terms_condition">{{ __('levels.customer_terms_condition') }}</label>
                                            <span class="text-danger">*</span>
                                            <textarea name="customer_terms_condition" id="customer_terms_condition"
                                                cols="30" rows="3"
                                                class="form-control small-textarea-height summernote @error('customer_terms_condition') is-invalid @enderror">{{ old('customer_terms_condition', setting('customer_terms_condition')) }}</textarea>
                                            @error('customer_terms_condition')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="shopowner_terms_condition">{{ __('levels.shop_owner_terms_condition') }}</label>
                                            <span class="text-danger">*</span>
                                            <textarea name="shopowner_terms_condition" id="shopowner_terms_condition"
                                                cols="30" rows="3"
                                                class="form-control summernote small-textarea-height @error('shopowner_terms_condition') is-invalid @enderror">{{ old('shopowner_terms_condition', setting('shopowner_terms_condition')) }}</textarea>
                                            @error('shopowner_terms_condition')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <button
                                            class="btn btn-primary btn-sm"><span>{{ __('Update Setting') }}</span></button>
                                    </div>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>

                <div class="tab-pane fade {{ (old('gateway') == 'sms') ? 'show active' : '' }}" id="smstab"
                    role="tabpanel" aria-labelledby="sms">
                    <form class="form-horizontal" role="form" method="POST"
                        action="{{ route('admin.setting.sms.update') }}">
                        @csrf
                        <h4 class="paymentheader">{{ __('SMS Setting') }}</h4>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="twilio_auth_token">{{ __('Twilio Auth Token') }}</label> <span
                                        class="text-danger">*</span>
                                    <input name="twilio_auth_token" id="twilio_auth_token" type="text"
                                        class="form-control {{ $errors->has('twilio_auth_token') ? ' has-error ' : '' }}"
                                        value="{{ old('twilio_auth_token', setting('twilio_auth_token')) }}">
                                    @if ($errors->has('twilio_auth_token'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('twilio_auth_token') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="twilio_account_sid">{{ __('levels.twilio_account_sid') }}</label> <span
                                        class="text-danger">*</span>
                                    <input name="twilio_account_sid" id="twilio_account_sid" type="text"
                                        class="form-control {{ $errors->has('twilio_account_sid') ? ' has-error ' : '' }}"
                                        value="{{ old('twilio_account_sid', setting('twilio_account_sid')) }}">
                                    @if ($errors->has('twilio_account_sid'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('twilio_account_sid') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="twilio_from">{{ __('levels.twilio_from') }}</label> <span
                                        class="text-danger">*</span>
                                    <input name="twilio_from" id="twilio_from" type="text"
                                        class="form-control {{ $errors->has('twilio_from') ? ' has-error ' : '' }}"
                                        value="{{ old('twilio_from', setting('twilio_from')) }}">
                                    @if ($errors->has('twilio_from'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('twilio_from') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="twilio_disabled">{{ __('levels.disabled') }}</label> <span
                                        class="text-danger">*</span>
                                    <input name="twilio_disabled" id="twilio_disabled" type="checkbox"
                                        class="form-control {{ $errors->has('twilio_disabled') ? ' has-error ' : '' }}"
                                        value="{{ setting('twilio_disabled') == true ? 1 : 0 }}"
                                        {{ setting('twilio_disabled') == true ? "checked" : "" }}>
                                    @if ($errors->has('twilio_disabled'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('twilio_disabled') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <button class="btn btn-primary btn-sm"><span>{{ __('Update Setting') }}</span></button>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="tab-pane fade {{ (old('gateway2') == 'payment') ? 'show active' : '' }}" id="paymenttab"
                    role="tabpanel" aria-labelledby="payment">
                    {{--  <h4 class="paymentheader">{{ __('Payment Setting') }}</h4> --}}


                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Payment Gateway') }}</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item"> <a
                                                class="nav-link {{ (old('gateway2') == 'stripe') ? 'active' : ((old('gateway2') == '') ? 'active' : '') }}"
                                                id="stripe" data-toggle="pill" href="#stripetab" role="tab"
                                                aria-controls="stripetab" aria-selected="true">{{ __('Stripe') }}</a>
                                        </li>
                                        <li class="nav-item"> <a
                                                class="nav-link {{ (old('gateway2') == 'razorpay') ? 'active' : '' }}"
                                                id="razorpay" data-toggle="pill" href="#razorpaytab" role="tab"
                                                aria-controls="razorpaytab"
                                                aria-selected="false">{{ __('Razorpay') }}</a></li>
                                        <li class="nav-item"> <a
                                                class="nav-link {{ (old('gateway2') == 'paystack') ? 'active' : '' }}"
                                                id="paystack" data-toggle="pill" href="#paystacktab" role="tab"
                                                aria-controls="paystacktab"
                                                aria-selected="false">{{ __('Paystack') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card" id="settings-card">
                                <div class="card-body">
                                    <div class="tab-content" id="v-pills-tabContent">
                                        <div class="tab-pane fade {{ (old('gateway2') == 'stripe') ? 'show active' : ((old('gateway2') == '') ? 'show active' : '') }}"
                                            id="stripetab" role="tabpanel" aria-labelledby="stripe">
                                            <h4 class="paymentheader">{{ __('Stripe Setting') }}</h4>
                                            <form class="form-horizontal" role="form" method="POST"
                                                action="{{ route('admin.setting.payment.update') }}">
                                                @csrf
                                                <input type="hidden" name="gateway2" value="stripe">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="stripe_key">{{ __('levels.stripe_key') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input name="stripe_key" id="stripe_key" type="text"
                                                                class="form-control @error('stripe_key') is-invalid @enderror"
                                                                value="{{ old('stripe_key', setting('stripe_key') ?? '') }}">
                                                            @error('stripe_key')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <button
                                                                class="btn btn-primary"><span>{{ __('Update Stripe Setting') }}</span></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="stripe_secret">{{ __('levels.stripe_secret') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="stripe_secret" id="stripe_secret" type="text"
                                                                class="form-control @error('stripe_secret') is-invalid @enderror"
                                                                value="{{ old('stripe_secret', setting('stripe_secret') ?? '') }}">
                                                            @error('stripe_secret')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade {{ (old('gateway2') == 'razorpay') ? 'show active' : '' }}"
                                            id="razorpaytab" role="tabpanel" aria-labelledby="razorpay">
                                            <h4 class="paymentheader">{{ __('Razorpay Setting') }}</h4>
                                            <form class="form-horizontal" role="form" method="POST"
                                                action="{{ route('admin.setting.payment.update') }}">
                                                @csrf
                                                <input type="hidden" name="gateway2" value="razorpay">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="razorpay_key">{{ __('levels.razorpay_key') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="razorpay_key" id="razorpay_key" type="text"
                                                                class="form-control @error('razorpay_key')is-invalid @enderror"
                                                                value="{{ old('razorpay_key', setting('razorpay_key') ?? '') }}">
                                                            @error('razorpay_key')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <button
                                                                class="btn btn-primary"><span>{{ __('Update Razorpay  Setting') }}</span></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label
                                                                for="razorpay_secret">{{ __('levels.razorpay_secret') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="razorpay_secret" id="razorpay_secret"
                                                                type="text"
                                                                class="form-control @error('razorpay_secret') is-invalid @enderror"
                                                                value="{{ old('razorpay_secret', setting('razorpay_secret') ?? '') }}">
                                                            @error('razorpay_secret')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                         <div class="tab-pane fade {{ (old('gateway2') == 'paystack') ? 'show active' : '' }}"
                                            id="paystacktab" role="tabpanel" aria-labelledby="paystack">
                                            <h4 class="paymentheader">{{ __('Paystack Setting') }}</h4>
                                            <form class="form-horizontal" role="form" method="POST"
                                                action="{{ route('admin.setting.payment.update') }}">
                                                @csrf
                                                <input type="hidden" name="gateway2" value="paystack">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="paystack_key">{{ __('Paystack Key') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="paystack_key" id="paystack_key" type="text"
                                                                class="form-control @error('paystack_key')is-invalid @enderror"
                                                                value="{{ old('paystack_key', setting('paystack_key') ?? '') }}">
                                                            @error('paystack_key')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <button
                                                                class="btn btn-primary"><span>{{ __('Update Paystack  Setting') }}</span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>





                <div class="tab-pane fade {{ (old('gateway') == 'email') ? 'show active' : '' }}" id="emailtab"
                    role="tabpanel" aria-labelledby="email">
                    {{--  <h4 class="paymentheader">{{ __('Email Setting') }}</h4> --}}


                    <form class="form-horizontal" role="form" method="POST"
                        action="{{ route('admin.setting.email.update') }}">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Email Settings') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_host">{{ __('levels.mail_host') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="mail_host" id="mail_host" type="text"
                                                class="form-control @error('mail_host') is-invalid @enderror"
                                                value="{{ old('mail_host', setting('mail_host')) }}">
                                            @error('mail_host')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mail_username">{{ __('levels.mail_username') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="mail_username" id="mail_username" type="text"
                                                class="form-control @error('mail_username') is-invalid @enderror"
                                                value="{{ old('mail_username', setting('mail_username')) }}">
                                            @error('mail_username')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mail_from_name">{{ __('levels.mail_from_name') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="mail_from_name" id="mail_from_name" type="text"
                                                class="form-control @error('mail_from_name') is-invalid @enderror"
                                                value="{{ old('mail_from_name', setting('mail_from_name')) }}">
                                            @error('mail_from_name')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_port">{{ __('levels.mail_port') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="mail_port" id="mail_port"
                                                class="form-control @error('mail_port') is-invalid @enderror"
                                                value="{{ old('mail_port', setting('mail_port')) }}">
                                            @error('mail_port')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mail_password">{{ __('levels.mail_password') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="mail_password" id="mail_password" type="text"
                                                class="form-control @error('mail_password') is-invalid @enderror"
                                                value="{{ old('mail_password', setting('mail_password')) }}">
                                            @error('mail_password')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mail_from_address">{{ __('levels.mail_from_address') }}</label>
                                            <span class="text-danger">*</span>
                                            <textarea name="mail_from_address" id="mail_from_address" cols="30" rows="2"
                                                class="form-control @error('mail_from_address') is-invalid @enderror">{{ old('mail_from_address', setting('mail_from_address')) }}</textarea>
                                            @error('mail_from_address')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_disabled">{{ __('levels.mail_disabled') }}</label> <span
                                                class="text-danger">*</span>
                                            <input name="mail_disabled" id="mail_disabled" type="checkbox"
                                                class="form-control {{ $errors->has('mail_disabled') ? ' has-error ' : '' }}"
                                                value="1" {{ setting('mail_disabled') == true ? "checked" : "" }}>
                                            @if ($errors->has('mail_disabled'))
                                            <span class="error-block">
                                                <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                                {{ $errors->first('mail_disabled') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm"><span>{{ __('Update Email Setting') }}</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>


                </div>


                <div class="tab-pane fade {{ (old('gateway') == 'notification') ? 'show active' : '' }}"
                    id="notificationtab" role="tabpanel" aria-labelledby="notification">
                    {{--  <h4 class="paymentheader">{{ __('Notification Setting') }}</h4> --}}


                    <form class="form-horizontal" role="form" method="POST"
                        action="{{ route('admin.setting.notification.update') }}">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Notification Settings') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="fcm_secret_key">{{ __('levels.firebase_secret_key') }}</label>
                                            <span class="text-danger">*</span>
                                            <input name="fcm_secret_key" id="fcm_secret_key" type="text"
                                                class="form-control @error('fcm_secret_key') is-invalid @enderror"
                                                value="{{ old('fcm_secret_key', setting('fcm_secret_key')) }}">
                                            @error('fcm_secret_key')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm"><span>{{ __('Update Notification Setting') }}</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>



                <div class="tab-pane fade {{ (old('gateway1') == 'social') ? 'show active' : '' }}" id="socialtab"
                    role="tabpanel" aria-labelledby="social">
                    {{--  <h4 class="paymentheader">{{ __('Social Setting') }}</h4> --}}


                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Social Login Setting') }}</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item"> <a
                                                class="nav-link {{ (old('gateway1') == 'facebook') ? 'active' : ((old('gateway1') == '') ? 'active' : '') }}"
                                                id="facebook" data-toggle="pill" href="#facebooktab" role="tab"
                                                aria-controls="facebooktab"
                                                aria-selected="true">{{ __('Facebook') }}</a>
                                        </li>
                                        <li class="nav-item"> <a
                                                class="nav-link {{ (old('gateway1') == 'google') ? 'active' : '' }}"
                                                id="google" data-toggle="pill" href="#googletab" role="tab"
                                                aria-controls="googletab" aria-selected="false">{{ __('Google') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card" id="settings-card">
                                <div class="card-body">
                                    <div class="tab-content" id="v-pills-tabContent">
                                        <div class="tab-pane fade {{ (old('gateway1') == 'facebook') ? 'show active' : ((old('gateway1') == '') ? 'show active' : '') }}"
                                            id="facebooktab" role="tabpanel" aria-labelledby="facebook">
                                            <h4 class="paymentheader">{{ __('Facebook Setting') }}</h4>
                                            <form class="form-horizontal" role="form" method="POST"
                                                action="{{ route('admin.setting.social.update') }}">
                                                @csrf
                                                <input type="hidden" name="gateway1" value="facebook">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="facebook_key">{{ __('Facebook Client ID') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="facebook_key" id="facebook_key" type="text"
                                                                class="form-control @error('facebook_key') is-invalid @enderror"
                                                                value="{{ old('facebook_key', setting('facebook_key') ?? '') }}">
                                                            @error('facebook_key')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label
                                                                for="facebook_secret">{{ __('Facebook Client Secret') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="facebook_secret" id="facebook_secret"
                                                                type="text"
                                                                class="form-control @error('facebook_secret') is-invalid @enderror"
                                                                value="{{ old('facebook_secret', setting('facebook_secret') ?? '') }}">
                                                            @error('facebook_secret')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="facebook_url">{{ __('Facebook url') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="facebook_url" id="facebook_url" type="text"
                                                                class="form-control @error('facebook_url') is-invalid @enderror"
                                                                value="{{ old('facebook_url', setting('facebook_url') ?? '') }}">
                                                            @error('facebook_url')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button
                                                            class="btn btn-primary"><span>{{ __('Update Facebook Setting') }}</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade {{ (old('gateway1') == 'google') ? 'show active' : '' }}"
                                            id="googletab" role="tabpanel" aria-labelledby="google">
                                            <h4 class="paymentheader">{{ __('Google Setting') }}</h4>
                                            <form class="form-horizontal" role="form" method="POST"
                                                action="{{ route('admin.setting.social.update') }}">
                                                @csrf
                                                <input type="hidden" name="gateway1" value="google">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="google_key">{{ __('Googel Client ID') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="google_key" id="google_key" type="text"
                                                                class="form-control @error('google_key')is-invalid @enderror"
                                                                value="{{ old('google_key', setting('google_key') ?? '') }}">
                                                            @error('google_key')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="google_secret">{{ __('Googel Client Sceret') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="google_secret" id="google_secret" type="text"
                                                                class="form-control @error('google_secret') is-invalid @enderror"
                                                                value="{{ old('google_secret', setting('google_secret') ?? '') }}">
                                                            @error('google_secret')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="google_url">{{ __('Googel Url') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input name="google_url" id="google_url" type="text"
                                                                class="form-control @error('google_url') is-invalid @enderror"
                                                                value="{{ old('google_url', setting('google_url') ?? '') }}">
                                                            @error('google_url')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button
                                                            class="btn btn-primary"><span>{{ __('Update Google  Setting') }}</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="tab-pane fade {{ (old('gateway') == 'otp') ? 'show active' : '' }}" id="otptab"
                    role="tabpanel" aria-labelledby="otp">

                    <form class="form-horizontal" role="form" method="POST"
                        action="{{ route('admin.setting.otp.update') }}">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('OTP Setting') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="otp_type_checking">{{ __('OTP Type') }}</label>
                                            <span class="text-danger">*</span>
                                            <select
                                                class="form-control @error('otp_type_checking') is-invalid @enderror"
                                                name="otp_type_checking" id="otp_type_checking">
                                                <option value="email"
                                                    {{ (old('otp_type_checking', setting('otp_type_checking')) == 'email') ? 'selected' : '' }}>
                                                    {{ __('Email')}} </option>
                                                <option value="phone"
                                                    {{ (old('otp_type_checking', setting('otp_type_checking')) == 'phone') ? 'selected' : '' }}>
                                                    {{ __('Phone') }}</option>
                                                <option value="both"
                                                    {{ (old('otp_type_checking', setting('otp_type_checking')) == 'both') ? 'selected' : '' }}>
                                                    {{ __('Both') }}</option>
                                            </select>
                                            @error('otp_type_checking')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="otp_digit_limit">{{ __('OTP Digit Limit') }}</label>
                                            <span class="text-danger">*</span>
                                            <select class="form-control @error('otp_digit_limit') is-invalid @enderror"
                                                name="otp_digit_limit" id="otp_digit_limit">
                                                <option value="4"
                                                    {{ (old('otp_digit_limit', setting('otp_digit_limit')) == 4) ? 'selected' : '' }}>
                                                    {{ __('4')}} </option>
                                                <option value="6"
                                                    {{ (old('otp_digit_limit', setting('otp_digit_limit')) == 6) ? 'selected' : '' }}>
                                                    {{ __('6') }}</option>
                                                <option value="8"
                                                    {{ (old('otp_digit_limit', setting('otp_digit_limit')) == 8) ? 'selected' : '' }}>
                                                    {{ __('8') }}</option>
                                            </select>
                                            @error('otp_digit_limit')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="otp_expire_time">{{ __('Expire Time (In Minute) ') }}</label>
                                            <span class="text-danger">*</span>
                                            <input name="otp_expire_time" id="otp_expire_time" type="number"
                                                class="form-control @error('otp_expire_time') is-invalid @enderror"
                                                value="{{ old('otp_expire_time', setting('otp_expire_time')) }}">
                                            @error('otp_expire_time')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm"><span>{{ __('Update Otp Setting') }}</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>

@endsection
