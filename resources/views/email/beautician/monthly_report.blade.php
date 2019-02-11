@include('email.header')

Monthly Report for {{date('F')}}
</h5>

<p style="text-align: left;margin: 0;">Hi Beauty Pro, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
  <p>Please find attached your monthly service report.</p>
  <p>Make sure to reach out to us at help@beautyjunkie.com.au if you have any questions or feedback. We'd love to hear from you!</p>
</div>

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')	