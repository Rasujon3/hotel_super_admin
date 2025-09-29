@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Package</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/packages')}}">All Package
                                </a></li>
                        <li class="breadcrumb-item active">Add Package</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Package</h3>
            </div>

            <form id="package-form">
                @csrf
                <div class="card-body">
                    <div class="row">

                        <!--
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Package Name <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Package Name" required>
                                <span class="text-danger" id="name_error"></span>
                            </div>
                        </div>
                        -->

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
                                <label for="duration">Duration <span class="required">*</span></label>
                                <select name="duration" id="duration" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="monthly">Monthly</option>
{{--                                    <option value="yearly">Yearly</option>--}}
                                </select>
                                <span class="text-danger" id="duration_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price <span class="required">*</span></label>
                                <input
                                    type="text"
                                    name="price"
                                    class="form-control numericInput"
                                    id="price"
                                    placeholder="Price"
                                    required
                                >
                                <span class="text-danger" id="price_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <span class="text-danger" id="status_error"></span>
                            </div>
                        </div>

                    </div>

                    <div class="form-group w-100 px-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </div>
            </form>
        </div>
    </section>
</div>

@endsection

@push('scripts')

  <script src="{{asset('custom/multiple_files.js')}}"></script>

  <script>
      $(document).ready(function() {
          let token = "{{ session('api_token') }}";
          let apiBaseUrl = '{{ config("app.api_base_url") }}';

          $('#package-form').on('submit', function(e) {
              e.preventDefault();

              // clear errors
              $('#name_error, #duration_error, #price_error, #status_error').text('');

              let form = $(this);
              let submitBtn = form.find('button[type="submit"]');

              submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

              let formData = {
                  name: $('#name').val(),
                  duration: $('#duration').val(),
                  price: $('#price').val(),
                  status: $('#status').val()
              };

              $.ajax({
                  url: apiBaseUrl + 'api/v1/packages/create',
                  type: 'POST',
                  contentType: 'application/json',
                  data: JSON.stringify(formData),
                  beforeSend: function(xhr) {
                      xhr.setRequestHeader("Authorization", "Bearer " + token);
                  },
                  success: function(resp) {
                      if(resp.success) {
                          toastr.success(resp.message || 'Package created successfully');
                          setTimeout(() => {
                              window.location.href = "{{ route('packages.index') }}";
                          }, 1500);
                      } else {
                          toastr.error(resp.message || 'Something went wrong');
                      }
                  },
                  error: function(xhr) {
                      if(xhr.status === 422) {
                          let errors = xhr.responseJSON.errors;
                          if(errors.name) $('#name_error').text(errors.name[0]);
                          if(errors.duration) $('#duration_error').text(errors.duration[0]);
                          if(errors.price) $('#price_error').text(errors.price[0]);
                          if(errors.status) $('#status_error').text(errors.status[0]);
                      } else {
                          toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                      }
                  },
                  complete: function() {
                      // always restore the button
                      submitBtn.prop('disabled', false).html('Submit');
                  }
              });
          });
      });
  </script>

@endpush
