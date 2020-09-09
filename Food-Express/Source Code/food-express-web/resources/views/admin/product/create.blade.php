@extends('admin.layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
@endsection

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Product') }}</h1>
            {{ Breadcrumbs::render('products/add') }}
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                        <input id="name" type="text" name="name" class="form-control {{ $errors->has('name') ? " is-invalid " : '' }}" value="{{ old('name') }}">
                                        @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col {{ $errors->has('categories') ? " has-error " : '' }}">
                                        <label for="categories">{{ __('levels.categories') }}</label>
                                        <select id="categories" name="categories[]" class="form-control select2 {{ $errors->has('categories') ? " is-invalid " : '' }}" multiple="multiple">
                                            <option value="">{{ __('Select Categories') }}</option>
                                            @if(!blank($categories))
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('categories'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('categories') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label for="unit_price">{{ __('levels.mrp') }}</label> <span class="text-danger">*</span>
                                        <input id="unit_price" type="text" name="unit_price" class="form-control {{ $errors->has('unit_price') ? " is-invalid " : '' }}" value="{{ old('name') }}">
                                        @error('unit_price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group col">
                                        <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                            @foreach(trans('statuses') as $key => $status)
                                                <option value="{{ $key }}" {{ (old('status') == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label for="description">{{ __('levels.description') }}</label>
                                        <textarea name="description"
                                                  class="summernote-simple form-control height-textarea @error('description')
                                                  is-invalid @enderror"
                                                  id="description" >
                                            {{ old('description') }}
                                        </textarea>
                                        @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col {{ $errors->has('document') ? 'has-error' : '' }}">
                                        <label for="document"> {{ __('Image') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="needsclick dropzone {{ $errors->has('document') ? ' is-invalid' : '' }}" id="document-dropzone"></div>
                                        @error('document')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>


                                </div>
                            </div>

                            <div class="card-footer ">
                                <button class="btn btn-primary mr-1" type="submit">{{ __('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/product/create.js') }}"></script>


    <script type="text/javascript">
        var uploadedDocumentMap = {};
        Dropzone.options.documentDropzone = {
            url: '{{ route('admin.products.storeMedia') }}',
            maxFilesize: 2, // MB
            maxFiles:5,
            acceptedFiles: "image/jpeg, image/png, image/jpg",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
                uploadedDocumentMap[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove();
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDocumentMap[file.name]
                }
                $('form').find('input[name="document[]"][value="' + name + '"]').remove()
            },
            init: function () {
                    @if(isset($product) && $product->products)
                var files =
                {!! json_encode($product->products) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
                }
                @endif
            }
        }
    </script>

@endsection
