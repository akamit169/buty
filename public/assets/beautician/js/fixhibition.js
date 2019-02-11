//lightbox intialization

/**
 * 
 * @param {type} utc_datetime
 * @returns {String}
 */

    $('html').on('click','.upload-icon',function(){
        var my_fixhibition = $('.delete-caption').length;
        if(my_fixhibition >= 50){
            alert('You can upload maximum 50 fixhibitions.');
            return false;
        }
    });

    

$(function () {
    
    $("html").on('change', '.profile-img', function () {
        var file_data = this.files[0];
        if(!file_data.type.match('image')){
            alert('Please upload only jpeg/jpg/png files.');
            return false;
        }
        $('.loader').show();
        var form_data = new FormData(); // Creating object of FormData class
        form_data.append("fixhibitionImage", file_data);
        $.ajax({
            url: SITE_URL + '/beautician/saveBeauticianFixhibition',
            type: 'POST',
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: form_data,
            success: function (result) {
                $('.my-work').trigger('click');
                $('.loader').hide();

            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                if(response.success == false) {
                    alert(response.message);
                } else {
                    checkLoginStatus(response.statusCode);
                }
                $('.loader').hide();
            },
        });
        $('.fix-img-upload').show();
        $('.upload-work-wrap').hide();
    });
    
    $("html").on('click', '.delete-caption', function () {
        fixhibitionId = $(this).attr('data-id');
        $('.loader').show();
        $.ajax({
            url: SITE_URL + '/beautician/deleteFixhibition',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {
                    beauticianFixhibitionId:fixhibitionId
                },
            success: function (result) {
                $('.my-work').trigger('click');
                $('.loader').hide();

            },
            error: function (response) {
                var response = JSON.parse(response.responseText);
                checkLoginStatus(response.statusCode);
                $('.loader').hide();
            },
        });

        $(".lg-outer").hide();
        $(".lg-backdrop").hide();
        $('.fix-img-upload').show();
        $('.upload-work-wrap').hide();
    });

});


$(".tabs").click(function () {
    if ($(".tabs li a[href*='#mywork']").hasClass("active") & $('.upload-work-wrap').is(':hidden')) {
        $('.fix-img-upload').show();
    } else {
        $('.fix-img-upload').hide();
    }
});


var eventMyWork = 0;
var eventAllWork = 0;
var ajax_my_work = 0;
var ajax_all_work = 0;
var removeRow = 0;
var all_work_page = 0;
var my_work_page = 0;
var result_work;
var all_work_gallery;

