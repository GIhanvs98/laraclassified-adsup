@extends('admin.layouts.master')

@section('header')
	<div class="row page-titles">
		<div class="col-md-6 col-12 align-self-center">
			<h2 class="mb-0">
				<span class="text-capitalize">Memberships</span>
				<small id="tableInfo">Showing {{ $memberships->firstItem() }} to {{ $memberships->lastItem() }} of {{ $memberships->total() }} entries</small>
			</h2>
		</div>
		<div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
			<ol class="breadcrumb mb-0 p-0 bg-transparent">
				<li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
				<li class="breadcrumb-item"><a href="{{ admin_url('settings') }}" class="text-capitalize">Settings</a></li>
				<li class="breadcrumb-item active d-flex align-items-center">Memberships</li>
			</ol>
		</div>
	</div>
@endsection

@section('content')
	<div class="row">
		<div class="col-12">

			<div class="card rounded">
				
                <div class="card-body">
                    
                    <div id="loadingData"></div>

                    @if(isset($memberships))

                        @csrf

                        <div class="controlls" style="display: flex; margin-bottom: 20px;">
                            
                            <a href="{{ route('memberships.create') }}" style="margin-right: 5px;">
                                <button class="btn btn-primary shadow ladda-button">
                                    <i class="fas fa-plus"></i> Add Membership
                                </button>
                            </a>

                            <button style="margin-right: 5px;" id="activate-select" class="bulk-action btn btn-outline-secondary shadow">
                                <i class="fa fa-toggle-on"></i> Activate
                            </button>

                            <button style="margin-right: 5px;" id="deactivate-select" class="bulk-action btn btn-outline-secondary shadow">
                                <i class="fa fa-toggle-off"></i> Disable
                            </button>

                            <button style="margin-right: 5px;" id="delete-select" class="bulk-action btn btn-danger shadow">
                                <i class="fas fa-times"></i> Delete
                            </button>

                        </div>
                        
                        <table class="dataTable table table-bordered table-striped display dt-responsive nowrap" style="width:100%">
                            <tr>
                                <th>
                                    <input type="checkbox" name="select-all" id="select-all">
                                </th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Description</th>
                                <th>Allowed ads</th>
                                <th>Allowed pictures</th>
                                <th>Giveaway packages</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>

                            @foreach ($memberships as $membership)
                                <tr class="membership-item" membership-id="{{ $membership->id }}">
                                    <td>
                                        <input type="checkbox" class="select-membership" membership-id="{{ $membership->id }}">
                                    </td>
                                    <td>
                                        <a href="{{ route('memberships.edit', ['membership' => $membership->id]) }}">{{ $membership->name }}</a>
                                    </td>
                                    <td>
                                        @switch($membership->name)
                                            @case("Verified Seller")
                                                {!! $membership->icon !!}
                                                @break
                                            @default
                                                <div style="width: 18px;">{!! $membership->icon !!}</div>
                                        @endswitch
                                    </td>
                                    <td>{{ $membership->description }}</td>
                                    <td>
                                        @if(isset($membership->allowed_ads))
                                            {{ $membership->allowed_ads }}
                                        @else
                                            &#8734;
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($membership->allowed_pictures))
                                            {{ $membership->allowed_pictures }}
                                        @else
                                            &#8734;
                                        @endif
                                    </td>
                                    <td style="font-size: 12px;">
                                        @php
                                            $giveaway_packages = json_decode($membership->giveaway_packages, true);
                                        @endphp
                                        @if(isset($giveaway_packages) && count($giveaway_packages) > 0)
                                            @foreach($giveaway_packages as $key => $giveaway_package)
                                                @php
                                                    $package = \App\Models\Package::find($giveaway_package['id']);
                                                    $count = $giveaway_package['count'];
                                                @endphp
                                                <div style="margin-bottom: 8px;">
                                                    {{ $package->name }} {{ $package->short_name }} - {{ $count }} packages
                                                </div>
                                            @endforeach
                                        @else
                                            none
                                        @endif
                                    </td>
                                    <td>{{ $membership->currency->symbol }}.{{ $membership->amount }}</td>
                                    <td>
                                        <input type="checkbox" class="membership-status" membership-id="{{ $membership->id }}" url="{{ route('memberships.update', ['membership' => $membership->id]) }}" {{ ($membership->active == 1) ? "checked" : "" }}>
                                    </td>
                                    <td style="width: 16%;">
                                        <a href="{{ route('memberships.edit', ['membership' => $membership->id]) }}" style="margin-right: 5px;">
                                            <button style="margin-right: 5px; display:inline-block;" class="btn btn-xs btn-primary">
                                                <i class="bi bi-pen"></i> Edit
                                            </button>
                                        </a>

                                        <button style="display:inline-block;" class="btn btn-xs btn-danger destroy-memberships" membership-id="{{ $membership->id }}" url="{{ route('memberships.destroy', ['membership' => $membership->id]) }}">
                                            <i class="fas fa-times"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </table>

                        <div class="flex justify-between align-middle">
                            <div style="padding-top: 0.85em;">Showing {{ $memberships->firstItem() }} to {{ $memberships->lastItem() }} of {{ $memberships->total() }} entries.</div>
                            <div>{{ $memberships->links() }}</div>
                        </div>

                    @else
                        <div>No membership records. <a href="{{ route('memberships.create') }}">Create Now!</a></div>
                    @endif

                </div>
                
            </div>
            
        </div>
    </div>
