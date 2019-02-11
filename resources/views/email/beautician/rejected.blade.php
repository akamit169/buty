@include('email.header',['showTick' => 'false'])

Application Rejected
</h5>
<p style="text-align: left;margin: 0;">Hi {{ucfirst($userObj->first_name).' '.ucfirst($userObj->last_name)}}, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
	<p>We’re sad to inform that, although you were so close, on this occasion your application to register to use our mobile app platform has been <strong>unsuccessful.</strong> </p>

	<p>So what now, I hear you ask? Please do not despair as it is not all doom and gloom.</p>

	<p>In the words of destiny’s child, ‘and if at first you don’t succeed, dust yourself off and try again’… But in this case, it’s super easy!</p>

	<p>The beauty junkie app will be a game-changer for your brand and business so get that application in tip-top shape, re-apply, and be ready to go live! </p>

	<p>Please be sure to check that you have included all the necessary information when you re-apply:</p>

	<ul>
		<li>Do you have an Australian Business Number (ABN)? <a href="http://ABN.beautyjunkie.com.au">ABN.beautyjunkie.com.au</a> </li>

		<li>Do you have a current Working Police Check - <a href="http://check.beautyjunkie.com.au">check.beautyjunkie.com.au</a></li>
	</ul>

	<p>The good news is you only have to wait 24 hours to re-apply, so get checking that application and we hope to hear from you soon!   </p>

	<p>If you require assistance, please feel free to reach out to the Beauty Junkie team at <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a> </p>

</div>


<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')
