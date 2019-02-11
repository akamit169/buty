@include('email.header')

Reset Password
</h5>

<p style="text-align: left;margin: 0;">Hi, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

	<p>We have received a request to reset your  Beauty Junkie admin account password.<br> 
	<br>
	        Please use the temporary password given below to login to your admin account:<br>
	        <b>{{$password}} </b> </p>

	<p>We recommend you to change your password upon first login.</p>

	<p> Thank you for using  Beauty Junkie!</p>

	<p>Cheers,</p>

</div>

<p style="text-align: left;margin: 0;">In-app Messenger Service</p>

@include('email.footer')




