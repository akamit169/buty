@include('email.header')

User Flagged Successfully
 </h5>

<p style="text-align: left;margin: 0;"> Hi Team @ Beauty Junkie,  </p>


<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

		<p> {{$flaggedUser['name']}} ({{$flaggedUser['email']}}) has been flagged by  {{$flaggedBy['name']}} ({{$flaggedBy['email']}}) for the reason below: </p>

		<p><strong>{{$reason}}</strong></p>

		<p>Please contact the users involved.</p>

		<p>Thanks!</p>

</div>

<p style="text-align: left;margin: 0;">In-app Messenger Service</p>
