jQuery(document).ready(function($) {
  $(window).scroll(function () {
    if (!$('.lazyLoad').hasClass('running') && $('.lazyLoad').length) {
      var action = 'homepage_lazyload';
      if ($('body').hasClass('post-type-archive-career')) {
        action = 'archive_career_lazyload';
      }
      if ($('body').hasClass('post-type-archive-event')) {
        action = 'archive_event_lazyload';
      }
      if ($('body').hasClass('post-type-archive-sale')) {
        action = 'archive_sale_lazyload';
      }
      if($('#lazyRelatedPost.lazyLoad').length){
        action = 'related_post_lazyload';
      }
      if($('#lazyRelatedCareer.lazyLoad').length){
        action = 'related_career_lazyload';
      }
      if($('#lazyRelatedSale.lazyLoad').length){
        action = 'related_sale_lazyload';
      }
      if($('#lazyRelatedEvent.lazyLoad').length){
        action = 'related_event_lazyload';
      }
      var _this = $('.lazyLoad');
      if ($(window).scrollTop() + $(window).height() >= _this.position().top) {
        $.ajax({
          url: ajax_object.url,
          type: 'post',
          data: {
            action: action,
            time: _this.data('time'),
            offset: _this.data('offset'),
            post_in: _this.data('post-in'),
            search: _this.data('search'),
            tax: _this.data('tax'),
          },
          beforeSend: function () {
            _this.addClass('running');
          },
          success: function (html) {
            _this.after(html);
            _this.remove();
            addStyle();
          }
        });
      }
    }
  });

  $(document).on('change','select#filter-post',function(e) {
    var $this = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'filter_category_post',
        style: $this.data('style'),
        cat: $(this).val(),
      },
      beforeSend: function () {
      },
      success: function (html) {
        $this.parents('main.vertical-post').children().eq(1).remove();
        $('#footer').children().not('.footer-main').remove();
        $this.parents('main').append(html);
        var lazy = $('.lazyLoad').detach();
        lazy.appendTo("footer#footer");
        addStyle();
      }
    });
  });

  $(document).on('submit','#registerForm',function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'register_ajax',
        data: form.serialize(),
      },
      beforeSend: function () {
        form.find('.submitLoading').show();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
      },
      success: function (json) {
        json = JSON.parse(json);
        if(json['status'] == 'success'){
          window.location.href = json['redirect'];
        }
        else {
          if(json['error'] == 'is_existed'){
            form.find('#user_email').addClass('error');
            form.find('#user_email').parent().append('<span class="form-error-message">This email is already registered</span>');
          }
          else {
            form.find('#user_email').removeClass('error');
            form.find('#user_email').siblings().remove();
          }
          if(json['error'] == 'send_mail_failed') {

          }
        }
        form.find('.submitLoading').hide();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
      }
    });
  });

  $(document).on('submit','#loginForm',function(e){
    e.preventDefault();
    var form = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'login_ajax',
        data: form.serialize(),
      },
      beforeSend: function () {
        /*form.find('.submitLoading').show();*/
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
      },
      success: function (json) {
        json = JSON.parse(json);
        if(json['status'] == 'success'){
          window.location.href = json['redirect'];
        }
        else {
          if(json['error'] == 'incorrect_password'){
            form.find('#user_pass').addClass('error');
            form.find('#user_pass').parent().append('<span class="form-error-message">Incorrect password.</span>');
          }
          else {
            form.find('#user_pass').removeClass('error');
            form.find('#user_pass').siblings().remove();
          }
          if(json['error'] == 'invalid_email'){
            form.find('#user_login').addClass('error');
            form.find('#user_login').parent().append('<span class="form-error-message">This account does not exist.</span>');
          }
          else {
            form.find('#user_login').removeClass('error');
            form.find('#user_login').siblings().remove();
          }
        }
        form.find('.submitLoading').hide();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
      }
    });
  });

  $(document).on('submit','#settingUser',function(e){
    e.preventDefault();
    var form = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'account_settings_ajax',
        data: form.serialize(),
      },
      beforeSend: function () {
        form.find('.submitLoading').show();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
      },
      success: function (json) {
        json = JSON.parse(json);
        if(json['status'] == 'success'){
          if(json['redirect']) {
            window.location.href = json['redirect'];
          }
        }
        else {
          console.log('[name="'+json['object']+'"]');
          form.find('[name="'+json['object']+'"]').after('<span class="form-error-message">'+json['message']+'</span>')
        }
        form.find('.submitLoading').hide();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
      }
    });
  });
  $(document).on('submit','#deletePost',function(e){
    e.preventDefault();
    var form = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'delete_post',
        data: form.serialize(),
      },
      beforeSend: function () {
        form.find('.submitLoading').show();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
      },
      success: function (json) {
        json = JSON.parse(json);
        if(json['status'] == 'success'){
          if(json['redirect']) {
            window.location.href = json['redirect'];
          }
        }
        form.find('.submitLoading').hide();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
      }
    });
  });

  $(document).on('change','main#template-careers .hidden-xs.chooseOption select',function(){
    var $this = $(this);
    var name = $this.attr('name');
    var mobileFilter = $('main#template-careers .itemFilter .filterOptions select[name="'+name+'"]');
    if(mobileFilter.val() != $(this).val()){
      mobileFilter.val($(this).val());
      mobileFilter.siblings('.select-styled').html($this.siblings('.select-styled').html());
    }
    var data = {action:'filter_career'};
    $this.parents('.margin-space').find('.hidden-xs.chooseOption select').each(function(){
      data[$(this).attr('name')] = $(this).val();
    })
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: data,
      beforeSend: function () {
      },
      success: function (html) {
        $('#footer').children().not('.footer-main').remove();
        $('main#template-careers .main-body').html(html);
        var lazy = $('.lazyLoad').detach();
        lazy.appendTo("footer#footer");
        addStyle();
      }
    });
  });

  $(document).on('change','main#template-careers .itemFilter .filterOptions select',function() {
    var $this = $(this);
    var name = $this.attr('name');
    var desktopFilter = $('main#template-careers .hidden-xs.chooseOption select[name="'+name+'"]');
    if(desktopFilter.val() != $(this).val()){
      desktopFilter.val($(this).val());
      desktopFilter.siblings('.select-styled').html($this.siblings('.select-styled').html());
      desktopFilter.trigger('change');
    }
  });

  $(document).on('change','main#template-events .hidden-xs.chooseOption select',function(){
    var $this = $(this);
    var name = $this.attr('name');
    var mobileFilter = $('main#template-events .itemFilter .filterOptions select[name="'+name+'"]');
    if(mobileFilter.val() != $(this).val()){
      mobileFilter.val($(this).val());
      mobileFilter.siblings('.select-styled').html($this.siblings('.select-styled').html());
    }
    var data = {action:'filter_event'};
    $this.parents('.margin-space').find('.hidden-xs.chooseOption select').each(function(){
      data[$(this).attr('name')] = $(this).val();
    })
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: data,
      beforeSend: function () {
      },
      success: function (html) {
        $('#footer').children().not('.footer-main').remove();
        $('main#template-events .main-body').html(html);
        var lazy = $('.lazyLoad').detach();
        lazy.appendTo("footer#footer");
        addStyle();
      }
    });
  });

  $(document).on('change','main#template-events .itemFilter .filterOptions select',function() {
    var $this = $(this);
    var name = $this.attr('name');
    var desktopFilter = $('main#template-events .hidden-xs.chooseOption select[name="'+name+'"]');
    if(desktopFilter.val() != $(this).val()){
      desktopFilter.val($(this).val());
      desktopFilter.siblings('.select-styled').html($this.siblings('.select-styled').html());
      desktopFilter.trigger('change');
    }
  });

  $(document).on('change','main#template-sales .hidden-xs.chooseOption select',function(){
    var $this = $(this);
    var name = $this.attr('name');
    var mobileFilter = $('main#template-sales .itemFilter .filterOptions select[name="'+name+'"]');
    if(mobileFilter.val() != $(this).val()){
      mobileFilter.val($(this).val());
      mobileFilter.siblings('.select-styled').html($this.siblings('.select-styled').html());
    }
    var data = {action:'filter_sale'};
    $this.parents('.margin-space').find('.hidden-xs.chooseOption select').each(function(){
      data[$(this).attr('name')] = $(this).val();
    })
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: data,
      beforeSend: function () {
      },
      success: function (html) {
        $('#footer').children().not('.footer-main').remove();
        $('main#template-sales .main-body').html(html);
        var lazy = $('.lazyLoad').detach();
        lazy.appendTo("footer#footer");
        addStyle();
      }
    });
  });

  $(document).on('change','main#template-sales .itemFilter .filterOptions select',function() {
    var $this = $(this);
    var name = $this.attr('name');
    var desktopFilter = $('main#template-sales .hidden-xs.chooseOption select[name="'+name+'"]');
    if(desktopFilter.val() != $(this).val()){
      desktopFilter.val($(this).val());
      desktopFilter.siblings('.select-styled').html($this.siblings('.select-styled').html());
      desktopFilter.trigger('change');
    }
  });

  $(document).on('click','.btnNextPurchase',function(e){
    e.preventDefault();
    var form = '';
    var action = '';
    if($('form#purChaseCareer').length) {
      form = $('form#purChaseCareer');
      action = 'post_job_purchase';
    }
    else{
      if($('form#formCheckoutCareer').length) {
        form = $('form#formCheckoutCareer');
        action = 'post_job_checkout';
        Stripe.setPublishableKey('pk_test_6pRNASCoBOKtIshFeQd4XMUh');

        Stripe.createToken({
          number: $('#card_number').val(),
          cvc: $('#card_cvc').val(),
          exp_month: $('#date-mm').val(),
          exp_year: $('#date-yy').val()
        }, function(status, response) {
          if (response.error) {
            $(".payment-errors").html(response.error.message);
          } else {
            alert('sucess');
            // Response JSON FROM Stripe
            console.log(response);
            var token = response['id'];
            console.log(token);
          }
        });
      }
    }
    if(form){
      $.ajax({
        url: ajax_object.url,
        type: 'post',
        data: {
          action: action,
          data: form.serialize(),
        },
        beforeSend: function () {
          form.find('.submitLoading').show();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
        },
        success: function (json) {
          json = JSON.parse(json);
          if(json['status'] == 'success'){
            if(json['redirect']) {
              window.location.href = json['redirect'];
            }
          }
          form.find('.submitLoading').hide();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
        }
      });
    }
  });

  /*$(document).on('change','.userForm input.uploadImage',function(){
    var $upload = $(this);
    if(!$upload.val()){
      $upload.siblings('.divUploadImage').html('');
      $upload.siblings('input[name="' + $upload.attr('id') + '_id"]').val('');
    }
    else {
      var data = $upload.prop('files')[0];
      var form_data = new FormData();
      form_data.append('action', 'upload_image_media');
      form_data.append('file', data);
      $.ajax({
        url: ajax_object.url,
        type: 'post',
        contentType: false,
        processData: false,
        data: form_data,
        beforeSend: function () {
        },
        success: function (json) {
          json = JSON.parse(json);
          if (json['status'] == 'success') {
            $upload.siblings('.divUploadImage').html('<img src="' + json["link"] + '" alt="Image Uploaded"  style="width: 100%;height: 100%; object-fit:cover"/>');
            $upload.siblings('input[name="' + $upload.attr('id') + '_id"]').val(json['val']);
            $upload.siblings('.form-error-message').remove();
            $upload.removeClass('error');
          }
          else {
            $upload.siblings('.form-error-message').remove();
            $upload.siblings('.divUploadImage').html('');
            $upload.addClass('error');
            $upload.parent().append('<span class="form-error-message">'+json['message']+'</span>');
          }
        }
      });
    }
  });
  */

  $(document).on('submit','#forgotPassowrd',function(e){
    e.preventDefault();
    var form = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'forgot_password',
        data: form.serialize(),
      },
      beforeSend: function () {
        form.find('.submitLoading').show();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
      },
      success: function (json) {
        json = JSON.parse(json);
        if(json['status'] == 'success'){
          var content = json['message'];
          formPopup(content);
        }
        else {
          form.find('input[name="email"]').addClass('error');
          form.find('input[name="email"]').parent().append('<span class="form-error-message">'+json['message']+'</span>');
        }
        form.find('.submitLoading').hide();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
      }
    });
  });

  $(document).on('submit','#resetPassUser',function(e){
    e.preventDefault();
    var form = $(this);
    $.ajax({
      url: ajax_object.url,
      type: 'post',
      data: {
        action: 'reset_password',
        data: form.serialize(),
      },
      beforeSend: function () {
        form.find('.submitLoading').show();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
      },
      success: function (json) {
        json = JSON.parse(json);
        if(json['status'] == 'success'){
          var content = json['message'];
          formPopup(content);
        }
        else {
          form.find('input[name="reset_email"]').addClass('error');
          form.find('input[name="reset_email"]').parent().append('<span class="form-error-message">'+json['message']+'</span>');
        }
        form.find('.submitLoading').hide();
        form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
      }
    });
  });

  $(document).on('submit','#formPostAJob,#post-sale,#post-event,#purChaseCareer',function(e){
    e.preventDefault();
    var form = $(this);
    var form_data = new FormData();
    form_data.append('data', form.serialize());
    if(form.attr('id') == 'purChaseCareer'){
      form_data.append('action', 'post_job_purchase');
      $.ajax({
        url: ajax_object.url,
        type: 'post',
        contentType: false,
        processData: false,
        data: form_data,
        beforeSend: function () {
          form.find('.submitLoading').show();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
        },
        success: function (json) {
          json = JSON.parse(json);
          if (json['status'] == 'success') {
            if (json['redirect']) {
              window.location.href = json['redirect'];
            }
          }
          form.find('.submitLoading').hide();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
        }
      });
    }
    else {
      var data = form.find('#image').prop('files')[0];
      form_data.append('file', data);
      if (form.attr('id') == 'formPostAJob') {
        form_data.append('action', 'post_job_ajax');
      }
      if (form.attr('id') == 'post-sale') {
        form_data.append('action', 'post_sale_ajax');
      }
      if (form.attr('id') == 'post-event') {
        form_data.append('action', 'post_event_ajax');
      }
      ajax_object.crop.croppie('result', {
        type: 'base64',
        size: 'viewport'
      }).then(function (resp) {
        form_data.append('image', resp);
        $.ajax({
          url: ajax_object.url,
          type: 'post',
          contentType: false,
          processData: false,
          data: form_data,
          beforeSend: function () {
            form.find('.submitLoading').show();
            form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
          },
          success: function (json) {
            json = JSON.parse(json);
            if (json['status'] == 'success') {
              if (json['redirect']) {
                window.location.href = json['redirect'];
              }
            }
            form.find('.submitLoading').hide();
            form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
          }
        });
      });
    }
  });

  /*$(document).on('submit','#post-sale',function(e){
    e.preventDefault();
    var form = $(this);
    ajax_object.crop.croppie('result', {
      type: 'base64',
      size: 'viewport'
    }).then(function (resp) {
      $.ajax({
        url: ajax_object.url,
        type: 'post',
        data: {
          action: 'post_sale_ajax',
          data: form.serialize(),
          image: resp,
        },
        beforeSend: function () {
          form.find('.submitLoading').show();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
        },
        success: function (json) {
          json = JSON.parse(json);
          if(json['status'] == 'success'){
            if(json['redirect']) {
              console.log('ok');
              window.location.href = json['redirect'];
            }
          }
          form.find('.submitLoading').hide();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
        }
      });
    });
  });

  $(document).on('submit','#post-event',function(e){
    e.preventDefault();
    var form = $(this);
    ajax_object.crop.croppie('result', {
      type: 'base64',
      size: 'viewport'
    }).then(function (resp) {
      $.ajax({
        url: ajax_object.url,
        type: 'post',
        data: {
          action: 'post_event_ajax',
          data: form.serialize(),
          image: resp,
        },
        beforeSend: function () {
          form.find('.submitLoading').show();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', true);
        },
        success: function (json) {
          json = JSON.parse(json);
          if(json['status'] == 'success'){
            if(json['redirect']) {
              window.location.href = json['redirect'];
            }
          }
          form.find('.submitLoading').hide();
          form.find('input[type="submit"],button[type="submit"]').prop('readonly', false);
        }
      });
    });
  });*/
});