function addStyle() {
  jQuery('.hrefThumbnails .imageThumbnails .tagName.featured-section, .tagName, .titleText.postType,.sliderPostItem.slick-current').each(function () {
    var _this = jQuery(this);
    jQuery('#header nav .menu-category-menu-container #menu-category-menu li a > span').each(function () {
      var text = _this.text();
      if(_this.hasClass('sliderPostItem') && _this.hasClass('slick-current')){
        text = jQuery('.articleShoot .articleMeta .tagName').text();
      }
      if (jQuery.trim(jQuery(this).text().toLowerCase()) === text.toLowerCase()) {
        if(_this.hasClass('sliderPostItem') && _this.hasClass('slick-current')){
          var $color = jQuery(this).data('color');
          _this.siblings().find('.numberSliderPost').css({'color':''});
          _this.siblings().find('.descriptionSliderPost').css({'color':''});
          _this.siblings().find('.imageSliderPost').css({'border-color':''});
          _this.find('.numberSliderPost').css({'color': $color});
          _this.find('.descriptionSliderPost').css({'color': $color});
          _this.find('.imageSliderPost').css({'border-color': $color});
        }
        else {
          _this.css({'background-color': jQuery(this).data('bg'), 'color': jQuery(this).data('color')})
        }
        return;
      }
    });
  });
}

function formPopup(content,time){
  removeFormPopup();
  var html = '<div id="formPopup" style="display: none;"><div class="formPopupWrapper"><button class="btnClose" onclick="removeFormPopup()">&times;</button><div class="contentPopup">'+content+'</div></div></div>';
  jQuery('body').append(html);
  if(!time) time = 500;
  jQuery('#formPopup').fadeIn( time );
}

function formPopupDelete (content,params,time) {
  removeFormPopup();
  var totalXPosition = params.x_Position - 662 + 17;
  var html = '<div id="formPopup" class="formDelete" style="top:' +  params.y_Position +'px; left:' + (totalXPosition > 0 ? totalXPosition : 0) + 'px"><div class="formPopupWrapper"><button class="btnClose" onclick="removeFormPopupDelete()">&times;</button><div class="contentPopup">'+content+'</div></div></div>';
  jQuery('body').append(html);
  if(!time) time = 400;
  setTimeout(function () {
    jQuery('#formPopup').addClass('showForm')
  }, time);
}

function removeFormPopupDelete () {
  jQuery('#formPopup').removeClass('showForm');
  setTimeout(function () {
    jQuery('body').children('#formPopup').remove();
  }, 400);
}

function removeFormPopup(){
  jQuery('body').children('#formPopup').remove();
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}



jQuery(document).ready(function($){
  $('.userForm select').each(function () {
    var $this = $(this);
    var numberOfOptions = $(this).children('option').length;
    var val = $(this).val();
    $this.hide();
    $this.wrap('<div class="userFormSelectWrapper"></div>')
    $this.after('<div class="userFormSelect">' + $this.children('option').eq(0).text() + '</div>')
    var $styledSelect = $this.next('div.userFormSelect')
    var $list = $('<ul />', {'class': 'userFormSelectOptions'}).insertAfter($styledSelect)
    $list.hide();
    for (var i = 0; i < numberOfOptions; i++) {
      var active = (val == $this.children('option').eq(i).val())?'active':'';
      if(val == $this.children('option').eq(i).val()){
        $styledSelect.text($this.children('option').eq(i).text());
      }
      $('<li />', {
        text: $this.children('option').eq(i).text(),
        rel: $this.children('option').eq(i).val(),
        class: active,
      }).appendTo($list)
    }
    var $listItems = $list.children('li')
    $styledSelect.click(function (e) {
      $(document).find('.userFormSelectOptions').not($list).hide();
      $(document).find('.userFormSelect').not(this).removeClass('active');
      e.stopPropagation()
      $('div.select-styled.active').not(this).each(function () {
        $(this).removeClass('active').next('ul.userFormSelectOptions').hide()
      })
      $(this).toggleClass('active').next('ul.userFormSelectOptions').toggle()
    })
    $listItems.click(function (e) {
      e.stopPropagation();
      $styledSelect.text($(this).text()).removeClass('active');
      if( (val == $(this).attr('rel') || val != $(this).attr('rel') || $(this).attr('rel') == '') && !$(this).hasClass('active') ) {
        $list.children('li').removeClass('active');
        $(this).addClass('active');
        $this.val($(this).attr('rel'));
        $this.trigger('change');
      }
      $list.hide()
    })
    $(document).click(function () {
      $styledSelect.removeClass('active')
      $list.hide()
    })
  });

  $(document).on('click','form.userForm input[type="submit"],form.userForm button[type="submit"]',function(e) {
    if($(this).attr('readonly') || $(this).hasClass('disabled')){
      e.preventDefault();
      return false;
    }
    if ($(this).parents('form').length) {
      var form = $(this).parents('form');
      var check = true;
      form.find('.groupControl input, .groupControl select, .groupControl textarea').each(function () {
        var each = $(this);
        if (each.attr('data-required')) {
          if (!$.trim(each.val())) {
            if(check) {
              check = false;
            }
            if (!each.hasClass('error')) {
              each.addClass('error');
              each.parent().append('<span class="form-error-message">This field is mandatory</span>');
            }
          }
          else {
            each.removeClass('error');
            each.siblings('span.form-error-message').remove();
            if (each.data('type') == 'email' || each.attr('type') == 'email') {
              if (!isEmail(each.val())) {
                each.addClass('error');
                each.parent().append('<span class="form-error-message">This field is invalid</span>');
                if(check) {
                  check = false;
                }
              }
            }
            if(each.data('equal')){
              var relation = form.find('input[name="'+(each.data('equal'))+'"]');
              if(each.val() != relation.val()){
                if(check) {
                  check = false;
                }
                var text = relation.attr('placeholder');
                text = text?text:'the relation field';
                each.addClass('error');
                each.parent().append('<span class="form-error-message">This field is not equal to '+text+'</span>');
              }
              else {
                each.removeClass('error');
                each.siblings('.form-error-message').remove();
              }
            }
          }
        }
      });
      if (!check) {
        e.preventDefault();
        return false;
      }
    }
  });

  if(typeof jQuery('.userForm input.inputDate').datepicker === 'function') {
    jQuery('.userForm input.inputDate').datepicker({
      dateFormat: 'DD MM d',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      minDate: 0,
    });
  }
  if(typeof jQuery('.userForm input.inputDate').datepicker === 'function') {
    jQuery('.userForm input.inputDateYear').datepicker({
      dateFormat: 'dd.mm.yy',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      minDate: 0,
    });
  }
  if(typeof jQuery('.userForm input.inputDate').timepicker === 'function') {
    jQuery('.userForm input.inputTime').timepicker({
      timeFormat: 'hh:mmTT',
      oneLine: true,
      ampm: true,
    });
  }
});

(function(){
  // Your base, I'm in it!
  var originalAddClassMethod = jQuery.fn.addClass;

  jQuery.fn.addClass = function(){
    // Execute the original method.
    var result = originalAddClassMethod.apply( this, arguments );
    // trigger a custom event
    jQuery(this).trigger('cssClassChanged');

    // return the original result
    return result;
  }
})();