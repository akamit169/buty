swagger: '2.0'
info:
  version: 1.0.0
  title: BeautyJunkie APIs
  description: |
    ####  https://<?php echo $_SERVER['SERVER_NAME']; ?><?php echo explode("/public", dirname($_SERVER['REQUEST_URI']))[0] ?>/public/api
    Use Above URL as basepath + path listed below and use Try Operation to test it. You can see the response right here.
    
definitions: 
   Error:
    type: object
    properties:
      status:
        type: boolean
      message:
        type: string
      errors:
        type: object
        description: Key/value errors for each field
        
schemes:
  - https
host: <?php echo $_SERVER['SERVER_NAME']; ?>

basePath: /api
paths:
  /customer/userRegistration:
    post:
      description: Used to register customer with or without facebook
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: email
          required: true
          in: formData
          description: User's email
          type: string  
        - name: facebookId
          required: false
          in: formData
          description: User's facebook id if connecting with facebook
          type: string  
        - name: firstName
          required: true
          in: formData
          description: User's First Name
          type: string  
        - name: lastName
          required: true
          in: formData
          description: User's Last Name
          type: string  
        - name: password
          required: false
          in: formData
          description: User's selected password
          type: string  
        - name: confirmPassword
          required: false
          in: formData
          description: User's selected custom password | should match with password field
          type: string  
        - name: referralCode
          required: false
          in: formData
          description: Referral Code
          type: string 
        - name: userType
          required: true
          in: formData
          description: User's Type | 2 => 'Beautician' | 3 => 'Customer' In this case, send 3
          type: int  
        - name: deviceToken
          required: true
          in: formData
          description: Device token
          type: string  
        - name: deviceType
          required: true
          in: formData
          description: Device Type user is signing up with | 1 => 'ios'
          type: integer 
          
  /customer/userLogin:
    post:
      description: Used to login user i.e. either customers or beauticians | In case of beautician, if not approved by admin you will get adminApprovalStatus as 0 else 1.
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: email
          required: true
          in: formData
          description: User's email
          type: string  
        - name: password
          required: true
          in: formData
          description: User's selected password
          type: string
        - name: deviceToken
          required: true
          in: formData
          description: Device token
          type: string  
        - name: deviceType
          required: true
          in: formData
          description: Device Type user is signing up with | 1 => 'ios'
          type: integer
          
  /customer/userLogout:
    post:
      description: Used to logout user from app
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/userRegistration:
    post:
      description: Used to register beautician and login user and In case of beautician, if not approved by admin you will get adminApprovalStatus as 0 else 1.
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: email
          required: true
          in: formData
          description: User's email
          type: string  
        - name: firstName
          required: true
          in: formData
          description: User's First Name
          type: string  
        - name: lastName
          required: true
          in: formData
          description: User's Last Name
          type: string  
        - name: password
          required: true
          in: formData
          description: User's selected password
          type: string  
        - name: confirmPassword
          required: true
          in: formData
          description: User's selected custom password | should match with password field
          type: string
        - name: phone
          required: true
          in: formData
          description: User Phone Number
          type: string 
        - name: businessName
          required: true
          in: formData
          description: User Business Name
          type: string 
        - name: abn
          required: true
          in: formData
          description: User ABN Name | max length upto 11 characters
          type: string 
        - name: instaId
          required: true
          in: formData
          description: User Instagram Id
          type: string 
        - name: certificate
          required: true
          in: formData
          description: User's Police checked Certificate | Accepted Image types are jpg, jpeg and png.
          type: file 
        - name: userType
          required: true
          in: formData
          description: User's Type | 2 => 'Beautician' | 3 => 'Customer' In this case, send 2
          type: int 
        - name: referralCode
          required: false
          in: formData
          description: Referral code 
          type: int  
        - name: deviceToken
          required: true
          in: formData
          description: Device token
          type: string  
        - name: deviceType
          required: true
          in: formData
          description: Device Type user is signing up with | 1 => 'ios'
          type: integer
  

  /user/changePassword:
    post:
      description: Used to Change Password from app
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          parameters:
        - name: oldPassword
          required: true
          in: formData
          description: User's old password
          type: string  
        - name: password
          required: true
          in: formData
          description: Password that the user wants to set
          type: string
        - name: confirmPassword
          required: true
          in: formData
          description: Password that the user wants to set
          type: string

  /user/forgotPassword:
    post:
      description: Used to send forgot password email to user
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters: 
        - name: email
          required: true
          in: formData
          description: registered email id
          type: string 

  /service/getServiceList:
    get:
      description: Used to get all service list
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string

  /user/profilePic:
    post:
      description: Used to upload user profile pic
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: profilePic
          required: true
          in: formData
          description: User's profile pic object form data
          type: file

  /beautician/setupBusinessProfile:
    post:
      description: Used to setup business profile
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          parameters:
        - name: address
          required: false
          in: formData
          description: User's address
          type: string
        - name: suburb
          required: true
          in: formData
          description: User's suburb
          type: string
        - name: country
          required: true
          in: formData
          description: User's country
          type: string
        - name: businessDescription
          required: true
          in: formData
          description: Business Description
          type: string
        - name: workRadius
          required: false
          in: formData
          description: Radius from users's location upto which they can provide the services
          type: string
        - name: lat
          required: true
          in: formData
          description: User's latitude
          type: number
          format: float
        - name: lng
          required: true
          in: formData
          description: User's longitude
          type: number
          format: float
        - name: phone
          required: false
          in: formData
          description: User's phone number
          type: string
        - name: instaId
          required: true
          in: formData
          description: User's instagram id
          type: string
        - name: postalCode
          required: false
          in: formData
          description: User's postal code
          type: integer
        - name: crueltyFreeMakeup
          required: false
          in: formData
          description: User's cruelty free makeup flag | 0 => 'No', 1=>'Yes'
          type: integer
          
  /beautician/saveBeauticianPortfolio:
    post:
      description: Used to upload user profile pic
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: portfolioPic
          required: true
          in: formData
          description: Service's portfolio image.
          type: file 
        - name: serviceId
          required: true
          in: formData
          description: Service Id against which image needs to be uploaded.
          type: integer

  /customer/setupProfile:
    post:
      description: Used to setup customer profile
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: address
          required: false
          in: formData
          description: User's address
          type: string
        - name: suburb
          required: true
          in: formData
          description: User's suburb
          type: string
        - name: country
          required: true
          in: formData
          description: User's country
          type: string
        - name: gender
          required: true
          in: formData
          description: gender
          type: integer
        - name: dateOfBirth
          required: false
          in: formData
          description: date Of Birth
          type: string
        - name: lat
          required: true
          in: formData
          description: User's latitude
          type: number
          format: float
        - name: lng
          required: true
          in: formData
          description: User's longitude
          type: number
          format: float
        - name: skinColorId
          required: true
          in: formData
          description: skin Color Id
          type: integer
        - name: skinTypeId
          required: true
          in: formData
          description: skin Type Id
          type: integer
        - name: hairTypeId
          required: true
          in: formData
          description: hair Type Id
          type: integer
        - name: hairlengthTypeId
          required: true
          in: formData
          description: hairlength Type Id
          type: integer
        - name: isHairColored
          required: true
          in: formData
          description: is Hair Colored
          type: integer
        - name: allergies
          required: false
          in: formData
          description: allergies
          type: string
        - name: description
          required: true
          in: formData
          description: description
          type: string
        - name: phone
          required: true
          in: formData
          description: User's phone number
          type: string
        - name: postalCode
          required: true
          in: formData
          description: User's postal code

  /customer/appearanceData:
    get:
      description: get data related to custome appearance Data
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/beauticianPortfolio:
    delete:
      description: Used to upload user profile pic
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          parameters:
        - name: portfolioId
          required: true
          in: formData
          description: Service's portfolio id.
          type: integer
          
  /beautician/getBeauticianPortfolioList:
    get:
      description: Used to get beautician portfolio list
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/getBeauticianKit:
    get:
      description: Used to get beautician kit list
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/saveBeauticianKit:
    post:
      description: Used to get beautician kit list
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: kitName[]
          required: false
          in: formData
          description: beautician entered kit name to be provided in array
          type: array
        - name: deletedKitId[]
          required: false
          in: formData
          description: deleted beautician kit id to be provided in array
          type: array
  
  /beautician/saveExpertise:
    post:
      description: Used to save beautician qualifications and specialities
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: qualification[]
          required: false
          in: formData
          description: qualification text to be provided in array
          type: array
        - name: deletedQualificationId[]
          required: false
          in: formData
          description: deleted qualification id of the beautician to be provided in array
          type: array
        - name: speciality[]
          required: false
          in: formData
          description: speciality text to be provided in array
          type: array
        - name: deletedSpecialityId[]
          required: false
          in: formData
          description: deleted speciality id of the beautician to be provided in array
          type: array

  /beautician/getExpertise:
    get:
      description: Used to get the qualifications and specialities of the beautician
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/updateBusinessDescription:
    post:
      description: Used to setup business profile
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: businessDescription
          required: true
          in: formData
          description: Business Description
          type: string

  /customer/registerUserOnStripe:
    post:
      description: Used to register user on stripe 
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: stripeToken
          required: true
          in: formData
          description: stripeToken
          type: string

  /customer/signupReferral:
    post:
      description: Used to save referral code after social login
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: referralCode
          required: true
          in: formData
          description: referralCode
          type: string

  /customer/searchBeautician:
    get:
      description: Used to search beautician
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianName
          required: false
          in: formData
          description: beautician Name
          type: string
        - name: serviceIds
          required: false
          in: formData
          description: array of service Ids
          type: array
        - name: sortByCost
          required: false
          in: formData
          description: 0 for low to high and 1 high to low
          type: integer
        - name: sortByRating
          required: false
          in: formData
          description: 0 for low to high and 1 high to low
          type: integer
        - name: lat
          required: false
          in: formData
          description: latitude
          type: string
        - name: lng
          required: false
          in: formData
          description: longitude
          type: string
        - name: serviceId
          required: false
          in: formData
          description: service id 
          type: string
        - name: availableAt
          required: false
          in: formData
          description: required to filter beautician based on their availability date time (yyyy-mm-dd HH:mm:ss) 24 Hour format UTC
          type: string
        - name: isCrueltyFree
          required: false
          in: formData
          description: required to filter beautician based on crueltyFree makeup (0 or 1)
          type: integer
        - name: page
          required: false
          in: formData
          description: page number used for pagination
          type: integer

  /service/list:
    get:
      description: Used to get the list of top level services
      responses:  
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/createService:
    post:
      description: Used to create service by beautician
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          parameters:
        - name: parentServiceId
          required: true
          in: formData
          description: sub category's parent id
          type: integer  
        - name: serviceId
          required: true
          in: formData
          description: sub category / sub service id
          type: integer
        - name: duration
          required: true
          in: formData
          description: duration of service | needs to provide in mins i.e. lower value
          type: integer
        - name: cost
          required: true
          in: formData
          description: cost of service
          type: float
        - name: description
          required: false
          in: formData
          description: description of service
          type: string
        - name: tip
          required: false
          in: formData
          description: tip of service | max length is 500 characters
          type: string
        - name: sessionNumber
          required: false
          in: formData
          description: number of session if beautician has checked session flag | value ranges from 1 to 10 | required if session is applicable
          type: integer
        - name: timeBtwSession
          required: false
          in: formData
          description: min duration between session | should provide in days i.e. lower value | required if session is applicable
          type: integer
        - name: discount
          required: false
          in: formData
          description: discount on service if there is any
          type: float
        - name: discountStartDate
          required: false
          in: formData
          description: start date of discount | date format is Y-m-d H:i:s | required if discount is applicable
          type: string
        - name: discountedDays
          required: false
          in: formData
          description: end number of days for discount | required if discount is applicable
          type: integer

  /beautician/deleteService:
    delete:
      description: Used to upload user profile pic
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          parameters:
        - name: beauticianServiceId
          required: true
          in: formData
          description: id of the service created by beautician.
          type: integer  
        - name: timezone
          required: true
          in: formData
          description: current timezone
          type: string  
          
  /beautician/getService:
    get:
      description: used to get all the services created by beautician
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /beautician/updateService:
    post:
      description: Used to update description snd tips 
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          parameters:
        - name: id
          required: true
          in: formData
          description: primary id of the service created by beautician
          type: integer  
        - name: parentServiceId
          required: true
          in: formData
          description: sub category's parent id
          type: integer  
        - name: serviceId
          required: true
          in: formData
          description: sub category / sub service id
          type: integer
        - name: duration
          required: true
          in: formData
          description: duration of service | needs to provide in mins i.e. lower value
          type: integer
        - name: cost
          required: true
          in: formData
          description: cost of service
          type: float
        - name: description
          required: false
          in: formData
          description: description of service
          type: string
        - name: tip
          required: false
          in: formData
          description: tip of service | max length is 500 characters
          type: string
        - name: sessionNumber
          required: false
          in: formData
          description: number of session if beautician has checked session flag | value ranges from 1 to 10 | required if session is applicable
          type: integer
        - name: timeBtwSession
          required: false
          in: formData
          description: min duration between session | should provide in days i.e. lower value | required if session is applicable
          type: integer
        - name: discount
          required: false
          in: formData
          description: discount on service if there is any
          type: float
        - name: discountStartDate
          required: false
          in: formData
          description: start date of discount | date format is Y-m-d H:i:s | required if discount is applicable
          type: string
        - name: discountedDays
          required: false
          in: formData
          description: end number of days for discount | required if discount is applicable
          type: integer
          
  /beautician/setAvailability:
    post:
      description: Used to set availability for particular day by beautician pro
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: arrAvailabilityDetail[]
          required: true
          in: formData
          description: array consist of startDateTime, endDateTime and isAvailable
          type: array

  /beautician/updateServiceDescriptionTips:
    post:
      description: Used to create service by beautician
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: beauticianId
          required: true
          in: formData
          description: beautician Id
          type: integer
        - name: serviceId
          required: true
          in: formData
          description: sub category / sub service id
          type: integer
        - name: description
          required: true
          in: formData
          description: description of the service
          type: string
        - name: tip
          required: true
          in: formData
          description: tip of the service
          type: string

  /customer/getBeauticianExpertise:
    get:
      description: used to get beautician expertise data from customer end
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer

  /customer/getBeauticianKit:
    get:
      description: used to get beautician kit data from customer end
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer

  /customer/getBeauticianServices:
    get:
      description: used to get beautician services data from customer end
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer

  /customer/getBeauticianDetails:
    get:
      description: used to get beautician about us data and portfolio info
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer

  /service/subServices:
    get:
      description: used to get sub services of of the given parent services
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: parentServiceIdArr
          required: true
          in: formData
          description: array of parent service Ids
          type: array

  /flag/user:
    post:
      description: used to flag a user
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: flaggedUser
          required: true
          in: formData
          description: userId of the user who you want to flag
          type: integer
        - name: reasonId
          required: true
          in: formData
          description: reasonId is the Id of the predefined list of reasons
          type: integer

  /flag/reasons:
    get:
      description: get all the available flag reasons
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string

  /beautician/getMyFixhibition:
    get:
      description: get all the my fixhibitions available
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: page
          required: false
          in: formData
          description: page number used for pagination
          type: integer   

  /beautician/getAllFixhibition:
    get:
      description: get all the beautician's fixhibition available
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: page
          required: false
          in: formData
          description: page number used for pagination
          type: integer  

  /beautician/saveBeauticianFixhibition:
    post:
      description: post beautician fixhibition 
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: fixhibitionImage
          required: true
          in: formData
          description: upload jpeg,png,jpg file
          type: file
        

  /beautician/deleteFixhibition:
    delete:
      description: post beautician fixhibition 
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianFixhibitionId
          required: true
          in: formData
          description: fixhibition id 
          type: integer 

  /customer/getBeauticianFixhibition:
    get:
      description: get all the the fixhibitions available
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: page
          required: false
          in: formData
          description: page number for pagination 
          type: integer
  
  /beautician/getAvailability?date={date}:
    get:
      description: get all the the beautician available
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string        
        - name: date
          required: true
          in: path
          description: Today's date
          type: string


  /beautician/setPaymentDetails:
    post:
      description: create account and link account details and card 
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string        
        - name: cardToken
          required: true
          in: formData
          description: stripe cardToken
          type: string
        - name: bankToken
          required: true
          in: formData
          description: stripe bankToken
          type: string


  /customer/getBeauticianBookingAvailability?beauticianId={beauticianId}&startDateTime={startDateTime}&endDateTime={endDateTime}:
    get:
      description: get beautician availability along with booked slots
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string        
        - name: beauticianId
          required: true
          in: path
          description: beautician Id
          type: integer
        - name: startDateTime
          required: true
          in: path
          description: start date time to check for booking availability
          type: string
        - name: endDateTime
          required: true
          in: path
          description: end date time to check for booking availability
          type: string
          
  /user/rateReviewUser:
    post:
      description: API to rate and review beautician pro or customer
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string        
        - name: bookingId
          required: true
          in: formData
          description: booking id of the services of particular session
          type: string
        - name: userId
          required: true
          in: formData
          description: user id of the user whom rating is being given
          type: integer
        - name: rating
          required: true
          in: formData
          description: rating to be given | min:1 and max:5
          type: integer
        - name: comment
          required: true
          in: formData
          description: comment
          type: string
        - name: reasonId
          required: false
          in: formData
          description: reason id to be given in case rating is given 3 or below
          type: integer
          
  /user/ratingReason:
    get:
      description: API to get all rating master reason based on loggedin user
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
          
  /user/userPreviousRating?userId={userId}&page={page}:
    get:
      description: API to fetch user all previous rating
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: userId
          required: true
          in: path
          description: User id
          type: integer
        - name: page
          required: true
          in: path
          description: page id
          type: integer
          
  /service/markServiceComplete:
    get:
      description: API to mark a service completed
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: bookingId
          required: true
          in: formData
          description: Customer booking id
          type: integer

  /customer/markBeauticianFavourite:
    post:
      description: API to mark a beautician favourite
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer

  /customer/getFavouriteBeauticians:
    get:
      description: API to get FavouriteBeauticians of the given customer
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: page
          required: false
          in: formData
          description: page number for pagination
          type: integer

  /beautician/getCustomerDetails?customerId={customerId}:
    get:
      description: API to get customer details
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'  
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: customerId
          required: true
          in: path
          description: customerId
          type: integer

  /customer/bookService:
    post:
      description: book services
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'  
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer
        - name: bookingAddress
          required: true
          in: formData
          description: booking Address
          type: string
        - name: bookingArr
          required: true
          in: formData
          description: booking Array
          type: array
          
  /customer/getCustomerCurrentBooking?startDateTime={startDateTime}&endDateTime={endDateTime}:
    get:
      description: Used to get all current booking by given date and time | Status refers to 0=>'Pending', 1=>'Done But Payment Incomplete', 3=>'Payment Completed'
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: startDateTime
          required: true
          in: path
          description: start date and time | Y-m-d H:i:s Format in UTC
          type: string
        - name: endDateTime
          required: true
          in: path
          description: end date and time | Y-m-d H:i:s Format in UTC
          type: string

  /customer/customerCurrentBooking:
    delete:
      description: delete customer current booking 
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: customerBookingId
          required: true
          in: formData
          description: customer booking id 
          type: integer 
          
  /beautician/getPriceRange?beauticianId={beauticianId}:
    get:
      description: Used to get beautician price range
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: beauticianId
          required: true
          in: path
          description: beautician id
          type: integer

  /beautician/getBeauticianCurrentBooking?startDateTime={startDateTime}&endDateTime={endDateTime}:
    get:
      description: Used to get all current booking by given date and time | Status refers to 0=>'Pending', 1=>'Done But Payment Incomplete', 3=>'Payment Completed'
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: startDateTime
          required: true
          in: path
          description: start date and time | Y-m-d H:i:s Format in UTC
          type: string
        - name: endDateTime
          required: true
          in: path
          description: end date and time | Y-m-d H:i:s Format in UTC
          type: string

  /user/notifications?page={page}:
    get:
      description: Used to get notifications list
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: page
          required: false
          in: path
          description: page for pagination
          type: integer

  /user/raiseDispute:
    post:
      description: Used to raise dispute against a bookings
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: customerId
          required: true
          in: formData
          description: customerId
          type: integer
        - name: beauticianId
          required: true
          in: formData
          description: beauticianId
          type: integer
        - name: bookingId
          required: true
          in: formData
          description: bookingId
          type: integer
        - name: reason
          required: true
          in: formData
          description: reason for dispute
          type: string


  /notification/markRead:
    post:
      description: Used to mark a notification as read
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: notificationId
          required: true
          in: formData
          description: notificationId
          type: integer


  /notification/delete:
    delete:
      description: Used to delete a notification
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: notificationId
          required: true
          in: formData
          description: beauticianId
          type: integer


  /notification/count:
    get:
      description: Used to get unread notifications count
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string


  /user/cancelBooking:
    post:
      description: Used to cancel a booking
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: bookingId
          required: true
          in: formData
          description: bookingId
          type: integer
          
  /user/getUserPendingFeedback:
    get:
      description: fetch all user's pending feedback
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string

  /beautician/setBeauticianTimeliness:
    post:
      description: notify customer that whether beautician is on time or running late
      responses:
        200:
          description: Success response
        default:
          description: Unexpected error
          schema:
             $ref: '#/definitions/Error'
      parameters:
        - name: accessToken
          required: true
          in: header
          description: User access token 
          type: string
        - name: deviceType
          required: true
          in: header
          description: Device Type, 1 for iOS
          type: string
        - name: bookingId
          required: true
          in: formData
          description: bookingId
          type: string
        - name: delay
          required: false
          in: formData
          description: delay in beautician schedule (in minutes)
          type: string
          

  
        
