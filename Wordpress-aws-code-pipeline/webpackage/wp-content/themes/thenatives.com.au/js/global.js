jQuery(document).ready(function($){
  if($(window).width()<767) {
      jQuery('.verticalTitle').delay(2000).fadeOut();
  }
  $('.userForm .buttonNext').click(function(e){
    $(this).parents('.userForm').find('input[type="submit"], button[type="submit"]').trigger('click');
    e.preventDefault();
    return false;
  });

  $(document).on('click','.forgotPassword > a',function(e){
    e.preventDefault();
    var content = '<div class="popupForgotPassword"><h2 class="titleH2">FORGOT PASSWORD</h2>' +
      '<form id="forgotPassowrd" action="POST" class="userForm">' +
      '<div class="groupControl">' +
      '<label for="email-forgot">EMAIL</label>' +
      '<input type="email" name="email" class="input" placeholder="Your Email" data-required="true">' +
      '</div>' +
      '<div class="mainControl">' +
      '<button type="submit" name="user-submit" value="Send" tabindex="14" class="user-submit">SEND <span>|</span></button>' +
      '<input type="hidden" name="redirect_to" value="'+$(this).parents('.mainControl').children('input[name="redirect_to"]').val()+'"></div>' +
      '</form></div>';
    formPopup(content);
  });

  $(document).on('click','.relatedSettings .btnNew',function(){
    $(this).children('ul').slideToggle();
    $(this).toggleClass('showSelect');
  });

  $(document).on('click','.relatedSection .btnDelete',function(e){
    e.preventDefault();
    $(this).addClass('move');
    var post = $(this).data('post');
    var positionPopup = {
      x_Position: $(this).offset().left,
      y_Position: $(this).offset().top
    };
    console.log(positionPopup)
    var content = '<h2>Are you sure you want to delete?<br><br>This action is irreversible.</h2>' +
      '<form id="deletePost" action="POST" class="userForm">' +
      '<div class="mainControl">' +
      '<button type="submit" name="user-submit" value="Send" tabindex="14" class="user-submit">DELETE <span>|</span></button>' +
      '<input type="hidden" name="post_id" value="'+post+'" tabindex="14"/>' +
      '<input type="hidden" name="redirect_to" value="'+$(this).parents('.mainControl').children('input[name="redirect_to"]').val()+'"></div>' +
      '</form></div>';
    formPopupDelete(content, positionPopup);
  });

  $(document).on('click','.postPurchase ul > li',function(){
    $('input#career_type').val($(this).data('type'));
  });

  $(document).on('click','.contentPurchase .hrefThumbnails a',function(e){
    e.preventDefault();
  });

  $(document).on('click','label.disabled',function(e){
    e.preventDefault();
  });

  var crop = '';
  if($('.divUploadImage').attr('src')){
    ajax_object.crop = '1';
    ajax_object.crop = $('.divUploadImage');
    $(ajax_object.crop).parents('label').addClass('disabled');
    $(ajax_object.crop).find('.removeImage').show();
    ajax_object.crop.croppie({
      url: ajax_object.crop.attr('src'),
      enableExif: true,
      mouseWheelZoom: true,
      showZoomer: false,
      viewport: {
        width: 304,
        height: 384,
      },
      boundary: {
        width: 304,
        height: 384,
      }
    });
  }

  $(document).on('click','button.removeImage',function(e){
    var $this = $(this);
    var parent = $this.parent();
    $(parent).siblings('input').attr('data-required','true');
    $(parent).siblings('input').val('');
    ajax_object.crop.croppie('destroy');
    $this.hide();
    $this.parents('label').removeClass('disabled');
  });

  $(document).on('change','.userForm input.uploadImage',function(){
    var upload = this;
    if (upload.files && upload.files[0]) {
      var check = true;
      var file = upload.files[0];
      if(file['type'] != 'image/jpeg' && file['type'] != 'image/jpg' && file['type'] != 'image/png') {
        check = false;
        if($(upload).siblings('input[name="image_id"]').hasClass('error')){
          $(upload).siblings('span.form-error-message').remove();
        }
        $(upload).siblings('input[name="image_id"]').addClass('error');
        $(upload).parent().append('<span class="form-error-message">Incorrect file format. JPG or PNG accepted.</span>');
      }
      if(check) {
        var _URL = window.URL || window.webkitURL;
        var file, img;
        if ((file = this.files[0])) {
          img = new Image();
          img.onload = function () {
            var maxsize = $(upload).attr('max');
            var maxtype = maxsize.substr(maxsize.length - 1, 1);
            if (!Number.isInteger(maxtype)) {
              maxsize = maxsize.replace(maxtype, '', maxsize);
            }
            switch (maxtype) {
              case 'K' :
                maxsize *= 1024;
                break;
              case 'M' :
                maxsize *= 1024 * 1024;
                break;
              case 'G' :
                maxsize *= 1024 * 1024 * 1024;
                break;
            }
            if (file['size'] > maxsize) {
              check = false;
              if ($(upload).siblings('input[name="image_id"]').hasClass('error')) {
                $(upload).siblings('span.form-error-message').remove();
              }
              $(upload).siblings('input[name="image_id"]').addClass('error');
              $(upload).parent().append('<span class="form-error-message">Image too large, maximum size: ' + $(upload).attr('max') + 'B</span>');
            }
            if (this.width < 835) {
              check = false;
              if ($(upload).siblings('input[name="image_id"]').hasClass('error')) {
                $(upload).siblings('span.form-error-message').remove();
              }
              $(upload).siblings('input[name="image_id"]').addClass('error');
              $(upload).parent().append('<span class="form-error-message">Image too small, mininum width: 835px</span>');
            }
            if (check) {
              var reader = new FileReader();
              reader.onload = function (e) {
                $(upload).siblings('input').removeAttr('data-required');
                ajax_object.crop = $('.divUploadImage');
                ajax_object.crop.croppie({
                  url: e.target.result,
                  enableExif: true,
                  mouseWheelZoom: true,
                  showZoomer: false,
                  viewport: {
                    width: 304,
                    height: 384,
                  },
                  boundary: {
                    width: 304,
                    height: 384,
                  }
                });
                $(upload).siblings('.divUploadImage').find('.removeImage').show();
                $(upload).siblings('input[name="image_id"]').removeClass('error');
                $(upload).siblings('span.form-error-message').remove();
              }
              reader.readAsDataURL(upload.files[0]);
              $(upload).parents('label').addClass('disabled');
            }
          };
          img.src = _URL.createObjectURL(file);
        }
      }
    }
  });
    $('.textarea-editor').on('focusin', function() {
        if(!$(this).find('ul').length){
            $(this).html('<ul><li></li></ul>');
        }
        before = $(this).html();
    }).on('focusout', function(){
        if(!$(this).text()){
            var textarea = 'textarea#' + $(this).attr('for');
            $(this).html('<span class="place-holder">'+$(textarea).attr('placeholder')+'</span>');
            $(textarea).val('');
            $(textarea).trigger('change');
        }
        else {
            var textarea = 'textarea#' + $(this).attr('for');
            $(textarea).val($(this).html());
            $(textarea).trigger('change');
        }
    }).on('keyup patse cut', function() {
        if (before != $(this).html()) {
            var textarea = 'textarea#'+$(this).attr('for');
            $(textarea).val($(this).html());
            $(textarea).trigger('change');
        }
    }).on('keydown',function(e){
        if(e.key == 'Backspace' && !$(this).text()) {
            e.preventDefault();
            return false;
        }
    });
});