<!DOCTYPE html>
<html>

<head>
    <!--Import Google Icon Font-->
    <title>Beauty Junkie</title>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/materialize.min.css')}}" media="screen,projection">
    <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/style.css')}}">
    <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/font.css')}}">
    <link type="text/css" rel="stylesheet" href="{{asset('assets/beautician/css/responsive.css')}}">
    <link rel="icon" type="image/png" href="{{asset('assets/beautician/images/favicon.ico')}}" />
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta http-equiv="Content-Type" content="text/html; charset = utf-8" />
    <meta name="_token" content="{!! csrf_token() !!}"/>
</head>

<body>

   <div class="error-msg-div"></div>
    <div class="pg-container">
        <a href="#" class="menu-toggle">
            <span class="icon-close"></span>
            <h2 class="m-heading">Settings</h2>
        </a>
        <div class="left-nav-wrapper">
            <div class="left-nav">
                <header class="top-header">
                    <div class="left-logo">
                        <a class="small-logo">
                            <img src="{{asset('assets/beautician/images/logo.png')}}" alt="Site logo">
                        </a>
                    </div>
                </header>
                <ul class="navigation-panel">
                    <li><a href="#" class="booking-icon"><i class="icon-booking-icon"></i>Bookings</a></li>
                    <li><a href="#" class="fix-icon"><i class="icon-fix-icon"></i>Fixihibition</a></li>
                    <li><a href="#" class="notify-icon"><i class="icon-notify-icon"></i>Notifications</a></li>
                    <li><a href="#" class="profile-icon active"><i class="icon-profile-icon"></i>Profile</a></li>
                </ul>

            </div>
            <div class="sub-menu">
                <div class="profile-nav">
                    <a href="#">Expertise</a>
                    <a href="#">Services</a>
                    <a href="#">My Kit</a>
                    <a href="#" class="setting-links active">Settings</a>
                </div>
                <div class="profile-sub-nav">
                    <div class="tabs">
                        <a href="#edit-password-modal" class="edit-pro-link">Edit Profile Details<i class="icon-next"></i></a>
                        <a href="#ed-tip">Edit Service Tips<i class="icon-next"></i></a>
                        <a href="#avail">Availability<i class="icon-next"></i></a>
                        <a href="#pay">Payment Details<i class="icon-next"></i></a>
                        <a href="#tutorial">Tutorials<i class="icon-next"></i></a>
                        <a href="#app">App Help &amp; Feedback<i class="icon-next"></i></a>
                        <a href="#tc-div" class="active">T &amp; Cs / Privacy Policy<i class="icon-next"></i></a>
                    </div>
                    <a href="logout">Logout</a>
                </div>
            </div>
        </div>
        <div class="content-container">
            <div id="edit-profile"></div>
            <div id="ed-tip"></div>
            <div id="avail"></div>
            <div id="pay"></div>
            <div id="tutorial"></div>
            <div id="app"></div>
            <div id="tc-div" class="active">
                <div class="term-div">
                    <h2 class="m-heading mob-heading"><i class="icon-back"></i>T &amp; Cs</h2>
                    <h4>What is Lorem Ipsum?</h3>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><br>
        <h4>Why should I use Lorem Ipsum?</h3>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
              <br>
        <p class="text-right"><a href="#" class="change-policy-div">Read Privacy Policy</a></p></div>
              
        
        <div class="policy-div" style="display:none;">
                   <h2 class="m-heading mob-heading"><i class="icon-back"></i>Privacy Policy</h2>
                <h4>What is Lorem Ipsum?</h3>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><br>
        <h4>Why should I use Lorem Ipsum?</h3>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
              <br>
        <p class="text-right"><a href="#" class="change-term-div">Read T &amp; Cs </a></p></div>
               </div>
                </div>
        </div>
     <div id="edit-password-modal" class="modal">
     <form id="change-password-form" method="post">
    <div class="modal-content">
      <h4>Change Password</h4>
                    <div class="row">
                        <div class="col s12">
                            <div class="input-field">
                                <input id="oldPassword" type="password" name="oldPassword" class="validate"  autocomplete="new-password">
                                <label for="oldPassword">Old Password</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <div class="input-field">
                                <input id="password" type="password" name="password" class="validate"  autocomplete="new-password">
                                <label for="password">New Password</label>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-bottom-none">
                        <div class="col s12">
                            <div class="input-field">
                                <input id="confirmPassword" type="password" name="confirmPassword"  autocomplete="new-password" class="validate">
                                <label for="confirmPassword">Confirm Password</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-action modal-close waves-effect border-btn">Cancel</a>
                    <a href="#!" type="submit" class="modal-action bg-btn waves-effect save">Save</a>
                </div>
             </form>
            </div>

            <script type="text/javascript">
                var SITE_URL = "{{url('')}}/beautician";
            </script>
            <script type="text/javascript" src="{{asset('assets/beautician/js/jquery.min.js')}}"></script>
            <script type="text/javascript" src="{{asset('assets/beautician/js/materialize.min.js')}}"></script>
            <script type="text/javascript" src="{{asset('assets/beautician/js/common.js')}}"></script>
            <script type="text/javascript" src="{{asset('assets/beautician/js/settings.js')}}"></script>

</body>

</html>