@include('email.header')

 Reset Password Link
 </h5>

<p style="text-align: left;margin: 0;">Hi {{$username}},</p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

  <p>Thanks for advising us that you’ve forgotten your password.  We have passwords for everything in this digital age, so we totally get it! ;) </p>

	<p>Please <a href="{{$link}}">Click Here</a> to reset your password, so that you’ll be able to get into the app again to book your Fix in no time!</p>

	<p>Thanks, and have a great day!</p>

</div>


<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')









