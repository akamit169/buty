@include('email.header')

Dispute Raised Successfully
</h5>

<p style="text-align: left;margin: 0;">Hi Team @ Beauty Junkie,</p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
	<p>
	  <strong>{{$raiser['name']}}</strong> has raised a dispute with <strong>{{$raisedTo['name']}}</strong> on the booking with <strong>Booking Id: {{$bookingId}} </strong> for the reason below:
	   <p>
	   	 <strong>{{$reason}}</strong>
	   </p>
	</p>

	<p>Please contact the users involved.  </p>

	<p>Thanks! </p>
</div>

<p style="text-align: left;margin: 0;">In-app Messenger Service</p>

@include('email.footer')





