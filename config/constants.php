<?php

return [
    'PASSWORD_EXPIRY' => 48, //in hours
    'PASSWORD_RECOVERY_LIMIT' => 5, 
    'AcrCalculationLimit' => 5,
    'TwentyFiveLimit' => 25,
    'ZeroLimit' => 0,
    'SkillParameters'=>['Timing'=>1,'Musicality'=>2,'Fun'=>3,'Connection'=>4,'Lead'=>5,'Follow'=>6],
    
    'VERIFICATION_TOKEN_TYPE' => ['email' => 1, 'forgot_password' => 2],
    'USER_TYPE' => ['admin' => 1, 'user' => 2],
    'DEVICE_TYPE' => ['web' => 0, 'ios' => 1, 'android' => 2],
    'USER_STATUS' => ['blocked' => 0, 'active' => 1],
    'USER_ACR_STATUS' => ['blocked' => 0, 'active' => 1],
    'ORG_NAME' => 'BeautyJunkie',
    'RESET_PASSWORD' => ['blocked' => 0, 'active' => 1],
    'ADMIN_EMAIL_SIGNATURE' => ' Beauty Junkie Admin',
    'SUBJECT' => ['admin_forgot_password' => ' Beauty Junkie Account Password Recovery', 'instructor_create' => 'Your  Beauty Junkie Account has been created'],
    'PER_PAGE' => 10,
    'GOOGLE_PLACES_URL' => 'https://maps.googleapis.com/maps/api/',
    'AUS_STATES' => ['Australian Capital Territory','New South Wales','Northern Territory','Queensland','South Australia','Tasmania','Victoria','Western Australia']
];
