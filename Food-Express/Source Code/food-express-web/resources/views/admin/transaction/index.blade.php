@extends('admin.layouts.master')

@section('main-content')
  
  <section class="section">
        <div class="section-header">
            <h1>{{ __('Transactions') }}</h1>
            {{ Breadcrumbs::render('transaction') }}
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-8 offset-sm-2">
                                    <div class="input-group input-daterange" id="date-picker">
                                        <select class="form-control col-sm-3" id="user_id" name="user_id">
                                            <option value="">{{ __('Select User') }}</option>
                                            @if(!blank($users))
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <input autocomplete="off" class="form-control datepicker" id="form_date" type="text" name="form_date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}">
                                        <input autocomplete="off" class="form-control datepicker" id="to_date" type="text" name="to_date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="refresh"> {{ __('Refresh') }}</button>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="get-search">{{ __('Search') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-striped" id="maintable" data-url="{{ route('admin.transaction.get-transaction') }}">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Amount') }}</th>
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
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/transaction/index.js') }}"></script>
@endsection