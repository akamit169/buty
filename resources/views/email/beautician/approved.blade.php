@include('email.header')

Application Approved
</h5>

<p style="text-align: left;margin: 0;">Hi {{ucfirst($userObj->first_name).' '.ucfirst($userObj->last_name)}}, </p>

<div style="text-align: left;color: #676767;line-height: 19px;margin: 16px 0;">
	<p>We’ve got some awesome news for you changing the game for your brand and business!</p>

	<p>Your application to register to use our mobile app platform has been <strong>approved!</strong> 
	<span style="color:green">&#10003;</span><br>

	<strong><i>Congratulations!</i></strong> </p>

	<p>So what now, I hear you ask!  Firstly, go and celebrate any way you know how – yay! ;) </p>

	<p>Following that, please login <a href="https://itunes.apple.com/in/genre/ios/id36?mt=8" style="color:red">BEAUTY JUNKIE APP</a> to the app and responsive web version 
	<a href="http://www.beautyjunkie.com.au/bpdash" style="color:red">www.beautyjunkie.com.au/bpdash</a> with your credentials and explore the many fun and functional features of our mobile app platform, and essentially start setting up your beauty pro profile for new and potential clients to see!

	<p>Beauty Junkie will be available to clients and beauty pros via iPhone, soon followed by the Android version. Beauty pros will also have access to Beauty Junkie via our responsive web version which allows you to populate content and images for your profile from the comfort of a larger screen.</p>

	Just some other useful information at your fingertips:

	<ul>
		<li>A Checklist and FAQs are available via our website at <a href="http://www.beautyjunkie.com.au">www.beautyjunkie.com.au</a></li>
		
		<li>Our Terms and Conditions and Privacy Policy are available on the Beauty Junkie app and the Beauty Junkie responsive web version if you ever need to refresh your memory re our cancellation and no show policies, etc.
		</li>
		
		<li>Important – your account will NOT go live until you include your profile pic and banking details, so if you need more time to perfect your profile, and don’t want it made public, please do not include this info.</li>

		<li>The Beauty Junkie digital marketing and PR team will be doing their thing to create hype, excitement, and educate our target market/your future clients (typically women!) about the Beauty Junkie app as the new way to find and book freelance beauty pro’s in Australia.</li>

		<li>You too can help spread the word!  Let your existing clients and personal/professional networks know that you’re on Beauty Junkie – use your Instagram profile + posts, Facebook, your website, and word of mouth!
		An Instagram 'promo' tile is also available for download on our website!
		</li>
	</ul>

	<p>But as always, please feel free to reach out to us if you have any questions or require support.  You can find us at <a href="mailto:help@beautyjunkie.com.au">help@beautyjunkie.com.au</a> We’d love to hear from you.</p>

	<p>We look forward to working with you and being part of the beauty junkie tribe. </p>

	<p>Chat soon!</p>

</div

<p style="text-align: left;margin: 0;">@include('email.signature')</p>

@include('email.footer')