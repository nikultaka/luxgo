@extends('Admin.layouts.Dashboard.index')
@section('adminTitle', 'Admin | Manage Devices')
@section('breadcrumbFirst', 'Manage Devices')
@section('breadcrumbSecond', 'Devices')
{{-- @section('pageTitle', 'Devices List') --}}
@section('adminMainContent')

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">

            <div class="panel-heading">
                <div class="panel-title">Devices List</div>
                <div class="panel-options">
                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                    <a href="#" data-rel="close" class="bg"><i class="entypo-cancel"></i></a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
						<div style="float: right">
                            <button type="button" id="addNewDevices" class="btn btn-blue float-right"><i class="entypo-user-add"></i> Add Device</button>
                        </div>
						<br />
						<br />
						<br />

						<div class="table-responsive">
							<table class="table table-striped table-sm" id="devicesDataTable">
								<thead>
									<tr>
										<th>Device Id</th>
										<th>Device Name</th>
										<th style="min-width: 150px" class="table-action">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('Admin.devices.modal')
@endsection


@section('adminFooterSection')
    <script type="text/javascript" src="{{ asset('assets/admin/js/devices.js') }}"></script>
@endsection