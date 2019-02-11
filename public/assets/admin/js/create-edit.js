$(function () {
    var $image1 = $('#image1');
    $('html').on('click', '.customFile', function () {
        if ($('.img-wrap').find('img').length) {
            var modalImg = $('.img-wrap').find('img').attr('src');
            console.log(modalImg);
            $image1.attr('src', modalImg);
        }
    });

    /*var cropBoxData;
     var canvasData;*/

    $('#cropModal1').on('shown.bs.modal', function (event) {
       
        $image1.cropper({
            autoCropArea: 0.5,
            aspectRatio: 1 / 1,
            minCropBoxWidth: 200,
            checkOrientation: true,
            minCropBoxHeight: 200
        });
        $('#doneButton').attr('disabled', true);
        var modal1 = $(this);
        var trigger1 = $(event.relatedTarget);


        // Methods
        $('.docs-buttons').on('click', '[data-method]', function () {

            var $this = $(this);
            var data = $this.data();
            var $target;
            var result;

            if ($this.prop('disabled') || $this.hasClass('disabled')) {
                return;
            }

            if ($image1.data('cropper') && data.method) {
                data = $.extend({}, data); // Clone a new one

                if (typeof data.target !== 'undefined') {
                    $target = $(data.target);

                    if (typeof data.option === 'undefined') {
                        try {
                            data.option = JSON.parse($target.val());
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                result = $image1.cropper(data.method, data.option, data.secondOption);

                switch (data.method) {
                    case 'scaleX':
                    case 'scaleY':
                        $(this).data('option', -data.option);
                        break;

                    case 'getCroppedCanvas':
                        if (result) {
                            //result.toDataURL('image/jpeg')
                           
                            $(".img-wrap").html('<img src=' + result.toDataURL('image/jpeg') + ' />');
                        
                         
                            modal1.modal('hide');
                            $image1.cropper('clear');
//                            $('#uploaded_image_input').remove();
                            document.getElementById('image_input').value=result.toDataURL('image/jpeg');
                        }

                        break;
                }

                if ($.isPlainObject(result) && $target) {
                    try {
                        $target.val(JSON.stringify(result));
                    } catch (e) {
                        console.log(e.message);
                    }
                }

            }
        });
        // Import image
        var $inputImage = $('#inputImage1');
        var URL = window.URL || window.webkitURL || self.URL || self;
        var blobURL;

        if (URL) {
            $inputImage.change(function () {
                $('#doneButton').attr('disabled', false);
                var files = this.files;
                var file;

                if (!$image1.data('cropper')) {
                    return;
                }

                if (files && files.length) {
                    file = files[0];

                    if (/^image\/\w+$/.test(file.type)) {
                        blobURL = URL.createObjectURL(file);
                        $image1.one('built.cropper', function () {

                            // Revoke when load complete
                            URL.revokeObjectURL(blobURL);
                        }).cropper('reset').cropper('replace', blobURL);
                        $inputImage.val('');
                    } else {
                        window.alert('Please choose an image file.');
                    }
                }
            });
        } else {
            $inputImage.prop('disabled', true).parent().addClass('disabled');
        }
    }).on('hidden.bs.modal', function () {
        /*cropBoxData = $image.cropper('getCropBoxData');
         canvasData = $image.cropper('getCanvasData');*/
        $image1.cropper('destroy');
    });

});