$(document).ready(function () {

    getAllWork();

    $('html').on('click', '.all-work', function () {
        if (ajax_all_work == 0) {
            $('.all-work').addClass('active');
            $('.my-work').removeClass('active');
            all_work_page = 0;
            getAllWork();
        }
    });

    $('html').on('click', '.load-more', function () {
        if ($('.all-work').hasClass('active')) {
            if (eventAllWork == 0) {
                getAllWork();
            }
        } else {
            if (eventMyWork == 0) {
                getMyWork();
            }
        }
    });

    $('html').on('click', '.my-work', function () {
        if (ajax_my_work == 0) {
            $('.all-work').removeClass('active');
            $('.my-work').addClass('active');
            my_work_page = 0;
            getMyWork();
        }
    });

    /**
     * 
     * @param {type} jobs
     * @returns {String}
     */
    function loadAllWork(jobs) {
        show = '';
        $.each(jobs, function (index, n) {
            show += '<div class="col s6 m4 l3 captions" data-responsive="' + n.image + '" data-src="' + n.image + '" data-sub-html="<div class=\'caption-details\'>\n\
            <div class=\'profile-pic\'>\n\
            <img src=\'' + n.profilePic + '\'>\n\
            </div><div class=\'user-details\'><div class=\'name\'>'+ n.businessName +'</div></div></div>">';
            show += '<div class="img-box"><img src="' + n.image + '"></div>';
            show += ' <div class="caption-details">';
            show += '<div class="profile-pic"><img src="' + n.profilePic + '"></div>';
            show += '<div class="user-details">';
            show += '<div class="name">' + n.businessName + '</div>';
            show += ' </div></div>';
            show += '</div>';
        });

        return show;
    }

    /**
     * 
     * @param {type} jobs
     * @returns {String}
     */
    function loadMyWork(jobs) {

        var show = '';
        $.each(jobs, function (index, n) {
            show += '<div class="col s6 m4 l3 captions" data-responsive="' + n.image + '" data-src="' + n.image + '">';
            show += '<div class="img-box"><img src="' + n.image + '"><span class="delete-caption icon icon-delete" data-id="'+n.fixhibitionId+'"></span></div>';
            show += '</div>';
        });

        return show;
    }

    function getAllWork() {
        if (all_work_page == 0) {
            $('#lightgallery').html('');
            $('#lightgallery').empty();
            $('#lightgallery2').html('');
            $('#lightgallery2').empty();
        }
        $('.loader').show();
        eventAllWork = 1;
        ajax_all_work = 1;
        $.ajax({
            url: SITE_URL + '/beautician/getAllFixhibition',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            data: {
                page: all_work_page + 1
            },
            success: function (result) {
                if (result.data.data.length) {
                    result_work = result.data;
                    all_work_page = result_work.currentPage;

                    var notifications = loadAllWork(result_work.data);
                    if(all_work_gallery) {
                        $('#lightgallery').data('lightGallery').destroy(true);
                    }
                    $('#lightgallery').append(notifications);
                    
                    all_work_gallery = $('#lightgallery').lightGallery();
                    if (result_work.currentPage != result_work.lastPage) {
                        eventAllWork = 0;
                    }
                    
                } else {
                    $('#lightgallery').append('<span class="no-message-display"> <span data-icon="a" class="icon"></span>No Fixhibition </span>');
                }
                $('.loader').hide();
                $("body").css("overflow", "scroll");
                ajax_all_work = 0;
            },
            error: function () {
                var response = JSON.parse(response.responseText);
                checkLoginStatus(response.statusCode);
                ajax_all_work = 0;
                eventAllWork = 0;
                $("body").css("overflow", "scroll");
                $('.loader').hide();
            },
        });
    }
    var my_work_gallery;
    function getMyWork() {
        $('.deletion-div').hide();
        $('.selection-div').hide();
        if (my_work_page == 0) {
            $('#lightgallery').html('');
            $('#lightgallery').empty();
            $('#lightgallery2').html('');
            $('#lightgallery2').empty();
        }
        $('.loader').show();
        eventMyWork = 1;
        ajax_my_work = 1;
        $.ajax({
            url: SITE_URL + '/beautician/getMyFixhibition',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            type: 'POST',
            data: {
                page: my_work_page + 1
            },
            success: function (result) {

                result_work = result.data;
                my_work_page = result_work.currentPage;
                if (result_work.data.length) {
                    var jobs = loadMyWork(result_work.data);
                    if(my_work_gallery) {
                        $('#lightgallery2').data('lightGallery').destroy(true);
                    }
                    $('#lightgallery2').append(jobs);
                    my_work_gallery = $('#lightgallery2').lightGallery();
                    
                    if (result_work.currentPage != result_work.lastPage) {
                        eventMyWork = 0;
                    }
                    $('.fix-img-upload').show();
                } else {
                    $('#lightgallery2').append('<div class="upload-work-wrap"><div class="row">\n\
                                            <div class="col s12 m12">\n\
                                            Get noticed! Our unique way of showcasing your best work to Australian beauty junkies \n\
                                        </div></div>\n\
                                        <div class="upload-btn"><a class="btn-up-work" href="javascript:;">\n\
                                        <span> Add Photos<i class="upload-icon"></i></span>\n\
                                        <input class="profile-img" id="profile-img" type="file" class="file" name="fixhibition-image" /> </a></div></div>');
                }

                $("body").css("overflow", "scroll");
                $('.loader').hide();
                ajax_my_work = 0;

            },
            error: function () {
                eventMyWork = 0;
                ajax_my_work = 0;
                $("body").css("overflow", "scroll");
                $('.loader').hide();
            },
        });
    }



});


$(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
        if ($('.all-work').hasClass('active') && eventAllWork == 0 && removeRow == 0) {
            $("body").css("overflow", "hidden");
            $('.load-more').trigger('click');
        } else if ($('.my-work').hasClass('active') && eventMyWork == 0 && removeRow == 0) {
            $("body").css("overflow", "hidden");
            $('.load-more').trigger('click');
        } else {
            $("body").css("overflow", "scroll");
        }
    } else
        $("body").css("overflow", "scroll");
});