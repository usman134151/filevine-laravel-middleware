@extends('admin.admin_master')
@section('admin_content')
<div class="row">
    <div class="col-xl">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Company</h4>
                <br>
                {{-- @if ($error_message)
                    <p class="alert alert-danger">{{ $error_message }}</p>
                @endif
                @if ($success_message)
                <p class="alert alert-success">{{ $success_message }}</p>
            @endif --}}
                <form class="custom-validation" action="{{ route('store.company') }}" method="POST">
                    @csrf
                    <div class="mb-3 col-6">
                        <div class="mt-2">
                            <input type="text" class="form-control" name="client_name" required placeholder="Client Name"/>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="filevine_API_key" class="form-control" required 
                            placeholder="Filevine Key" />
                        </div>
                        <div class="mt-2">
                            <input type="text" name="filevine_API_secret" class="form-control" required 
                            placeholder="Filevine Secret" />
                        </div>
                        <div class="mt-2">
                            <input type="text" name="road_prof_API_key" class="form-control" required
                            placeholder="Road Proof Key"/>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection