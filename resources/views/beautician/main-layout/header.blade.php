@if(!\Auth::check())
 <header>
    <a href="signup" class="sign-btn"><span>Sign Up</span></a>
    <div class="logo"></div>
    <a href="login" class="login-btn">Log In</a>
 </header>
@else
    <header class="main-header">
        <a href="#" class="menu-toggle">
            <span class="fa fa-bars menu-icon"></span>
            <span class="icon icon-left-arrow back-arrow"></span>
        </a>
        <div class="inner-page-logo"></div>
        <nav class="nav-wrapper">
            <ul>
                <li class="{{Request::is('beautician/bookings')?'active':''}}">
                    <a href="{{url('beautician/bookings')}}"><i class="icon icon-booking"></i><span>Bookings</span></a>
                </li>

                <li class="{{Request::is('beautician/fixhibitions')?'active':''}}"><a href="{{url('beautician/fixhibitions')}}"><i class="icon icon-fixhibition"></i><span>FIXhibition</span></a></li>

                <li class="{{Request::is('beautician/notifications')?'active':''}}">
                    <a id="notifications-menu-tab" href="{{url('beautician/notifications')}}"><i class="icon icon-notification"></i><span>Notifications</span>
                     
                     @if(\Auth::user()->new_notifications_count) 
                     <span class="badge">{{\Auth::user()->new_notifications_count}}</span>
                     @else
                        <span class="badge hide"></span>
                     @endif
                    
                    </a>

                    <div class="notification-sub-menu">
                        
                    </div>
                </li>

                <li class="profile-nav{{(Request::is('beautician/profile') || Request::is('beautician/profile/*') || Request::is('beautician/getPortfolioUpload'))?' active':''}}"><a href="{{url('beautician/profile')}}"><i class="icon icon-profile"></i><span>Profile</span><i class="arrow fa fa-angle-down"></i></a>

                @if(Request::is('beautician/profile') || Request::is('beautician/profile/*') || Request::is('beautician/getPortfolioUpload') || Request::is('beautician/*'))
                    <div class="profile-sub-nav">
                        <ul>
                            <li @if(Request::is('beautician/profile')) class="active" @endif><a href="{{url('beautician/profile')}}"><span>About</span></a></li>
                            <li @if(Request::is('beautician/profile/beauticianExpertise')) class="active" @endif><a href="{{url('beautician/profile/beauticianExpertise')}}"><span>Expertise</span></a></li>
                            <li  class="{{Request::is('beautician/profile/services')?'active':''}}"><a href="{{url('beautician/profile/services')}}"><span>Services</span></a></li>
                            <li @if(Request::is('beautician/profile/beauticianProfileKit')) class="active" @endif><a href="{{url('beautician/profile/beauticianProfileKit')}}"><span>My Kit</span></a></li>
                        </ul>
                    </div>
                @endif

                </li>
                <li class="setting-nav {{(Request::is('beautician/setting') || Request::is('beautician/setting/*'))?' active':''}}">
                    <a href="{{url('beautician/setting/editProfile')}}">
                        <i class="icon icon-settings"></i><span>Settings</span><i class="arrow fa fa-angle-down"></i>
                    </a>
                    <div class="setting-sub-nav" style="display: none;">
                        <ul>
                            <li @if(Request::is('beautician/setting/editProfile')) class="active" @endif><a href="{{url('beautician/setting/editProfile')}}"><span>Edit Profile</span></a></li>
                            <li @if(Request::is('beautician/setting/beauticianAvailability')) class="active" @endif><a href="{{url('beautician/setting/beauticianAvailability')}}"><span>Availability</span></a></li>
                            <li @if(Request::is('beautician/setting/beauticianPaymentDetail')) class="active" @endif><a href="{{url('beautician/setting/beauticianPaymentDetail')}}"><span>Payment Details</span></a></li>
                            <li><a href="mailto:help@beautyjunkie.com.au?subject=App Help %26 Feedback"><span>App Help &amp; Feedback</span></a></li>
                            <li @if(Request::is('beautician/setting/tutorials')) class="active" @endif><a href="{{url('beautician/setting/tutorials')}}"><span>Features and Benefits</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="logout"><a href="{{url('beautician/logout')}}"><i class="fa fa-power-off"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </header>
@endif
