@extends('admin.layouts.master')

@section('header')
<div class="row page-titles">
    <div class="col-md-6 col-12 align-self-center">
        <h2 class="mb-0">
            <span class="text-capitalize">User Memberships</span>
            <small id="tableInfo">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries</small>
        </h2>
    </div>
    <div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
        <ol class="breadcrumb mb-0 p-0 bg-transparent">
            <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ admin_url('user') }}" class="text-capitalize">User</a></li>
            <li class="breadcrumb-item active d-flex align-items-center">User Memberships</li>
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

                @if(isset($users))

                @csrf

                <table class="dataTable table table-bordered table-striped display dt-responsive nowrap" style="width:100%">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone number</th>
                        <th>Membership</th>
                    </tr>

                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            {{--<select class="form-select select-user-membership" user-id="{{ $user->id }}" url="{{ route('admin.user.memberships.update', ['user' => $user->id]) }}">
                            @forelse($memberships as $key => $membership)
                            @if(isset($user->membership->id))
                            @if( $user->membership->id == $membership->id )
                            <option selected value="{{ $membership->id }}">{{ $membership->name }}</option>
                            @else
                            <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                            @endif
                            @else
                            <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                            @endif
                            @empty
                            <option value="">No memberships</option>
                            @endforelse
                            </select>--}}
                            {{ $user->membership->name }}
                        </td>
                    </tr>
                    @endforeach

                </table>

                <div class="flex justify-between align-middle">
                    <div style="padding-top: 0.85em;">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries.</div>
                    <div>{{ $users->links() }}</div>
                </div>

                @else
                <div>No user records. <a href="{{ route('users.index') }}">Create Now!</a></div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection

@section('after_styles')
<style>
    .flex {
        display: flex;
    }

    .justify-between {
        justify-content: space-between;
    }

    .align-middle {
        vertical-align: middle;
    }

</style>
@endsection

@section('after_scripts')
<script>
    $(function() {
        $('.select-user-membership').on('change', function() {

            let elem = $(this);
            let url = $(this).attr("url");

            let csrfToken = $('input[name=_token]').val();

            let dataObj = {
                '_token': csrfToken
                , 'membership': elem.val()
            , };

            let ajax = $.ajax({
                method: 'PUT'
                , url: url
                , data: dataObj
                , beforeSend: function() {
                    //
                }
            });
            ajax.done(function(xhr) {

                let output = JSON.parse(JSON.stringify(xhr));

                elem.eq(output.data.membership).prop('selected', true)

                if (output.success == false) {
                    console.log("Server error: Membership do not updated.");
                }
            });
            ajax.fail(function(xhr) {
                console.log("Server error: Membership do not updated.");
            });

        });
    });

</script>
@endsection
