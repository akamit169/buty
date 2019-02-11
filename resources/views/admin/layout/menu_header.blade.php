<header class="header-bg-main dk header navbar navbar-fixed-top-xs">
    <div class="navbar-header aside-md"> 
       <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html"> <i class="fa fa-bars"></i> </a> 
       <a href="{{url('admin/user')}}" class="navbar-brand"><span class="textLogo m-r-sm"> <img src="{{URL::asset('assets/admin/images/logo.png')}}" class="m-r-sm" alt=""> beauty junkie</span></a> 
       <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user"> <i class="fa fa-cog"></i> </a> 
    </div>
    <ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user">
        <li class="dropdown">
<!--            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <b class="caret"></b> </a> -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="thumb-sm avatar pull-left"> </span><b class="caret"></b><i class="fa fa-cog setting-icon"></i> </a>
            <ul class="dropdown-menu animated fadeInRight">
                <span class="arrow top"></span> 
                <li> <a href="{{url('admin/user/change-password-by-dasboard')}}" >Change Password</a> </li>
                <li> <a href="{{url('/admin/logout')}}" >Logout</a> </li>
                 
                
            </ul>
        </li>
    </ul>
</header>