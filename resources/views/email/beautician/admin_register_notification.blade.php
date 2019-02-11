@include('email.header')

New Beauty Pro Sign up
</h5>

<p style="text-align: left;margin: 0;">Hi Team @ Beauty Junkie,</p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

	<p>{{ucfirst($userObj->first_name).' '.ucfirst($userObj->last_name)}} has sent their application with all the pre-requisite details. Please review this application and notify the applicant within 24-48 hours. </p>


	<p>Thanks!</p>

</div>

<p style="text-align: left;margin: 0;">In-app Messenger Service</p>

@include('email.footer')
