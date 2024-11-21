<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" class="bg-cover-1">

<head>
    <meta charset="utf-8" />
    <title>SMTT | Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="E-FORM" />
    <meta name="author" content="JH Chen" />
    <link rel="icon" type="image/png" href="{{asset('img/company.png')}}">
    <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/app.min.css')}}" rel="stylesheet" />

</head>


<body class="pace-done pace-top app-init theme-warning">
    <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99"
            style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div id="app" class="app app-full-height app-without-header">

        <div class="login">

            <div class="login-content">

                <h1 class="text-center">Reset Password</h1>
                <div class="text-inverse text-opacity-50 text-center mb-4">
                    For your protection, please reset your password.
                </div>
                <div class="mb-3">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col" colspan="3"><img src="{{asset('img/eg_logo.png')}}" alt=""
                                        class="card-img-top" /></th>
                            </tr>
                            <form method="POST" action="{{ route('password.update') }}" id="ResetPasswordForm">
                            @csrf
                                <tr>
                                    <th scope="col" colspan="3">
                                        <input type="password" class="form-control form-control-lg"  placeholder="New Password" name="password" value="{{ old('password') }}" autofocus>
                                        @error('password')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <div id="otpMessage"></div>
                                        <div id="reader"></div>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="3">
                                        <input type="password" class="form-control form-control-lg"  placeholder="Password Confirmation" name="password_confirmation">
                                        @error('password_confirmation')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="3" class="text-center">
                                        <button id="saveButton" type="submit" onclick="saveStatus()"
                                            class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3">Save
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></button>
                                    </th>
                                </tr>
                            </form>
                        </thead>

                        <body>
                            <tr>
                                <td>
                                    {{-- <button id="emailButton"
                                        class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3"><i class="bi bi-envelope"></i> Email OTP
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"> </button>
                                    <button id="telegramButton" 
                                        class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3"><i class="bi bi-telegram"></i> Telegram OTP
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"> </button> --}}
                                    {{-- <button id="loginButton"
                                        class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3 d-none">Validate</button> --}}
                                    <button id="refreshButton" onclick="location.reload(); return false;"
                                        class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3 d-none">Cancel</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-center text-inverse text-opacity-50">
                                        Don't have an account yet? Please contact Application Teams (<a href="#">EXT
                                            357</a>).
                                    </div>
                                </td>
                            </tr>
                        </body>

                    </table>
                </div>

            </div>

        </div>
    </div>
    </div>
    </div>
    </div>


    <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>

    <script src="{{asset('js/jquery-3.6.3.min.js')}}"></script>

    <script src="{{asset('js/vendor.min.js')}}" type="75585ce28ba806737c24d4bd-text/javascript"></script>
    <script src="{{asset('js/app.min.js')}}" type="75585ce28ba806737c24d4bd-text/javascript"></script>
    <script src="{{asset('js/html5-qrcode.min.js')}}"></script>
    <!-- <script src="../assets/js/jquery.inputmask.min.js"></script> -->
</body>
<script>
    $("#ResetPasswordForm").submit(function() {
            var $this = $("#saveButton");
            $this.attr("disabled", true);
            $this.find(".spinner-border").removeClass('d-none');
            $('#saveStatus').html('<div class="alert alert-info">Logging in, please wait...</div>');
        });
</script>

</html>