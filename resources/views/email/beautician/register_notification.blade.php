@include('email.header')

Registeration Successful
</h5>

<p style="text-align: left;margin: 0;"> Welcome {{ucfirst($userObj->first_name).' '.ucfirst($userObj->last_name)}}, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">

	<p>Thanks for registering your account with Beauty Junkie.</p>

	<p>We’re super excited you’ve found us, and want to be on our mobile app community!</p>

	<p>The team at Beauty Junkie will review your application, and will be in touch shortly via email once your account has been approved and is ready to go live!</p>

	<p>Please hang tight as this process usually takes around 24-48 hours</p>

	<p>We highly recommend that you have your own business insurance as a beauty pro working mobile and/or in studio. Should you require assistance in setting this up, we do have a preferred insurance partner that we would happily recommend.</p>

	<p>While you’re waiting , and if you have any questions, or simply wish to say ‘hello’, please feel free to reach out to us at <a href="mailto:help@beautyjunkie.com.au" target="_top">help@beautyjunkie.com.au</a> We'd love to hear from you!</p>

	<p>Chat soon!</p>

</div>

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')