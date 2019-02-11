@include('email.header',['showTick' => 'false'])

Payment Failure | Booking Cancelled
 </h5>

<p style="text-align: left;margin: 0;">Hi {{trim($bookingDetails->customerFirstName." ".$bookingDetail->customerLastName}},</p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

  
  <p>The following service(s) has/have been cancelled due to payment failure:</p>
   @foreach($bookingDetails as $bookingDetail)
      <p>
      	<strong>Service Name:</strong> {{$bookingDetail->serviceName}}</br>
        <strong>Service Cost:</strong> {{$bookingDetail->actual_cost}}</br>
        <strong>Service Date Time:</strong> {{\App\Utilities\DateTimeUtility::convertDateTimeToTimezone($bookingDetail->start_datetime,$bookingDetail->timezone,'d/m/Y , g:i A')}}</br>
        <strong>Beautician:</strong> {{trim($bookingDetails->beauticianFirstName." ".$bookingDetail->beauticianLastName}}
       </p>
   @endforeach

</div>


<p style="text-align: left;margin: 0;">In-app Messenger Service</p>

@include('email.footer')





