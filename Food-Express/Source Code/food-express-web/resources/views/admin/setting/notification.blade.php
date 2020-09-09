@extends('admin.layouts.master')

@section('main-content')
	
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Notification Settings') }}</h1>
            {{ Breadcrumbs::render('notificationsetting') }}
        </div>

        <div class="section-body">
        	<form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.notification.update') }}" >
				@csrf
				<div class="card">
					<div class="card-header">
						<h4>{{ __('Notification Settings') }}</h4>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="fcm_secret_key">{{ __('Firebase Secret Key') }}</label> <span class="text-danger">*</span>
									<input name="fcm_secret_key" id="fcm_secret_key" type="text" class="form-control @error('fcm_secret_key') is-invalid @enderror" value="{{ old('fcm_secret_key', setting('fcm_secret_key')) }}">
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
								<button type="submit" class="btn btn-primary btn-sm"><span>{{ __('Update Notification Setting') }}</span></button>
							</div>
						</div>
					</div>
				</div>
			</form>
        </div>
    </section>
@endsection
