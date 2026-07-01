<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Tab Icon -->
    <link rel="shortcut icon" href="{{tab_icon()}}">

    <!-- Title -->
    <title>{{App_Name()}}</title>

    <link href="{{asset('/assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{asset('/assets/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{asset('/assets/css/toastr.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('/assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <div class="h-100">
        <div class="h-100 no-gutters row">
            <div class="d-none d-lg-block h-100 col-lg-5 col-xl-4">
                <div class="left-caption">
                   <!-- <img src="{{asset('assets/imgs/login.jpg')}}" class="bg-img" /> -->
                    <div class="caption">
                        <div>
                            <!-- {{App_Name()}} -->
                            <h1> <img src="https://ishivaxservices.com/admin_panel/public/images/app/logo.png" width="80%" /></h1>
                       
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-100 d-flex login-bg justify-content-center align-items-lg-center col-md-12 col-lg-7 col-xl-8">
                <div class="mx-auto col-sm-12 col-md-10 col-xl-8">
                    <div class="py-5 p-3">

                        <div class="app-logo mb-4">
                           
                            <a class="mb-4 d-block d-lg-none">
                                <h3 class="text-white mb-0 font-weight-bold">{{App_Name()}}</h3>
                            </a>

                            <h3 class="text-white mb-0 font-weight-bold">Login</h3>
                        </div>

                        <h4 class="mb-0 font-weight-bold text-white">
                            <span class="d-block mb-2">Welcome back,</span>
                            <span>Please sign in to your account.</span>
                        </h4>

                        <form id="save_login">
                            @csrf
                            <div class="form-row mt-4">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label>Email</label>
                                        <input name="email" placeholder="Email here..." type="email" class="form-control" value="" required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label>Password</label>
                                        <input name="password" placeholder="Password here..." type="password" class="form-control" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox mr-sm-2">
                                <input type="checkbox" class="custom-control-input" name="remember" id="customControlAutosizing">
                                <label class="custom-control-label" for="customControlAutosizing">Keep me logged in</label>
                            </div>

                            <div class="form-row mt-4">
                                <div class="col-sm-6 text-center text-sm-left">
                                    <button class="btn btn-default my-3 mw-120" onclick="save_login()" type="button">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/js.js') }}"></script>
    <script src="{{ asset('/assets/js/toastr.min.js')}}"></script>
    <script>
        // Login Form
        function save_login() {
            var formData = new FormData($("#save_login")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("adminLoginPost") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'save_login', '{{ route("dashboard") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
        // Toastr
        function get_responce_message(resp, form_name, url) {
            if (resp.status == '200') {
                toastr.success(resp.success);
                document.getElementById(form_name).reset();
                setTimeout(function() {
                    window.location.replace(url);
                }, 500);
            } else {
                var obj = resp.errors;
                if (typeof obj === 'string') {
                    toastr.error(obj);
                } else {
                    $.each(obj, function(i, e) {
                        toastr.error(e);
                    });
                }
            }
        }
        $(document).ready(function() {
            @if(Session::has('error'))
                toastr.error('{{ Session::get('error') }}');
            @elseif(Session::has('success'))
                toastr.success('{{ Session::get('success') }}');
            @endif
        });
    </script>

</body>

</html>