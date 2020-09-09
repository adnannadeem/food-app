@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Updates') }}</h1>
            {{ Breadcrumbs::render('updates') }}
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="progress-box" class="progress display-none">
                                        <div id="dynamic" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <span id="current-progress"></span>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>

                            <div class="row">
                                <div id="update-box" class="col-sm-12 d-none">
                                    <div id="check-message"></div>
                                    <br>
                                    <button data-url="{{ route('admin.updates.update') }}" id="click-update" class="btn btn-icon icon-left btn-primary">
                                        <i class="fas fa-arrow-circle-up"></i> {{ __('Update') }}</button>
                                    <button id="cancel-update" class="btn btn-icon icon-left btn-danger">
                                        <i class="fas fa-times-circle "></i> {{ __('Cancel') }}</button>
                                </div>
                                <div class="col-sm-12">
                                    <button data-url="{{ route('admin.updates.checking-updates') }}" id="check-update" class="btn btn-icon icon-left btn-primary">
                                        <i class="fas fa-arrow-circle-up"></i> {{ __('Check Update') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="main-table"  data-url="{{ route('admin.updates.get-updates') }}">
                                    <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
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

    <div class="modal" tabindex="-1" role="dialog" id="fire-modal-1">
        <button class="btn btn-primary trigger--fire-modal-1 display-none" id="modal-1">x</button>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update Log') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body modal-data-info">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/bootstrap-modal.js') }}"></script>
    <script src="{{ asset('js/update/index.js') }}"></script>
@endsection
