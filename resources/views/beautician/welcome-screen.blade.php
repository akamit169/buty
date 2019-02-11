<!DOCTYPE html>
  <html>
    <head>
      <!--Import Google Icon Font-->
      <title>Beauty Junkie</title>
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/materialize.min.css')}}"  media="screen,projection">
      <link type="text/css" media="screen" rel="stylesheet" href="{{asset('assets/beautician/css/jquery.cropbox.css')}}">
      <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/style.css')}}">
      <link rel="icon" type="image/png" href="{{asset('assets/beautician/images/favicon.ico')}}" />
      
      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->
      <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/responsive.css')}}">
    </head>

    <body>

    <div class="pg-container">
      <header class="top-header">
        <div class="left-logo">
          <div class="left-logo">
          <a class="small-logo">
            <img src="{{asset('assets/beautician/images/logo.png')}}" alt="Site logo">
          </a>
        </div>
        </div>
      </header>
      <div class="main-container">
      <div class="large-btn-wrapper">
      <!-- start welcome screen -->
        <div class="welcome-wrapper">
          <h2>Hello Georgia!</h2>
          <h4>Upload a Business Profile Picture</h4>
          <p>(This should be a business logo or a<br> professional business pic/headshot)</p>

          <div class="form-for-upload-image">
            <div class="round-upload-button">
              <img src="{{asset('assets/beautician/images/upload-icon.png')}}" alt="">
              <input id="profile-pic-url" type="file" name=""  accept="images">
            </div>
            <div class="crop-wrapper">
              <img class="cropimage" alt="" src="{{asset('assets/beautician/images/profile_default.jpg')}}" />
            </div>
            <div class="edit-image">edit</div>
          </div>

        </div>
        <button id="continue-btn" class="bg-btn waves-effect">Continue</button>
        <!-- end welcome screen -->
      </div>
      </div>
    </div>


    <script type="text/javascript" src="{{asset('assets/beautician/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/beautician/js/hammer.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/beautician/js/jquery.mousewheel.js')}}"></script>

    <script src="{{asset('assets/beautician/js/jquery.cropbox.js')}}"></script>
    <script src="{{asset('assets/beautician/js/welcome-screen.js')}}"></script>



    </body>
  </html>