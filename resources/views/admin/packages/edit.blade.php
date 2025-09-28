@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Package</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/packages')}}">All Package
                                </a></li>
                        <li class="breadcrumb-item active">Edit Package</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Package</h3>
            </div>

            <form id="edit_form">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Hotel Type <span class="required">*</span></label>
                                <select name="name" id="name" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="3 Star Hotel">3 Star Hotel</option>
                                    <option value="4 Star Hotel">4 Star Hotel</option>
                                    <option value="5 Star Hotel">5 Star Hotel</option>
                                </select>
                                <span class="text-danger" id="name_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <select name="duration" id="duration" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <span class="text-danger" id="duration_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" name="price" class="form-control" id="price" required>
                                <span class="text-danger" id="price_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <span class="text-danger" id="status_error"></span>
                            </div>
                        </div>

                    </div>

                    <div class="form-group w-100 px-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('packages.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let token = "{{ session('api_token') }}";   // store your token in session when login
            let apiBaseUrl = '{{ config("app.api_base_url") }}';
            let packageId = "{{ request()->route('package') }}"; // from /packages/{id}/edit

            // 🔹 Fetch package data
            $.ajax({
                url: apiBaseUrl + 'api/v1/packages/view/' + packageId,
                type: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + token);
                },
                success: function(resp) {
                    if (resp.success) {
                        let data = resp.data;
                        $('#name').val(data.name);
                        $('#duration').val(data.duration);
                        $('#price').val(data.price);
                        $('#status').val(data.status);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong while fetching package data.');
                    window.location.href = "{{ route('packages.index') }}";
                }
            });

            // 🔹 Handle update form submit
            $('#edit_form').on('submit', function(e) {
                e.preventDefault();

                let formData = {
                    name: $('#name').val(),
                    duration: $('#duration').val(),
                    price: $('#price').val(),
                    status: $('#status').val()
                };

                $.ajax({
                    url: apiBaseUrl + 'api/v1/packages/update/' + packageId,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("Authorization", "Bearer " + token);
                    },
                    success: function(resp) {
                        toastr.success(resp.message || 'Package updated successfully');
                        setTimeout(() => {
                            window.location.href = "{{ route('packages.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) $('#name_error').text(errors.name[0]);
                            if (errors.duration) $('#duration_error').text(errors.duration[0]);
                            if (errors.price) $('#price_error').text(errors.price[0]);
                            if (errors.status) $('#status_error').text(errors.status[0]);
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    }
                });
            });
        });
    </script>
@endpush
