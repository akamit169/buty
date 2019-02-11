$(function(){
          function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var imageElem = document.createElement("img");
                reader.onload = function (e) {
                    imageElem.setAttribute('src', e.target.result);
                    $('.uploaded-image').append(imageElem);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#profile-img").change(function(){
            readURL(this);
            $('.work-service').fadeIn();
            $('.upload-btn').hide();
        });

        $('html').on('click', '.delete-caption', function(){
          $("#profile-img").val('');
          $('.uploaded-image').find('img').remove();
          $('.work-service').hide();
          $('.upload-btn').fadeIn();
        });

        $('form').on('submit',function(){
            $(this).find('button[type=submit]').attr('disabled',true);
            localStorage.uploadedPortofolioServiceId = $('#services').val();
            $('.loader').show();
        });


      });
