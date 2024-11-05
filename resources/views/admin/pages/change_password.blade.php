@extends('admin.admin_master')
@section('admin_content')
    <div class="row">
        <div class="col-xl">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Change Password</h4>
                    <br>
                    <form class="custom-validation" action="{{ route('store.new.password') }}" method="POST">
                        @csrf
                        <div class="mb-3 col-6">
                            <div class="mt-2">
                                <input type="password" class="form-control" name="current_password" required placeholder="Old Password"/>
                            </div>
                            <div class="mt-2">
                                <input type="password" name="password" id="pass2" class="form-control" required
                                placeholder="New Password"/>
                            </div>
                            <div class="mt-2">
                                <input type="password" name="password_confirmation" class="form-control" required data-parsley-equalto="#pass2"
                                    placeholder="Re-Type Password" />
                            </div>
                        </div>
                        <div class="mb-0">
                            <div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                    Submit
                                </button>
                                <button type="reset" class="btn btn-secondary waves-effect">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
