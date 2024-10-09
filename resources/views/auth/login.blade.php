{{-- <?php

// // Assuming you have a database connection established

// // Get the authentication token from the HttpOnly cookie
// $cookieTicket = isset($_COOKIE['auth_ticket']) ? $_COOKIE['auth_ticket'] : null;
// if ($cookieTicket) {
//     // Check the database for the user's ticket
//     include('connection/dbcon.php'); // Include your database connection file

//     $sqlCheck = "SELECT Redirect_url FROM [erplivedb_customer].[dbo].[USERS_PROFILE] WHERE Ticket = ?";
//     $paramsCheck = array($cookieTicket);
    
//     $stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);
//     $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
    
//     if ($stmtCheck === false) {
//         // Handle database query error
//         die("Error executing query: " . print_r(sqlsrv_errors(), true));
//     }

//     header("Location: ".$row['Redirect_url'] , true, 301); 
//     exit();
// }

?> --}}

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" class="bg-cover-1">

<head>
    <meta charset="utf-8" />
    <title>SMTT | Login</title>
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

                <h1 class="text-center">Sign In</h1>
                <div class="text-inverse text-opacity-50 text-center mb-4">
                    For your protection, please verify your identity.
                </div>
                <div class="mb-3">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col" colspan="3"><img src="{{asset('img/company_2.png')}}" alt=""
                                        class="card-img-top" /></th>
                            </tr>
                            <form method="POST" action="{{ route('login') }}" id="LoginForm">
                            @csrf
                                <tr>
                                    <th scope="col" colspan="3">
                                        <input type="number" class="form-control form-control-lg"  placeholder="Employee ID(XXXXX)" name="username" id="username" value="{{ old('username') }}">
                                        @error('username')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <div id="otpMessage"></div>
                                        <div id="reader"></div>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="3">
                                        <input type="password" class="form-control form-control-lg"  placeholder="Password" name="password" id="password">
                                        @error('password')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="3" class="text-center">
                                        <button id="loginButton" type="submit" onclick="loginStatus()"
                                            class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3">Login
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

    @if (session('alert'))
        <script>
            alert('{{session('alert')}}');
        </script>
    @endif

    <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>

    <script src="{{asset('js/jquery-3.6.3.min.js')}}"></script>

    <script src="{{asset('js/vendor.min.js')}}" type="75585ce28ba806737c24d4bd-text/javascript"></script>
    <script src="{{asset('js/app.min.js')}}" type="75585ce28ba806737c24d4bd-text/javascript"></script>
    <script src="{{asset('js/html5-qrcode.min.js')}}"></script>
    <!-- <script src="../assets/js/jquery.inputmask.min.js"></script> -->
</body>

</html>

<script>
    $(document).ready(function () {

        $("#username").focus();
        $("#username").keydown(function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                $("#scanButton").click();
            }
        });

        $("#LoginForm").submit(function() {
            var $this = $("#loginButton");
            $this.attr("disabled", true);
            $this.find(".spinner-border").removeClass('d-none');
            $('#loginStatus').html('<div class="alert alert-info">Logging in, please wait...</div>');
        });

        // $("#emailButton, #telegramButton").click(function () {
           
        //     var $this = $(this); // Declare $this using var, let, or const
        //     $this.attr("disabled", true);
        //     $this.find(".spinner-border").removeClass('d-none');
        //     $('#reader').html('');
          
        //     $.ajax({
        //         type: "POST",
        //         url: "auth.php",
        //         data: { employeeID: $('#employeeID').val(), category:$this.text() },
        //         success: function (d) {
        //             if (d.type === 1) {
        //                 $('#reader').html(d.message)
        //             }
        //             else
        //             {
        //                 $('#otpMessage').html(d.message)
        //                 $("#telegramButton, #emailButton, #employeeID").addClass('d-none');
        //                 $("#refreshButton, #loginButton").removeClass('d-none');
        //             }
                  
        //             $this.attr("disabled",false);
        //             $(".spinner-border").addClass('d-none');
        //         },
        //         error: function (xhr, status, error) {
        //             console.log('AJAX Error:', xhr, status, error);
        //             alert('Error: ' + error.message);
        //         }
        //     });
        // });

        // $("#loginButton").click(function () {
        //     var formData = new FormData(document.getElementById('otpForm'));
        //     formData.append('employeeID', $('#employeeID').val());
        //     // Example: Send data to the backend using Fetch API
        //     fetch('login.php', {
        //         method: 'POST',
        //         body: formData,
        //     })
        //     .then(response => response.json())
        //     .then(data => {
                    
        //             $('#otpReturnMessage').html(data.message)
        //     })
        //     .catch((error) => {
        //         console.error('Error:', error);
        //     });
        // });
    });
</script>