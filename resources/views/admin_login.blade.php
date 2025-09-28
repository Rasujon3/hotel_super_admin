<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('back/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('back/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('back/dist/css/adminlte.min.css')}}">

  <link rel="stylesheet" href="{{asset('custom/toastr.css')}}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="{{URL::to('/')}}"><strong>Login</strong></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to your account</p>

      <form id="login-form">
       @csrf

        <div class="input-group mb-3">
          <input type="text" name="login" id="login" class="form-control" placeholder="Username" required="">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <span class="text-danger" id="login_error"></span>

        <div class="input-group mb-3">
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <span class="text-danger" id="password_error"></span>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>

          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
        <span class="text-danger" id="response_error"></span>
      </form>

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('back/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('back/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('back/dist/js/adminlte.min.js')}}"></script>

<script src="{{asset('custom/toastr.js')}}"></script>

@if(Session::has('message'))
    @toastr("{{ Session::get('message') }}")
@endif

<script>
$(document).ready(function() {
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('#login_error').text('');
        $('#password_error').text('');
        $('#response_error').text('');

        let login = $('#login').val();
        let password = $('#password').val();
        let apiBaseUrl = '{{ config("app.api_base_url") }}';

        $.ajax({
            url: apiBaseUrl + 'api/v1/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ login: login, password: password }),
            success: function(response) {
                if (response.success) {
                    if (response.data.user.user_type_id === '1' && response.data.user.role === 'super_admin') {
                        $.ajax({
                            url: '/store-api-token',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                token: response.data.token,
                                user: response.data.user
                            },
                            success: function(sessionResponse) {
                                if (sessionResponse.success) {
                                    window.location.href = '/dashboard';
                                } else {
                                    toastr.error('Failed to store session. Please try again.');
                                }
                            },
                            error: function() {
                                toastr.error('An error occurred while storing the session.');
                            }
                        });
                    } else {
                        toastr.error('You are not authorized to access this page.');
                    }
                } else {
                    if (response.errors) {
                        if (response.errors.login) {
                            $('#login_error').text(response.errors.login[0]);
                        }
                        if (response.errors.password) {
                            $('#password_error').text(response.errors.password[0]);
                        }
                    }
                    toastr.error(response.message || 'Login failed. Please check your credentials.');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.login) {
                        $('#login_error').text(errors.login[0]);
                    }
                    if (errors.password) {
                        $('#password_error').text(errors.password[0]);
                    }
                } else {
                    $('#response_error').text(xhr?.responseJSON?.message ?? 'Something went wrong, Please try again !!!');
                    toastr.error(xhr?.responseJSON?.message || 'Something went wrong, Please try again !!!');
                }
            }
        });
    });
});
</script>

</body>
</html>
