@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Admin Commision Report') }}</h1>
            {{ Breadcrumbs::render('admin-commission-report') }}
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    
                    <form action="<?=route('admin.admin-commission-report.index')?>" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('Shop') }}</label> <span class="text-danger">*</span>
                                    <select name="shop_id" class="form-control @error('shop_id') is-invalid @enderror">
                                        <option value="">{{ __('Select shop') }}</option>
                                        @if(!blank($shops))
                                            @foreach($shops as $shop)
                                                <option value="{{ $shop->id }}" {{ (old('shop_id', $set_shop_id) == $shop->id) ? 'selected' : '' }}>{{ $shop->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('shop_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('From Date') }}</label>
                                    <input type="text" name="from_date" class="form-control @error('from_date') is-invalid @enderror datepicker" value="{{ old('from_date', $set_from_date) }}">
                                    @error('from_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('To Date') }}</label>
                                    <input type="text" name="to_date" class="form-control @error('to_date') is-invalid @enderror datepicker" value="{{ old('to_date', $set_to_date) }}">
                                    @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="">&nbsp;</label>
                                <button class="btn btn-primary form-control" type="submit">Get Report</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
    
            @if($showView)
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Admin Commision Report') }}</h5>
                        <button class="btn btn-success btn-sm report-print-button" onclick="printDiv('printablediv')">{{ __('Print') }}</button>
                    </div>
                    <div class="card-body" id="printablediv">
                        @if(!blank($transactions))
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Order Code') }}</th>
                                            <th>{{ __('Shop Name') }}</th>
                                            <th>{{ __('Delivery Charge') }}</th>
                                            <th>{{ __('Sub Total') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Commision') }}</th>
                                        </tr>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->order->order_code }}</td>
                                                <td>{{ $transaction->shop->name }}</td>
                                                <td>{{ $transaction->order->delivery_charge }}</td>
                                                <td>{{ $transaction->order->sub_total }}</td>
                                                <td>{{ $transaction->order->total }}</td>
                                                <td>{{ $transaction->order->total-$transaction->amount }}</td>
                                            </tr>
                                        @endforeach
                                    </thead>
                                </table>
                            </div>
                        @else
                            <h4 class="text-danger">{{ __('The Shop order data not found') }}</h4>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/admincommission/index.js') }}"></script>
@endsection
