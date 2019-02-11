@include('email.header')

New Sign Up With Referral Code
</h5>

<p style="text-align: left;margin: 0;">Hi Team @ Beauty Junkie,,</div>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
	<p>A new client has signed up to Beauty Junkie!

	    <ul>
	    	<li><strong>New Client Name: {{$user->first_name.' '.$user->last_name}}</strong></li>
	    	<li><strong>New Client Email Address: {{$user->email}}</strong></li>
	    	<li><strong>New Client Referral Code #: {{$beautician->referral_code}}</strong></li>
	    	<li><strong>Client Referrer: {{$beautician->first_name.' '.$beautician->last_name}} ({{$beautician->email}}) </strong></li>
	    </ul>
	</p>


	<p>Please reward the referrer with a 10% money back gift once {{$user->first_name.' '.$user->last_name}} completes their first booking</p>   

	<p> Thanks!  </p> 

</div>

<p style="text-align: left;margin: 0;">In-app Messenger Service</p>

@include('email.footer')
