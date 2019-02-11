@include('email.header')

Rate &amp; Review | Comment 
</h5>

<p style="text-align: left;margin: 0;">Dear Admin,</p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
	<p>
	    {{$mailData['currentUserType'].' '.$mailData['currentUserName']}}  has given {{$mailData['otherUserType'].' '.$mailData['otherUserName']}} stars count {{$mailData['rating']}} @if(!empty($mailData['comment']))with a comment as mentioned below :-@endif
	</p>
	@if(!empty($mailData['comment']))
	<p>
	    {{$mailData['comment']}}
	</p>
	@endif

	<p>Cheers,</p>

</div>

<p style="text-align: left;margin: 0;">In-app Messenger Service</p>

@include('email.footer')





