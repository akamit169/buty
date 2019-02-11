$(document).ready(function () {
    
    $('html').addClass('bg-green');
    //$('#forgotPassword').hide();
    var clear_form = function() {
        $('input[type="text"]').val('');
        $('input[type="email"]').val('');
        $('input[type="password"]').val('');
        $("label.error").hide();
        $(".error").removeClass("error");
    }

    //common validation message
    $.validator.addMethod("validEmail", function (value, element) {
        if (value == '')
            return true;
        var temp1;
        temp1 = true;
        var ind = value.indexOf('@');
        var str2 = value.substr(ind + 1);
        var str3 = str2.substr(0, str2.indexOf('.'));
        if (str3.lastIndexOf('-') == (str3.length - 1) || (str3.indexOf('-') != str3.lastIndexOf('-')))
            return false;
        var str1 = value.substr(0, ind);
        if ((str1.lastIndexOf('_') == (str1.length - 1)) || (str1.lastIndexOf('.') == (str1.length - 1)) || (str1.lastIndexOf('-') == (str1.length - 1)))
            return false;
        str = /(^[a-zA-Z0-9]+[\._-]{0,1})+([a-zA-Z0-9]+[_]{0,1})*@([a-zA-Z0-9]+[-]{0,1})+(\.[a-zA-Z0-9]+)*(\.[a-zA-Z]{2,3})$/;
        temp1 = str.test(value);
        return temp1;
    }, "Invalid Email");
    $.validator.addMethod("noSpace", function (value, element) {
        if (value == '') {
            return true;
        }
        return value.indexOf(" ") != 0 && value != "";
    }, "No space allowed");

    $.validator.addMethod("alphanumeric", function (value, element) {
        if (value != '') {
            return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
        }
        return true;
    }, "Invalid Password");
 
    //end of forgot password validation
    
       //hide error after 3 seconds
    setTimeout(function () {
        document.getElementById('.alert-danger').fadeOut('fast');
      
    }, 3000);
    
   

});