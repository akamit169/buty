@include('email.header',['showTick' => 'false'])

Payment Failed

</h5>


<p style="text-align: left;margin: 0;">Hi {{trim($bookingDetails[0]->customerFirstName." ".$bookingDetails[0]->customerLastName}},</p>


<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

	  <p>
	   <p>Oops!  It appears that something has gone wrong with your payment of the following service/s:</p>
	   @foreach($bookingDetails as $bookingDetail)

	   	  <?php $beauticianAddressArr = []; ?>
			 @if($bookingDetail->beauticianAddress)
			   <?php array_push($beauticianAddressArr, $bookingDetail->beauticianAddress); ?>
			 @endif

			@if($bookingDetail->beauticianSuburb)
			   <?php array_push($beauticianAddressArr, $bookingDetail->beauticianSuburb); ?>
			 @endif

			 @if($bookingDetail->beauticianState)
			   <?php array_push($beauticianAddressArr, $bookingDetail->beauticianState); ?>
			 @endif

			 @if($bookingDetail->beauticianCountry)
			   <?php array_push($beauticianAddressArr, $bookingDetail->beauticianCountry); ?>
			 @endif

			 @if($bookingDetail->beauticianZipcode)
			   <?php array_push($beauticianAddressArr, $bookingDetail->beauticianZipcode); ?>
			 @endif

			 {{implode(", ",$beauticianAddressArr)}}

	      <p>
	      	<strong>Beauty Pro Name:</strong> {{trim($bookingDetail->beauticianFirstName." ".$bookingDetail->beauticianLastName}}
	      	<strong>Beauty Pro Address:</strong> {{implode(", ",$beauticianAddressArr)}} </br>
	      	<strong>Beauty Pro Mobile Number:</strong> {{$bookingDetail->beauticianPhone}}</br>
	      	<strong>Service Name:</strong> {{$bookingDetail->serviceName}}</br>
	        <strong>Service Cost:</strong> {{$bookingDetail->actual_cost}}</br>
	        <strong>Service Date Time:</strong> {{\App\Utilities\DateTimeUtility::convertDateTimeToTimezone($bookingDetail->start_datetime,$bookingDetail->timezone,'d/m/Y , g:i A')}}
	        <strong>Service Address: {{$bookingDetail->booking_address}}</strong> 
	       
	       </p>
	   @endforeach

	<p>

	<p>In our experience, here are 3 things to check to make sure everything is all good.
		<ul>
			<ol>Triple check you’ve entered your credit card details correctly</ol>
			<ol>Check your account for available funds, a pesky payment may have been taken without you knowing!</ol>
			<ol>Touch base with your bank and check everything is ok with the transaction</ol>
		</ul>
	</p>

	<p>We will try processing the payment for the current booking again after 24 hours.  We will similarly notify you if payment is still not able to go through. </p>

	<p>If you’ve checked all three things above and your payment is still unable to be processed, please get in contact with us as <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a> so we can help you work out what is going on. </p>

	<p>If you have any questions or feedback, please reach out to us at <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a>, we’re always here to help!</p>

	 <p>Thanks,</p>

</div>

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')





