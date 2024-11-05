@extends('admin.admin_master')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Companies</h4>
            <div class="mb-3">
                <a href="{{ route('add.company') }}" class="btn btn-primary">Add Company</a>
            </div>

                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Filevine API Key</th>
                        <th>Filevine Secret Key</th>
                        <th>Road Proof API Key</th>
                    </tr>
                    </thead>


                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->client_name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($user->filevine_API_key, 20) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($user->filevine_API_secret, 20) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($user->roadProof_API_key, 20) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection