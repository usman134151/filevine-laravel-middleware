@extends('admin.admin_master')
@section('admin_content')
    <div class="row">
        <div class="col-xl">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Profile</h4>
                    <br>
                    {{-- @if ($errors->any())
                        <div class="alert alert-danger col-6">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger col-6">{{ session('error') }}</div>
                    @endif     --}}

                    {{-- @if (session('success'))
                    <div class="alert alert-success col-6">{{ session('success') }}</div>
                    @endif    --}}

                    <form class="custom-validation" action="{{ route('store.update.profile') }}" method="POST">
                        {{-- <div class="mb-3">
                        <label>Required</label>
                        <input type="text" class="form-control" required placeholder="Type something"/>
                    </div> --}}
                        @csrf
                        <div class="mb-3 col-6">
                            <div class="mt-2">
                                <input type="text" class="form-control" name="name" required placeholder="Name" value="{{ $user->name }}"/>
                            </div>
                        </div>

                        {{-- <div class="mb-3">
                        <label>E-Mail</label>
                        <div>
                            <input type="email" class="form-control" required
                                    parsley-type="email" placeholder="Enter a valid e-mail"/>
                        </div>
                    </div> --}}
                        {{-- <div class="mb-3">
                        <label>URL</label>
                        <div>
                            <input parsley-type="url" type="url" class="form-control"
                                    required placeholder="URL"/>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Digits</label>
                        <div>
                            <input data-parsley-type="digits" type="text"
                                    class="form-control" required
                                    placeholder="Enter only digits"/>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Number</label>
                        <div>
                            <input data-parsley-type="number" type="text"
                                    class="form-control" required
                                    placeholder="Enter only numbers"/>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Alphanumeric</label>
                        <div>
                            <input data-parsley-type="alphanum" type="text"
                                    class="form-control" required
                                    placeholder="Enter alphanumeric value"/>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Textarea</label>
                        <div>
                            <textarea required class="form-control" rows="5"></textarea>
                        </div>
                    </div> --}}
                        <div class="mb-0">
                            <div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
