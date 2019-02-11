<aside class="bg-left-nav lter aside-md hidden-print hidden-xs" id="nav">
    <section class="vbox">
        <section class="w-f scrollable">
            <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                <!-- nav --> 
                <nav class="nav-primary hidden-xs">
                    <ul class="nav">
                        <li class="{{ Request::is('admin/beautician') ||Request::is('admin/beautician/*') ? 'active treeview' : ''}}" >
                            <a href="#" class=""> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Beauty Pro List</span> </a> 
                            <ul class="nav lt">
                                <li class="{{ Request::is('admin/beautician/get-beautician-list') ? 'active' : ''}}"> <a href="{{ url('admin/beautician/get-beautician-list')}}"> <i class="fa fa-angle-right"></i> <span>Beauty Pro For Approval</span> </a> </li>
                                <li class="{{ Request::is('admin/beautician/get-beautician-list') ? 'active' : ''}}"> <a href="{{ url('admin/beautician/approved-beautician-list')}}"> <i class="fa fa-angle-right"></i> <span>Approved Beauty Pro</span> </a> </li>
                                <li class="{{ Request::is('admin/beautician/get-beautician-list') ? 'active' : ''}}"> <a href="{{ url('admin/beautician/rejected-beautician-list')}}"> <i class="fa fa-angle-right"></i> <span>Rejected Beauty Pro</span> </a> </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('admin/customer') ||Request::is('admin/customer/*') ? 'active treeview' : ''}}" >
                            <a href="#" class=""> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Client</span> </a> 
                            <ul class="nav lt">
                                <li class="{{ Request::is('admin/customer/get-customer-list') ? 'active' : ''}}"> <a href="{{ url('admin/customer/get-customer-list')}}"> <i class="fa fa-angle-right"></i> <span>Client's List</span> </a> </li>
                            </ul>
                        </li>
                        <li class="{{Request::is('admin/user/get-suspended-user-list') || Request::is('admin/user/get-flagged-user-list')? 'active treeview' : ''}}" >
                            <a href="#" class=""> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Reported Users</span> </a> 
                            <ul class="nav lt">
                                <li class="{{ Request::is('admin/user/get-suspended-user-list') ? 'active' : ''}}"> <a href="{{ url('admin/user/get-suspended-user-list')}}"> <i class="fa fa-angle-right"></i> <span>Suspended User's List</span> </a> </li>
                                <li class="{{ Request::is('admin/user/get-flagged-user-list') ? 'active' : ''}}"> <a href="{{ url('admin/user/get-flagged-user-list')}}"> <i class="fa fa-angle-right"></i> <span>Flagged User's List</span> </a> </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('admin/service-booking') ||Request::is('admin/service-booking/*') ? 'active treeview' : ''}}" >
                            <a href="#" class=""> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Booked Service</span> </a> 
                            <ul class="nav lt">
                                <li class="{{ Request::is('admin/service-booking/get-service-list') ? 'active' : ''}}"> <a href="{{ url('admin/service-booking/get-service-list')}}"> <i class="fa fa-angle-right"></i> <span>Booked Service's List</span> </a> </li>
                                <li class="{{ Request::is('admin/service-booking/disputed-service-list') ? 'active' : ''}}"> <a href="{{ url('admin/service-booking/disputed-service-list')}}"> <i class="fa fa-angle-right"></i> <span>Disputed Bookings List</span> </a> </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('admin/referred-user-list') ? 'active' : ''}}"> <a href="{{ url('admin/referred-user-list')}}"> <span>Referred Users</span> </a> </li>

                        <li class="{{Request::is('admin/user/beautician-revenue') || Request::is('admin/user/customers-revenue') || Request::is('admin/revenue/*') ? 'active treeview' : ''}}" >
                            <a href="#" class=""> <span class="pull-right"> <i class="fa fa-angle-down text"></i> <i class="fa fa-angle-up text-active"></i> </span> <span>Lean Analytics</span> </a> 
                            <ul class="nav lt">
                                <li class="{{ Request::is('admin/user/customers-revenue') ? 'active' : ''}}"> <a href="{{ url('admin/user/customers-revenue')}}"> <span>Clients Revenue</span> </a> </li>
                                <li class="{{ Request::is('admin/user/beautician-revenue') ? 'active' : ''}}"> <a href="{{ url('admin/user/beautician-revenue')}}"> <span>Beauty Pro Revenue</span> </a> </li>
                                <li class="{{ Request::is('admin/revenue/revenue-gain') ? 'active' : ''}}"> <a href="{{ url('admin/revenue/revenue-gain')}}"> <span>Revenue Gain By Month</span> </a> </li>
                                <li class="{{ Request::is('admin/revenue/booking-ratio') ? 'active' : ''}}"> <a href="{{ url('admin/revenue/booking-ratio')}}"> <span>Booking Ratio By Month</span> </a> </li>
                                <li class="{{ Request::is('admin/revenue/used-service-list') ? 'active' : ''}}"> <a href="{{ url('admin/revenue/used-service-list')}}"> <span>Service Used Ratio</span> </a> </li>
                                <li class="{{ Request::is('admin/revenue/upcoming-revenue-list') ? 'active' : ''}}"> <a href="{{ url('admin/revenue/upcoming-revenue-list')}}"> <span>Upcoming Revenue</span> </a> </li>
                                <li class="{{ Request::is('admin/revenue/repeated-user-list') ? 'active' : ''}}"> <a href="{{ url('admin/revenue/repeated-user-list')}}"> <span>Repeated User By Month</span> </a> </li>
                            </ul>
                        </li>

                         <li class="{{ Request::is('admin/commission-settings') ? 'active' : ''}}"> <a href="{{ url('admin/commission-settings')}}"> <span>Commission Fee Settings</span> </a> </li>


                         <li class="{{ Request::is('admin/app-settings') ? 'active' : ''}}"> <a href="{{ url('admin/app-settings')}}"> <span>App Settings</span> </a> </li>

                    </ul>

                </nav>
                <!-- / nav --> 
            </div> 
        </section>

    </section>
</aside>