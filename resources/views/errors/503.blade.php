<html lang="en" data-bs-theme="dark">

<head>
  <meta charset="utf-8">
  <title>HUD | Error Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet">
  <link href="{{asset('css/app.min.css')}}" rel="stylesheet">

</head>

<body class="pace-done pace-top app-init">
  <div class="pace pace-inactive">
    <div class="pace-progress" data-progress-text="100%" data-progress="99"
      style="transform: translate3d(100%, 0px, 0px);">
      <div class="pace-progress-inner"></div>
    </div>
    <div class="pace-activity"></div>
  </div>

  <div id="app" class="app app-full-height app-without-header">

    <div class="error-page">

      <div class="error-page-content">
        <div class="card mb-5 mx-auto" style="max-width: 320px;">
          <div class="card-body">
            <div class="card">
              <div class="error-code">503</div>
              <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
              </div>
            </div>
          </div>
          <div class="card-arrow">
            <div class="card-arrow-top-left"></div>
            <div class="card-arrow-top-right"></div>
            <div class="card-arrow-bottom-left"></div>
            <div class="card-arrow-bottom-right"></div>
          </div>
        </div>
        <h1>Module Is Under Maintenance</h1>
        <h3>Please wait for a while. Thank You.</h3>
        <hr>
        <!-- <p class="mb-1">
          Here are some helpful links instead:
        </p> -->
        <!-- <p class="mb-5">
          <a href="index.html" class="text-decoration-none text-inverse text-opacity-50">Home</a>
          <span class="link-divider"></span>
          <a href="page_search_results.html" class="text-decoration-none text-inverse text-opacity-50">Search</a>
          <span class="link-divider"></span>
          <a href="email_inbox.html" class="text-decoration-none text-inverse text-opacity-50">Email</a>
          <span class="link-divider"></span>
          <a href="calendar.html" class="text-decoration-none text-inverse text-opacity-50">Calendar</a>
          <span class="link-divider"></span>
          <a href="settings.html" class="text-decoration-none text-inverse text-opacity-50">Settings</a>
          <span class="link-divider"></span>
          <a href="helper.html" class="text-decoration-none text-inverse text-opacity-50">Helper</a>
        </p> -->
        <a href="javascript:window.history.back();" class="btn btn-outline-theme px-3 rounded-pill"><i
            class="fa fa-arrow-left me-1 ms-n1"></i> Go Back</a>
      </div>

    </div>

    <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>

  </div>


  <script src="{{asset('/js/vendor.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('/js/app.min.js')}}" type="text/javascript"></script>



</body>

</html>