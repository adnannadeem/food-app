@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Payment Settings') }}</h1>
            {{ Breadcrumbs::render('payment-setting') }}
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Payment Gateway') }}</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item"> <a class="nav-link {{ (old('gateway') == 'stripe') ? 'active' : ((old('gateway') == '') ? 'active' : '') }}" id="stripe" data-toggle="pill" href="#stripetab" role="tab" aria-controls="stripetab" aria-selected="true">{{ __('Stripe') }}</a></li>
                            <li class="nav-item"> <a class="nav-link {{ (old('gateway') == 'razorpay') ? 'active' : '' }}" id="razorpay" data-toggle="pill" href="#razorpaytab" role="tab" aria-controls="razorpaytab" aria-selected="false">{{ __('Razorpay') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card" id="settings-card">
                    <div class="card-body">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade {{ (old('gateway') == 'stripe') ? 'show active' : ((old('gateway') == '') ? 'show active' : '') }}" id="stripetab" role="tabpanel" aria-labelledby="stripe">
                                <h4 class="paymentheader">{{ __('Stripe Setting') }}</h4>
                                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.payment.update') }}">
                                    @csrf
                                    <input type="hidden" name="gateway" value="stripe">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="stripe_key">{{ __('Stripe Key') }} <span
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
                                                <button class="btn btn-primary"><span>{{ __('Update Stripe Setting') }}</span></button>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="stripe_secret">{{ __('Stripe Secret') }} <span
                                                        class="text-danger">*</span></label>
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

                            <div class="tab-pane fade {{ (old('gateway') == 'razorpay') ? 'show active' : '' }}" id="razorpaytab" role="tabpanel" aria-labelledby="razorpay">
                                <h4 class="paymentheader">{{ __('Razorpay Setting') }}</h4>
                                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.payment.update') }}">
                                    @csrf
                                    <input type="hidden" name="gateway" value="razorpay">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="razorpay_key">{{ __('Razorpay  Key') }} <span class="text-danger">*</span></label>
                                                <input name="razorpay_key" id="razorpay_key" type="text" class="form-control @error('razorpay_key')is-invalid @enderror" value="{{ old('razorpay_key', setting('razorpay_key') ?? '') }}">
                                                @error('razorpay_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-primary"><span>{{ __('Update Razorpay  Setting') }}</span></button>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="razorpay_secret">{{ __('Razorpay Secret') }}
                                                    <span class="text-danger">*</span></label>
                                                <input name="razorpay_secret" id="razorpay_secret" type="text" class="form-control @error('razorpay_secret') is-invalid @enderror" value="{{ old('razorpay_secret', setting('razorpay_secret') ?? '') }}">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