@endsection

@section('after_styles')
<style>

    .flex{
        display: flex;
    }

    .justify-between{
        justify-content: space-between;
    }

    .align-middle{
        vertical-align: middle;
    }

</style>
@endsection

@section('after_scripts')
    <script>
        $(function(){

            $("#select-all").change(function(){
                $(".select-membership").prop("checked", this.checked);
            });


            $("#activate-select").click(function(){

                let memberships = [];

                $(".select-membership:checked").each(function(index, element) {
                    memberships[index] = $(element).attr("membership-id");
                });

                let url = "{{ route('admin.user.memberships.mass-update', ['function'=>'activate']) }}";

                let csrfToken = $('input[name=_token]').val();

                let dataObj = {
                    '_token': csrfToken,
                    'memberships': memberships,
                };
                
                let ajax = $.ajax({
                    method: 'PUT',
                    url: url,
                    data: dataObj,
                    beforeSend: function() {
                        //
                    }
                });
                ajax.done(function (xhr) {
                    
                    let output = JSON.parse(JSON.stringify(xhr));

                    memberships.forEach(element => {
                        $(".membership-status[membership-id="+element+"]").prop("checked", true);
                    });

                    $(".select-membership").prop("checked", false);
                    $("#select-all").prop("checked", false);

                    if(output.success == false){
                        console.log("Server error: Status do not updated.");
                    }
                });
                ajax.fail(function(xhr) {
                    console.log("Server error: Status do not updated.");
                });
                
            });

            $("#deactivate-select").click(function(){
                
                let memberships = [];

                $(".select-membership:checked").each(function(index, element) {
                    memberships[index] = $(element).attr("membership-id");
                });

                let url = "{{ route('admin.user.memberships.mass-update', ['function'=>'deactivate']) }}";

                let csrfToken = $('input[name=_token]').val();

                let dataObj = {
                    '_token': csrfToken,
                    'memberships': memberships,
                };
                
                let ajax = $.ajax({
                    method: 'PUT',
                    url: url,
                    data: dataObj,
                    beforeSend: function() {
                        //
                    }
                });
                ajax.done(function (xhr) {
                    
                    let output = JSON.parse(JSON.stringify(xhr));

                    memberships.forEach(element => {
                        $(".membership-status[membership-id="+element+"]").prop("checked", false);
                    });

                    $(".select-membership").prop("checked", false);
                    $("#select-all").prop("checked", false);

                    if(output.success == false){
                        console.log("Server error: Status do not updated.");
                    }
                });
                ajax.fail(function(xhr) {
                    console.log("Server error: Status do not updated.");
                });
                
            });

            $("#delete-select").click(function(){
                
                let memberships = [];

                $(".select-membership:checked").each(function(index, element) {
                    memberships[index] = $(element).attr("membership-id");
                });

                let url = "{{ route('admin.user.memberships.mass-update', ['function'=>'delete']) }}";

                let csrfToken = $('input[name=_token]').val();

                let dataObj = {
                    '_token': csrfToken,
                    'memberships': memberships,
                };
                
                let ajax = $.ajax({
                    method: 'PUT',
                    url: url,
                    data: dataObj,
                    beforeSend: function() {
                        //
                    }
                });
                ajax.done(function (xhr) {
                    
                    let output = JSON.parse(JSON.stringify(xhr));

                    memberships.forEach(element => {
                        $(".membership-item[membership-id="+element+"]").remove();
                    });

                    $(".select-membership").prop("checked", false);
                    $("#select-all").prop("checked", false);

                    if(output.success == false){
                        console.log("Server error: Status do not updated.");
                    }
                });
                ajax.fail(function(xhr) {
                    console.log("Server error: Status do not updated.");
                });
                
            });

            $(".membership-status").click(function(){
                
                let elem = $(this);
                let url = $(this).attr("url");

                let csrfToken = $('input[name=_token]').val();

                let dataObj = {
                    '_token': csrfToken,
                    'status': elem.prop("checked"),
                    'statusUpdate': 'statusUpdate',
                };
                
                let ajax = $.ajax({
                    method: 'PUT',
                    url: url,
                    data: dataObj,
                    beforeSend: function() {
                        //
                    }
                });
                ajax.done(function (xhr) {
                    
                    let output = JSON.parse(JSON.stringify(xhr));
                    
                    elem.prop("checked", output.data.status);

                    if(output.success == false){
                        console.log("Server error: Status do not updated.");
                    }
                });
                ajax.fail(function(xhr) {
                    console.log("Server error: Status do not updated.");
                });
                
            });


            /* Individual actions */
            $(".destroy-memberships").click(function(){

                let url = $(this).attr("url");
                let elem = $(this);

                let csrfToken = $('input[name=_token]').val();

                let dataObj = {
                    '_token': csrfToken
                };
                
                let ajax = $.ajax({
                    method: 'DELETE',
                    url: url,
                    data: dataObj,
                    beforeSend: function() {
                        //
                    }
                });
                ajax.done(function (xhr) {
                    
                    let output = JSON.parse(JSON.stringify(xhr));

                    $(".membership-item[membership-id="+elem.attr("membership-id")+"]").remove();

                    if(output.success == false){
                        console.log("Server error: Membership do not deleted.");
                    }
                });
                ajax.fail(function(xhr) {
                    console.log("Server error: Membership do not deleted.");
                });
                
            });
        });
    </script>
@endsection
