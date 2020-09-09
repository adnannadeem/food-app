@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('SMS Settings') }}</h1>
            {{ Breadcrumbs::render('sms-setting') }}
        </div>

        <div class="section-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.sms.update') }}">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('SMS Settings') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="twilio_auth_token">{{ __('Twilio Auth Token') }}</label> <span class="text-danger">*</span>
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
                                    <label for="twilio_account_sid">{{ __('Twilio Account SID') }}</label> <span class="text-danger">*</span>
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
                                    <label for="twilio_from">{{ __('Twilio From') }}</label> <span class="text-danger">*</span>
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
                                    <label for="twilio_disabled">{{ __('Disabled') }}</label> <span class="text-danger">*</span>
                                    <input name="twilio_disabled" id="twilio_disabled" type="checkbox"
                                           class="form-control {{ $errors->has('twilio_disabled') ? ' has-error ' : '' }}"
                                           value="{{ setting('twilio_disabled') == true ? 1 : 0 }}" {{ setting('twilio_disabled') == true ? "checked" : "" }}>
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
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
