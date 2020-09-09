@extends('admin.layouts.master')

@section('main-content')
	
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Email Settings') }}</h1>
            {{ Breadcrumbs::render('emailsetting') }}
        </div>

        <div class="section-body">
        	<form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.email.update') }}" >
				@csrf
				<div class="card">
					<div class="card-header">
						<h4>{{ __('Email Settings') }}</h4>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="mail_host">{{ __('Mail Host') }}</label> <span class="text-danger">*</span>
									<input name="mail_host" id="mail_host" type="text" class="form-control @error('mail_host') is-invalid @enderror" value="{{ old('mail_host', setting('mail_host')) }}">
									@error('mail_host')
										<div class="invalid-feedback">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="mail_username">{{ __('Mail Username') }}</label> <span class="text-danger">*</span>
									<input name="mail_username" id="mail_username" type="text" class="form-control @error('mail_username') is-invalid @enderror" value="{{ old('mail_username', setting('mail_username')) }}">
									@error('mail_username')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="mail_from_name">{{ __('Mail From Name') }}</label> <span class="text-danger">*</span>
									<input name="mail_from_name" id="mail_from_name" type="text" class="form-control @error('mail_from_name') is-invalid @enderror" value="{{ old('mail_from_name', setting('mail_from_name')) }}">
									@error('mail_from_name')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="mail_port">{{ __('Mail Port') }}</label> <span class="text-danger">*</span>
									<input name="mail_port" id="mail_port" class="form-control @error('mail_port') is-invalid @enderror" value="{{ old('mail_port', setting('mail_port')) }}">
									@error('mail_port')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="mail_password">{{ __('Mail Password') }}</label> <span class="text-danger">*</span>
									<input name="mail_password" id="mail_password" type="text" class="form-control @error('mail_password') is-invalid @enderror" value="{{ old('mail_password', setting('mail_password')) }}">
									@error('mail_password')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="mail_from_address">{{ __('Mail From Address') }}</label> <span class="text-danger">*</span>
									<textarea name="mail_from_address" id="mail_from_address" cols="30" rows="2" class="form-control @error('mail_from_address') is-invalid @enderror">{{ old('mail_from_address', setting('mail_from_address')) }}</textarea>
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
                                    <label for="mail_disabled">{{ __('Mail Disabled') }}</label> <span class="text-danger">*</span>
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
								<button type="submit" class="btn btn-primary btn-sm"><span>{{ __('Update Email Setting') }}</span></button>
							</div>
						</div>
					</div>
				</div>
			</form>
        </div>
    </section>
@endsection
