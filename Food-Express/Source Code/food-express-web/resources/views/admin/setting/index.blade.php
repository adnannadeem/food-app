@extends('admin.layouts.master')

@section('main-content')
	
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Settings') }}</h1>
            {{ Breadcrumbs::render('setting') }}
        </div>

        <div class="section-body">
        	<form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.update') }}" enctype="multipart/form-data">
				@csrf
				<div class="card">
					<div class="card-header">
						<h4>{{ __('Site Setting') }}</h4>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="site_name">{{ __('levels.site_name') }}</label> <span class="text-danger">*</span>
									<input name="site_name" id="site_name" type="text" class="form-control @error('site_name') is-invalid @enderror" value="{{ old('site_name', setting('site_name')) }}">
									@error('site_name')
										<div class="invalid-feedback">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="currency_name">{{ __('levels.currency_name') }}</label> <span class="text-danger">*</span>
									<input name="currency_name" id="currency_name" type="text" class="form-control @error('currency_name') is-invalid @enderror" value="{{ old('currency_name', setting('currency_name')) }}">
									@error('currency_name')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="geolocation_distance_radius">{{ __('levels.geolocation_distance_radius') }}</label> <span class="text-danger">*</span>
									<input name="geolocation_distance_radius" id="geolocation_distance_radius" type="text" class="form-control @error('geolocation_distance_radius') is-invalid @enderror" value="{{ old('geolocation_distance_radius', setting('geolocation_distance_radius')) }}">
									@error('geolocation_distance_radius')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="site_description">{{ __('levels.description') }}</label> <span class="text-danger">*</span>
									<textarea name="site_description" id="site_description" cols="30" rows="3" class="form-control small-textarea-height @error('site_description') is-invalid @enderror">{{ old('site_description', setting('site_description')) }}</textarea>
									@error('site_description')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="customFile">{{ __('levels.site_logo') }}</label>
                                        <div class="custom-file">
                                            <input name="site_logo" type="file" class="custom-file-input @error('site_logo') is-invalid @enderror" id="customFile" onchange="readURL(this);">
                                            <label  class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                        @error('site_logo')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        @if(setting('site_logo'))
                                        	<img class="img-thumbnail image-width mt-4 mb-3" id="previewImage" src="{{ asset('images/'.setting('site_logo')) }}" alt="Food Express Logo"/>
                                        @else
                                        	<img class="img-thumbnail image-width mt-4 mb-3" id="previewImage" src="{{ asset('images/logo.png') }}" alt="Food Express Logo"/>
                                        @endif
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label for="site_footer">{{ __('levels.site_footer') }}</label> <span class="text-danger">*</span>
									<input name="site_footer" id="site_footer" class="form-control @error('site_footer') is-invalid @enderror" value="{{ old('site_footer', setting('site_footer')) }}">
									@error('site_footer')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="currency_code">{{ __('levels.currency_code') }}</label> <span class="text-danger">*</span>
									<input name="currency_code" id="currency_code" type="text" class="form-control @error('currency_code') is-invalid @enderror" value="{{ old('currency_code', setting('currency_code')) }}">
									@error('currency_code')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="order_commission_percentage">{{ __('levels.order_commission_percentage') }}</label> <span class="text-danger">*</span>
									<input name="order_commission_percentage" id="order_commission_percentage" type="number" min="0"  max="100" class="form-control @error('order_commission_percentage') is-invalid @enderror" value="{{ old('order_commission_percentage', setting('order_commission_percentage')) }}">
									@error('order_commission_percentage')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>
								
								<div class="form-group">
									<label for="customer_terms_condition">{{ __('levels.customer_terms_condition') }}</label> <span class="text-danger">*</span>
									<textarea name="customer_terms_condition" id="customer_terms_condition" cols="30" rows="3" class="form-control small-textarea-height summernote @error('customer_terms_condition') is-invalid @enderror">{{ old('customer_terms_condition', setting('customer_terms_condition')) }}</textarea>
									@error('customer_terms_condition')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="shopowner_terms_condition">{{ __('levels.shop_owner_terms_condition') }}</label> <span class="text-danger">*</span>
									<textarea name="shopowner_terms_condition" id="shopowner_terms_condition" cols="30" rows="3" class="form-control summernote small-textarea-height @error('shopowner_terms_condition') is-invalid @enderror">{{ old('shopowner_terms_condition', setting('shopowner_terms_condition')) }}</textarea>
									@error('shopowner_terms_condition')
									<div class="invalid-feedback">
										<strong>{{ $message }}</strong>
									</div>
									@enderror
								</div>

								<div class="form-group">
									<label for="timezone">{{ __('levels.timezone') }}</label> <span class="text-danger">*</span>
									<?php
										$className = 'form-control';
										if($errors->first('timezone')) {
											$className = 'form-control is-invalid';
										}
										echo Timezonelist::create('timezone', setting('timezone') , ['class'=> $className]);
									?>
									@error('timezone')
										<div class="invalid-feedback">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
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

@section('scripts')
    <script src="{{ asset('js/setting/index.js') }}"></script>
@endsection