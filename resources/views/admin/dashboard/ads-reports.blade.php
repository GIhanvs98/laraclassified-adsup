@extends('admin.layouts.master')

@section('header')
	<div class="row page-titles">
		<div class="col-md-6 col-12 align-self-center">
			<h2 class="mb-0">
				<span class="text-capitalize">Ads Reports</span>
				<small id="tableInfo">Showing results from  {{ $count }} entries</small>
			</h2>
		</div>
		<div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
			<ol class="breadcrumb mb-0 p-0 bg-transparent">
				<li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
				<li class="breadcrumb-item active d-flex align-items-center">Ads Reports</li>
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

                    <livewire:admin-report-ads />

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
