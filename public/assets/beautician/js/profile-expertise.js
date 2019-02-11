
$(document).ready(function () {
    // allow only  Alphabets A-Z a-z _ and space
    $('.alphaonly').bind('keyup blur',function(){ 
        var node = $(this);
//        node.val(node.val().replace(/[^A-Za-z_\s]/,'') ); 
    }   // (/[^a-z]/g,''
    );
    
    $('html').on('click', '.sbmExpertise', function () {
        var qualification = $('#qualification').val().trim();
        var speciality = $('#speciality').val().trim();
        if (qualification == '' && speciality == '') {
            $('#speciality').val('');
            $('#qualification').val('');
            $('#qualification').focus();
            alert('Please enter either qualification or speciality.');
            return false;
        }
        var arrQualificationName = [];
        if(qualification != '') {
            arrQualificationName[0] = qualification;
        }
        var arrSpecialityName = [];
        if(speciality != '') {
            arrSpecialityName[0] = speciality;
        }
        
        $('.loader').show();
        $.ajax({
            url: SITE_URL + '/beautician/profile/saveBeauticianQualificationNSpeciality',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {qualification: arrQualificationName, speciality: arrSpecialityName},
            success: function (response) {
                $('.loader').hide();
                $('#qualification').val('');
                $('#speciality').val('');
                if (response.success == true) {
                    
                    alert(response.message);
                    if(response.data.arrQualification.length > 0) {
                        var loopData = response.data.arrQualification;
                        var list = '';
                        $.each(loopData, function (key, value) {
                            list += '<div class="add-value"><i class="cross-icon delete-qualitification"></i>' + toTitleCase(value.qualification) + '<input type="hidden" name="qualificationId" value="' + value.id + '" /></div>';
                        });
                        $('.qualificationNameList').html('');
                        $('.qualificationNameList').html(list);
                    }
                    if(response.data.arrSpeciality.length > 0) {
                        var loopData = response.data.arrSpeciality;
                        var list = '';
                        $.each(loopData, function (key, value) {
                            list += '<div class="add-value"><i class="cross-icon delete-speciality"></i>' + toTitleCase(value.speciality) + '<input type="hidden" name="specialityId" value="' + value.id + '" /></div>';
                        });
                        $('.specialityNameList').html('');
                        $('.specialityNameList').html(list);
                    }
                    
                } else {
                    alert(response.message);
                }
            },
            error: function (response) {
                $('#qualification').val('');
                $('#speciality').val('');
                $('.loader').hide();
                if(typeof response  === 'object') {
                    response = response.responseText;
                    response = JSON.parse(response);
                }
                
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            },
        });
    });


    $('html').on('click touchstart', '.delete-qualitification', function () {
        var qualificationId = $(this).siblings('input[name="qualificationId"]').val();
        $('.loader').show();
        var arrQualificationId = [qualificationId];
        $.ajax({
            url: SITE_URL + '/beautician/profile/deleteBeauticianExpertise',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {deletedQualificationId: arrQualificationId},
            currentElementScope: this,
            success: function (response) {
                $('.loader').hide();
                alert('Your expertise has been deleted successfully.');
                if (response.success == true) {
                    $(this.currentElementScope).closest('div').remove();
                }
            },
            error: function (response) {
                $('.loader').hide();
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            }
        });

    });

    $('html').on('click touchstart', '.delete-speciality', function () {
        var specialityId = $(this).siblings('input[name="specialityId"]').val();
        $('.loader').show();
        var arrSpecialityId = [specialityId];
        $.ajax({
            url: SITE_URL + '/beautician/profile/deleteBeauticianExpertise',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {deletedSpecialityId: arrSpecialityId},
            currentElementScope: this,
            success: function (response) {
                $('.loader').hide();
                alert('Your expertise has been deleted successfully.');
                if (response.success == true) {
                    $(this.currentElementScope).closest('div').remove();
                }
            },
            error: function (response) {
                $('.loader').hide();
                if (response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
            }
        });

    });

});