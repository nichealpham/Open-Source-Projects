jQuery(document).ready(function ($) {
    var h_height = $('header#header').outerHeight();
    jQuery(".SheArcH").click(function(e) {
        jQuery("#menu").find('#searchButton').trigger("click");
    });
    $(document).on('change','.gform_fields input[type="file"]', function(e){
        var file_path = jQuery(this)[0].value;

        if(file_path){
            $(this).parent().addClass('has-value');
        } else {
            $(this).parent().removeClass('has-value');
        }
    });

    jQuery('.buttonMenu button').add($('nav .closeButton')).on('click', function () {
        if (jQuery('nav#menu').hasClass('open')) {
            jQuery('nav#menu').removeClass('open');
            jQuery('.openMenu').remove();
            jQuery('body').removeClass('nonScroll');

        }
        else {
            jQuery('nav#menu').addClass('open')
            jQuery('body').append('<div class="openMenu"></div>')
            jQuery('body').addClass('nonScroll')
        }
    })

    jQuery(document).on('click', '.openMenu', function () {
        if (jQuery('nav#menu').hasClass('open')) {
            jQuery('nav#menu').removeClass('open')
            jQuery('.openMenu').remove()
            jQuery('body').removeClass('nonScroll')
        }
    })
    var current_scroll = $(window).scrollTop();
    $(window).scroll(function () {
        if ($(window).scrollTop() <= h_height) {
            $('#header.sticky').removeClass('header-fixed');
            $('#header.sticky').removeClass('fixed-up');
            $('#header.sticky').css('height', '');

            $('#header .header-main').css('background', '');
            $('#header .buttonMenu button').css('background', '#000000');
            $('#header .buttonMenu button').css('color', '#ffffff');
            $('#header .logoDesktop svg path').css('fill', '#000000');


            if ($('.socialHeader').length && $('.socialHeader').hasClass('onScroll')) {
                $('#header .wrapperLogo').show();
                $('.socialHeader').removeClass('onScroll');
            }
        }
        else {

            /*=== Show Past In Lazy Loag ===*/
            $('#footer .colImages').each(function () {
                if(!$(this).hasClass('showImage')){
                    var positionY = $(this).offset();
                    var heightEl = $(this).height();
                    if($(window).scrollTop() + $(window).height() > positionY.top + heightEl / 2)
                        $(this).addClass('showImage')
                }
            })

            $('#header.sticky').css('height', h_height);
            $('#header.sticky').addClass('header-fixed');

            $('#header.header-fixed .header-main').css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
            $('#header.header-fixed .buttonMenu button').css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
            $('#header.header-fixed .buttonMenu button').css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
            $('#header.header-fixed .logoDesktop-sticky svg path').css('fill', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
            $('#header.header-fixed .logoDesktop svg path').css('fill', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));

            if ($('.socialHeader') && !$('.socialHeader').hasClass('onScroll')) {
                $('.socialHeader').addClass('onScroll');
            }

            if ($(window).scrollTop() > current_scroll) {
                $('#header.sticky').removeClass('fixed-up');
                $('#header.sticky').addClass('fixed-down');

                if ($('.socialHeader').length) {
                    $('#header .wrapperLogo').hide();
                    $('.socialHeader .prevPost').removeClass('currentEvent');
                    $('.socialHeader .currentPost').addClass('currentEvent');
                    $('.socialHeader .nextPost').removeClass('currentEvent');
                    $('.wrapper-social-share').show();
                    $('.wrapperMoreTag').hide();
                }

            }
            else {
                $('#header.sticky').removeClass('fixed-down');
                $('#header.sticky').addClass('fixed-up');

                if ($('.socialHeader').length) {
                    $('#header .wrapperLogo').hide();
                    $('.socialHeader .prevPost').addClass('currentEvent');
                    $('.socialHeader .currentPost').removeClass('currentEvent');
                    $('.socialHeader .nextPost').addClass('currentEvent');
                    $('.wrapper-social-share').hide();
                    $('.wrapperMoreTag').show();
                }
            }

        }
        current_scroll = $(window).scrollTop();

        if ($('.header-fixed').length) {
            $('.buttonMenu button').hover(function () {
                    $('.tagButtonMenu').css('opacity', '0')
                },
                function () {
                    $('.tagButtonMenu').css('opacity', '0')
                })
        } else {
            $('.buttonMenu button').hover(function () {
                    $('.tagButtonMenu').css('opacity', '1')
                },
                function () {
                    $('.tagButtonMenu').css('opacity', '0')
                })
        }
    })

    /*== Set color and background dynamic  ==*/
    $('#header .verticalTitle').css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
    $('#header .verticalTitle').css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));



    $('#menu-category-menu .menu-item a span').hover(
        function () {
            if ($(this).data('bg')) {
                $(this).children('.before').css('background-color', $(this).data('bg'));
                $(this).children('.after').css('background-color', 'transparent');
            }
            if ($(this).data('color'))
                $(this).css('color', $(this).data('color'));
        }, function () {
            if ($(this).data('bg')) {
                $(this).children('.after').css('background-color', $(this).data('bg'));
                $(this).children('.before').css('background-color', '');
            }
            $(this).css('color', '')
        }
    )

    jQuery('.dropdownButtons select').each(function () {
        var $this = jQuery(this), numberOfOptions = jQuery(this).children('option').length
        $this.addClass('select-hidden')
        $this.wrap('<div class="select"></div>')
        $this.after('<div class="select-styled"></div>')
        var $styledSelect = $this.next('div.select-styled')//$styledSelect.text($this.children('option').eq(0).text());
        var $list = jQuery('<ul />', {'class': 'select-options'}).insertAfter($styledSelect)
        for (var i = 0; i < numberOfOptions; i++) {
            jQuery('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list)
        }
        var $listItems = $list.children('li')
        $styledSelect.click(function (e) {
            e.stopPropagation()
            jQuery('div.select-styled.active').not(this).each(function () {
                jQuery(this).removeClass('active').next('ul.select-options').hide()
            })
            jQuery(this).toggleClass('active').next('ul.select-options').toggle()
        })
        $listItems.click(function (e) {
            e.stopPropagation()
            $styledSelect.text(jQuery(this).text()).removeClass('active')
            $this.val(jQuery(this).attr('rel'))
            $list.hide()
        })
        jQuery(document).click(function () {
            $styledSelect.removeClass('active')
            $list.hide()
        })
    })

    $(window).scroll(function () {
        var scroll = $(window).scrollTop()
        var f_position = $('footer#footer').length ? $('footer#footer').offset().top : 0
        var body_padding_bottom = parseInt($('#body').css('padding-bottom').slice(0, -2))
        f_position = f_position - body_padding_bottom
        $('.bannerLeft, .bannerRight, .imgAdLeft, .imgAdRight, .sidebarFix').each(function () {
            position = f_position - $(this).outerHeight()
            if (scroll >= h_height && scroll < position) {
                if (!$(this).hasClass('fixed')) {
                    $(this).addClass('fixed')
                    $(this).removeClass('fixed-bottom')
                    $(this).css('position', '')
                }
            }
            else {
                if (scroll < h_height) {
                    $(this).removeClass('fixed')
                    $(this).removeClass('fixed-bottom')
                    $(this).css('position', '')
                }
                else {
                    $(this).removeClass('fixed')
                    $(this).addClass('fixed-bottom')
                    $(this).css('position', 'absolute')
                }
            }
        })
    })

    $('.articleSlider').slick({
      dots: true,
      infinite: true,
      fade: true,
      speed: 500,
      slidesToShow: 1,
      adaptiveHeight: true,
      arrows: true,
      customPaging: function (slider, i) {
        return (i + 1) + '/' + slider.slideCount
      },
    })

    $('.articleSlider').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        if (nextSlide === 0){
            $('.articleHeader h1').css({'opacity': '1', 'z-index': 10})
        } else {
            $('.articleHeader h1').css({'opacity': '0', 'z-index': -1})
        }
    });

    $('.articleSliderShoot').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        $('.articleHeader').addClass('showSlider')
        setTimeout(function(){ $('.articleHeader').removeClass('showSlider') }, 2000);
    });

    $('.articleSliderShoot').slick({
        dots: true,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear',
        slidesToShow: 1,
        adaptiveHeight: true,
        arrows: true,
        asNavFor: '.sliderPost',
        swipeToSlide: true,
        customPaging: function (slider, i) {
            i++;
            return '<span class="looks">LOOKS</span><span class="counts">' + i + ' / ' + slider.slideCount + '</span>';
        },
    })

    $('.sliderPost').slick({
        slidesToShow: 0,
        slidesToScroll: 1,
        asNavFor: '.articleSliderShoot',
        dots: false,
        arrows: false,
        focusOnSelect: true,
    })

    $('.wrapperSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 6,
        accessibility: true,
        arrows: true,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 5
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    variableWidth: true,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    variableWidth: true,
                }
            }
        ]
    })

    $(document).on('click','label.idForLabel',function(e){
        e.preventDefault();
        $(this).siblings('input[type="file"]').trigger('click');
    });

    $(document).on('change', '.ginput_container input[type="file"]', function () {
        text = $(this).val().split("\\");
        var index = text.length - 1;
        text = text[index];
        if (text) {
            $(this).siblings('label.idForLabel').text(text);
        }
        else {
            $(this).siblings('label').text('upload')
        }
    });

    /* get height  form footer */
    var heightFormFooter = $('#footer .bgFooter').outerHeight();

    setInterval(function () {
        $('.filedUpload input[type="file"]').each(function () {
            if ($(this).length && !$(this).siblings('label').length) {
                $(this).hide();
                var idForLabel = $(this).attr('id')
                if($(this).parent().find('.ginput_preview').length){
                    $(this).parent().append('<label class=\'idForLabel\' for=\'' + idForLabel + '\'>incorrect file type</label>');
                    $(this).parent().find('.idForLabel').css({'background-color': '#ff4222', 'color': '#f5b865'})
                } else {
                    $(this).parent().append('<label class=\'idForLabel\' for=\'' + idForLabel + '\'>upload</label>')
                }
            };
        });

        if($('#footer .footerForm .gform_confirmation_wrapper').length){
            if(!$('.wrapFormFooter').hasClass('formSuccess')){
                $('#footer .bgFooter').css('height', heightFormFooter + 'px')
                $('#footer .wrapFormFooter').addClass('formSuccess')
            }
        }

        $('.formApply').each(function () {
            var gform_validation_error = $(this).find('.gform_validation_error');
            if(gform_validation_error.length){
                $(this).find('.gform_button').val("Oops, we're missing something");
            }
        })

    }, 20);

    $(document).bind('gform_confirmation_loaded', function(event, formId){
        if ( $('div.validation_error').length) {
            $('.validation_error').each(function () {
                $(this).parents('.listingApply').removeClass('formError')
                $(this).parents('.listingApply').addClass('formError')
            })
        } else if($('.gform_confirmation_message').length) {
            $('.gform_confirmation_message').each(function () {
                $(this).parents('.listingApply').removeClass('formError')
                $(this).parents('.listingApply').addClass('formSuccess')
            })
        }
    });

    $("#gform_3").bind('ajax:error', function () {
        if ($('.filedUpload').length) {
            $('.filedUpload input[type="file"]').css('display', 'none')
            var idForLabel = $('.filedUpload input[type="file"]').attr('id')
            $('.filedUpload .ginput_container').append('<label class=\'idForLabel\' for=\'' + idForLabel + '\'>upload</label>')
        }
        ;
    });

    if (!$('.header-fixed').length) {
        $('.buttonMenu button').hover(function () {
                $('.tagButtonMenu').css('opacity', '1')
            },
            function () {
                $('.tagButtonMenu').css('opacity', '0')
            })
    }

    jQuery('.btnAddToCalendar select').each(function () {
        var $this = jQuery(this), numberOfOptions = jQuery(this).children('option').length
        $this.addClass('select-hidden')
        $this.wrap('<div class="select"></div>')
        $this.after('<div class="select-styled">ADD TO CALENDAR</div>')
        var $styledSelect = $this.next('div.select-styled')//$styledSelect.text($this.children('option').eq(0).text());
        var $list = jQuery('<ul />', {'class': 'select-options'}).insertAfter($styledSelect)
        for (var i = 0; i < numberOfOptions; i++) {
            jQuery('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list)
        }
        var $listItems = $list.children('li')
        $styledSelect.click(function (e) {
            e.stopPropagation()
            jQuery('div.select-styled.active').not(this).each(function () {
                jQuery(this).removeClass('active').next('ul.select-options').hide()
            })
            jQuery(this).toggleClass('active').next('ul.select-options').slideToggle(),
                jQuery(this).parent().parent().css({"border": "0"})
        })
        $listItems.click(function (e) {
            e.stopPropagation()
            $styledSelect.text(jQuery(this).text()).removeClass('active')
            $this.val(jQuery(this).attr('rel'))
            $list.hide()
        })
        jQuery(document).click(function () {
            $styledSelect.removeClass('active')
            $list.hide()
        })
    })

    $('.linkApply').click(function () {
        if (!$(this).parent().hasClass('changeApply')) {
            $(this).children().next().addClass('changeStroke');
            $(this).next().show();
            $(this).parent().addClass('changeApply');
        }
        else {
            $(this).children().next().removeClass('changeStroke');
            $(this).next().hide();
            $(this).parent().removeClass('changeApply');
        }
    })

    $('.wrapperBodyRetailTherapy').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 5,
        accessibility: false,
        arrows: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    variableWidth: true
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    variableWidth: true
                }
            }
        ]
    })

    jQuery('.chooseOption select').each(function () {
        var $this = jQuery(this), numberOfOptions = jQuery(this).children('option').length
        $this.addClass('select-hidden')
        $this.wrap('<div class="select"></div>');
        $this.after('<div class="select-styled">' + $this.children('option').eq(0).text() + '<span class="caret-down"></span></div>')
        var $styledSelect = $this.next('div.select-styled');
        var val = $(this).val();
        var $list = jQuery('<ul />', {'class': 'select-options'}).insertAfter($styledSelect)
        for (var i = 0; i < numberOfOptions; i++) {
            var active='';
            if(val) {
                if (val == $this.children('option').eq(i).val()) {
                    active = 'active';
                }
            }
            else {
                if(!i) {
                    active = 'active';
                }
            }
            if(active) {
                $styledSelect.html($this.children('option').eq(i).text() + '<span class="caret-down"></span>').removeClass('active')
            }
            jQuery('<li />', {
                html: '<span>'+$this.children('option').eq(i).text()+'</span>',
                rel: $this.children('option').eq(i).val(),
                class: active,
            }).appendTo($list)
        }
        var $listItems = $list.children('li')
        $styledSelect.click(function (e) {
            e.stopPropagation()
            jQuery('div.select-styled.active').not(this).each(function () {
                jQuery(this).removeClass('active').next('ul.select-options').hide()
            })
            jQuery(this).toggleClass('active').next('ul.select-options').toggle()

            if (jQuery(this).hasClass('active')) {
                jQuery(this).css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
                jQuery(this).css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
                jQuery(this).css('border-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
                jQuery(this).find('.caret-down').css('border-top-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
            } else {
                jQuery(this).css('background', 'transparent');
                jQuery(this).css('color', '#000');
                jQuery(this).css('border-color', '#efefef');
                jQuery(this).find('.caret-down').css('border-top-color', '#000');
            }
        })
        $listItems.click(function (e) {
            e.stopPropagation()

            $styledSelect.html(jQuery(this).text() + '<span class="caret-down"></span>').removeClass('active')

            if($this.val()!=$(this).attr('rel')){
                $list.children('li').removeClass('active');
                $(this).addClass('active');
                $this.val(jQuery(this).attr('rel'));
                $this.trigger('change');
            }
            $list.hide()
            if (jQuery(this).hasClass('active')) {
                $styledSelect.css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
                $styledSelect.css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
                $styledSelect.css('border-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
                $styledSelect.find('.caret-down').css('border-top-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
            } else {
                $styledSelect.css('background', 'transparent');
                $styledSelect.css('color', '#000');
                $styledSelect.css('border-color', '#efefef');
                $styledSelect.find('.caret-down').css('border-top-color', '#000');
            }
        })
        jQuery(document).click(function () {
            $styledSelect.removeClass('active')
            $list.hide()
            if (jQuery(this).hasClass('active')) {
                $styledSelect.css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
                $styledSelect.css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
                $styledSelect.css('border-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
                $styledSelect.find('.caret-down').css('border-top-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
            } else {
                $styledSelect.css('background', 'transparent');
                $styledSelect.css('color', '#000');
                $styledSelect.css('border-color', '#efefef');
                $styledSelect.find('.caret-down').css('border-top-color', '#000');
            }
        })

        $('.postAJob .postAJobButton').css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
        $('.postAJob .postAJobButton').css('border-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
        $('.postAJob .postAJobButton .bgColor').css('background-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));

        $('.postAJob .postAJobButton').hover(function () {
            $('.postAJob .postAJobButton').css('background-color', 'transparent');
        }, function () {
            $('.postAJob .postAJobButton').css('background-color', 'transparent');
        });

        $('.selectAria .select .select-options').css('background', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('bg'));
        $('.selectAria .select .select-options li').css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-menu-item a span').data('color'));
    })

    jQuery('.itemFilter select').each(function () {
        var $this = jQuery(this), numberOfOptions = jQuery(this).children('option').length
        var val = $(this).val();
        $this.addClass('select-hidden')
        $this.wrap('<div class="select filterOptions"></div>')
        $this.after('<div class="select-styled">' + $this.children('option').eq(0).text() + '</div>')
        var $styledSelect = $this.next('div.select-styled')
        var $list = jQuery('<ul />', {'class': 'select-options'}).insertAfter($styledSelect)
        for (var i = 0; i < numberOfOptions; i++) {
            jQuery('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val(),
                class: (!i)?'active':'',
            }).appendTo($list)
        }
        var $listItems = $list.children('li')
        $styledSelect.click(function (e) {
            e.stopPropagation()
            jQuery('div.select-styled.active').not(this).each(function () {
                jQuery(this).removeClass('active').next('ul.select-options').hide()
            })
            jQuery(this).toggleClass('active').next('ul.select-options').toggle()
        })
        $listItems.click(function (e) {
            e.stopPropagation()
            $styledSelect.text(jQuery(this).text()).removeClass('active')
            if( (val != $(this).attr('rel') || $(this).attr('rel') == '') && !$(this).hasClass('active') ) {
                $list.children('li').removeClass('active');
                $(this).addClass('active');
                $this.val($(this).attr('rel'));
                $this.trigger('change');
            }
            $list.hide()
        })
        jQuery(document).click(function () {
            $styledSelect.removeClass('active')
            $list.hide()
        })
    })

    $('.headerFilter .closeButton').click(function () {
        $('.wrapperFilterItem').hide();
    })
    $('.filterButton').click(function () {
        $('.wrapperFilterItem').show();
    })

    addStyle();

    if($('#header nav .menu-category-menu-container #menu-category-menu li.current-post-ancestor a span').data('color')) {
        $('.setPosition .upNext .contentUpNext label').css('color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-post-ancestor a span').data('color'));
        $('.setPosition .upNext .contentUpNext p').css('color', '#000');
        $('.setPosition .upNext').css('background-color', $('#header nav .menu-category-menu-container #menu-category-menu li.current-post-ancestor a span').data('bg'));
    }

    $('#header nav .menu-category-menu-container #menu-category-menu li a > span').each(function () {
        if ($.trim($(this).text().toLowerCase()) == $('#jobListing-page .postType.titleText').text().toLowerCase()) {
            $('#jobListing-page .postType.titleText').css({
                'background-color': $(this).data('bg'),
                'color': $(this).data('color')
            })
            return;
        }
    });



    $(window).scroll(function () {
        var wintop = $(window).scrollTop(), docheight = $('body').height(), winheight = $(window).height();
        var totalScroll = (wintop / (docheight - winheight)) * 100;
        $(".myProgressBar").css("width", totalScroll + "%");
    });
    var lazy = $('.lazyLoad').not('.lazyloadsearch').detach();
    lazy.appendTo("footer#footer");

    $('#searchButton').add('#searchPopup .closeButton').add('.buttonSearch button').on('click', function () {
        $('#searchPopup').toggleClass('open');
    })

    // This is a functions that scrolls to #{blah}link
    function goToByScroll(id) {
        // Remove "link" from the ID
        id = id.replace("link", "");
        // Scroll
        $('html,body').animate({
                scrollTop: $("#" + id).offset().top - 100
            },
            1000);
    }

    $(".getLook").click(function (e) {
        // Prevent a page reload when a link is pressed
        e.preventDefault();
        // Call the scroll function
        var index = $(this).parent().find('.slick-active').index();
        index += 1;
        goToByScroll('sliderPostItem' + index);
    });
    jQuery(window).resize(function () {
        if (jQuery(window).width() < 768) {
            $(".textTerm").prependTo('.mainControl');
        }
    });
    if (jQuery(window).width() < 768) {
        $(".textTerm").prependTo('.mainControl');
    }

    /*== scroll for sidebar ==*/
    var sponsoreD;

    if($('.setPosition .upNext').length){
      sponsoreD = $('.setPosition .upNext');
    } else if($('.setPosition .sponsoreD').length){
      sponsoreD = $('.setPosition .sponsoreD');
    }

    if($('.contentEvent').length)
        scrollSidebar($('.contentEvent'), $('.sidebarLef'), $('.articleRight'), $('.container'));
    if($('#articleA-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
    if($('#articleB-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
    if($('#articleShoot-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
    if($('#articleC-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
    if($('.jobContent').length)
        scrollSidebar($('.jobContent'), $('.sidebarLef'), $('.articleRight'), $('.container'));

    $(window).resize(function () {
      if($('.contentEvent').length)
        scrollSidebar($('.contentEvent'), $('.sidebarLef'), $('.articleRight'), $('.container'));
      if($('#articleA-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
      if($('#articleB-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
      if($('#articleShoot-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
      if($('#articleC-Page .contentArticle .sideBarImage').length)
        scrollaAd($('.contentArticle'), $('.sideBarImage'), $('.articleRight'), $('.container'), sponsoreD);
      if($('.jobContent').length)
        scrollSidebar($('.jobContent'), $('.sidebarLef'), $('.articleRight'), $('.container'));
    })

    function scrollaAd (parentElement, scrollElement, anotherElement, marginRight, upNext) {
        var scrollElWidth = parseInt($(parentElement).outerWidth() - $(anotherElement).outerWidth()),
            srollElHeight = parseInt($(scrollElement).outerHeight()),
            srollElPosition = parseInt((scrollElement).offset().top);

        var parentElHeight = parseInt($(parentElement).outerHeight()),
            parentElPosition = parseInt($(parentElement).offset().top);

        var srollElRightPosition = parseInt($(marginRight).css("margin-right").replace('px', '')),
            upNextPosition = upNext.length ? parseInt($(upNext).offset().top) : 0,
            upNextHeight = upNext.length ? parseInt($(upNext).outerHeight()) : 0,
            upNextMargin = upNext.length ? parseInt($(upNext).css("margin-bottom").replace('px', '')) : 0;

        upNextHeight = upNextHeight + upNextMargin;
        var scroll = $(window).scrollTop() + 60;

        if($(anotherElement).outerHeight() < upNextHeight + srollElHeight){
            $(scrollElement).css({'position': 'relative', 'width': 100 + '%', 'right': 0, 'top': 0 })
        } else {
            if (scroll >= srollElPosition + 42 + 30 - upNextHeight && scroll + srollElHeight < parentElHeight + parentElPosition) {
                $(scrollElement).css({'position': 'fixed', 'width': scrollElWidth + 'px', 'right': srollElRightPosition + 15 + 'px', 'top': '60px' })
            }
            if (scroll < srollElPosition + 42 + 30 - upNextHeight && scroll < srollElPosition + srollElHeight) {
                $(scrollElement).css({'position': 'absolute', 'width': scrollElWidth + 'px', 'right': 15 + 'px', 'top': upNextHeight + 'px', 'bottom': 'auto' })
            }
            if (scroll + srollElHeight > parentElHeight + parentElPosition && parentElHeight > upNextHeight + srollElHeight) {
                $(scrollElement).css({'position': 'absolute', 'width': scrollElWidth + 'px', 'right': 15 + 'px', 'bottom': 0, 'top': 'auto'})
            }
        }

        $(window).scroll(function () {
            scroll = $(window).scrollTop() + 60;

            if($(anotherElement).outerHeight() < upNextHeight + srollElHeight){
                $(scrollElement).css({'position': 'relative', 'width': 100 + '%', 'right': 0, 'top': 0 })
            } else {
                if (scroll >= srollElPosition + 42 + 30 - upNextHeight && scroll + srollElHeight < parentElHeight + parentElPosition) {
                    $(scrollElement).css({'position': 'fixed', 'width': scrollElWidth + 'px', 'right': srollElRightPosition + 15 + 'px', 'top': '60px' })
                }
                if (scroll < srollElPosition + 42 + 30 - upNextHeight && scroll < srollElPosition + srollElHeight) {
                    $(scrollElement).css({'position': 'absolute', 'width': scrollElWidth + 'px', 'right': 15 + 'px', 'top': upNextHeight + 'px', 'bottom': 'auto' })
                }
                if (scroll + srollElHeight > parentElHeight + parentElPosition && parentElHeight > upNextHeight + srollElHeight) {
                    $(scrollElement).css({'position': 'absolute', 'width': scrollElWidth + 'px', 'right': 15 + 'px', 'bottom': 0, 'top': 'auto'})
                }
            }
        })
    }

    function scrollSidebar (parentElement, scrollElement, anotherElement, marginRight) {

        var scrollElWidth = parseInt($(parentElement).outerWidth() - $(anotherElement).outerWidth()),
            srollElHeight = parseInt($(scrollElement).outerHeight()),
            srollElPosition = parseInt($(scrollElement).offset().top);

        var parentElHeight = parseInt($(parentElement).outerHeight()),
            parentElPosition = parseInt($(parentElement).offset().top);


        var srollElRightPosition = parseInt($(marginRight).css("margin-right").replace('px', ''));

        $(window).scroll(function () {
            var scroll = $(window).scrollTop() + 60;

            if (scroll >= srollElPosition && scroll + srollElHeight < parentElHeight + parentElPosition) {
                $(scrollElement).css({'position': 'fixed', 'width': scrollElWidth + 'px', 'right': srollElRightPosition + 'px', 'top': '60px' })
            }
            if (scroll < srollElPosition && scroll < srollElPosition + srollElHeight) {
                $(scrollElement).css({'position': 'absolute', 'width': scrollElWidth + 'px', 'right': 0 + 'px', 'top': 0, 'bottom': 'auto' })
            }
            if (scroll + srollElHeight > parentElHeight + parentElPosition) {
                $(scrollElement).css({'position': 'absolute', 'width': scrollElWidth + 'px', 'right': 0 + 'px', 'bottom': 0, 'top': 'auto'})
            }
        })

    }
    /*== END scroll for sidebar ==*/

    /*$('.TitleLast').appendTo('.btnSubmit');*/

    $(window).resize(function () {
        if ($(window).width() < 767) {
            $('.sidebarImageUpload').prependTo('.formPostAJob .contentForm');
            $('#post-sale .sidebarImageUpload').prependTo('#post-sale');
            $('#post-event .sidebarImageUpload').prependTo('#post-event');
            $('.saleRight').prependTo('.formSale');
            $('.TitleLast').appendTo('.btnSubmit');
            $('.colNext .TitleLast').appendTo('.bgButton');
            $('.relatedSection table').replaceWith(function() {

                var $th = $(this).find('th'); // get headers
                var th = $th.map(function() {
                    return $(this).text();
                }).get(); // and their values

                $th.closest('tr').remove(); // then remove them

                var $d = $('<div>', { 'class': 'box' });

                $('tr', this).each(function(i, el) {
                    var $div = $('<div>', {'class': 'inner'}).appendTo($d);
                    $('td', this).each(function(j, el) {
                        var n = j + 1;
                        var $row = $('<div>', {
                            'class': 'row-' + n
                        });
                        if(j==5){
                            var imgsrc=$('.btnEdit img').attr('src');
                            var linkEdit = $(this).children().attr('href');
                            $row.append(
                                $('<span>', {
                                    'class' :'data-' + n,
                                    html:'<a href="' + linkEdit + '" class="btnEdit"><img src="'+imgsrc+'"></a>'
                                })).appendTo($div);
                        }
                        else if(j==6){
                            var imgsrc=$('.btnDelete img').attr('src');
                            $row.append(
                                $('<span>', {
                                    'class' :'data-' + n,
                                    html:'<div class="btnDelete"><img src="'+imgsrc+'"></div>'
                                })).appendTo($div);
                        }
                        else{
                            $row.append(
                                $('<span>', {
                                    'class' :'label-' + n,
                                    text: th[j]
                                }), $('<span>', {
                                    'class' :'data-' + n,
                                    text: $(this).text()
                                })).appendTo($div);
                        }
                    });
                });

                return $d;
            });
        }
        else{
            $('.sidebarImageUpload').appendTo('.formPostAJob .contentForm');
            $('#post-sale .sidebarImageUpload').appendTo('#post-sale');
            $('#post-event .sidebarImageUpload').appendTo('#post-event');
            $('.colNext .TitleLast').appendTo('.bgButton .colNext');
        }
    });

    if ($(window).width() < 767) {
        $('.sidebarImageUpload').prependTo('.formPostAJob .contentForm');
        $('#post-sale .sidebarImageUpload').prependTo('#post-sale');
        $('#post-event .sidebarImageUpload').prependTo('#post-event');
        $('.saleRight').prependTo('.formSale');
        $('.TitleLast').appendTo('.btnSubmit');
        $('.colNext .TitleLast').appendTo('.bgButton');
        $('.relatedSection table').replaceWith(function() {

            var $th = $(this).find('th'); // get headers
            var th = $th.map(function() {
                return $(this).text();
            }).get(); // and their values

            $th.closest('tr').remove(); // then remove them

            var $d = $('<div>', { 'class': 'box' });

            $('tr', this).each(function(i, el) {
                var $div = $('<div>', {'class': 'inner'}).appendTo($d);
                $('td', this).each(function(j, el) {
                    var n = j + 1;
                    var $row = $('<div>', {
                        'class': 'row-' + n
                    });
                    if(j==5){
                        var imgsrc = $('.btnEdit img').attr('src');
                        var linkEdit = $(this).children().attr('href');
                        $row.append(
                            $('<span>', {
                                'class' :'data-' + n,
                                html:'<a href="' + linkEdit + '" class="btnEdit"><img src="'+imgsrc+'"></a>'
                            })).appendTo($div);
                    }
                    else if(j==6){
                        var imgsrc=$('.btnDelete img').attr('src');
                        $row.append(
                            $('<span>', {
                                'class' :'data-' + n,
                                html:'<div class="btnDelete"><img src="'+imgsrc+'"></div>'
                            })).appendTo($div);
                    }
                    else{
                        $row.append(
                            $('<span>', {
                                'class' :'label-' + n,
                                text: th[j]
                            }), $('<span>', {
                                'class' :'data-' + n,
                                text: $(this).text()
                            })).appendTo($div);
                    }
                });
            });

            return $d;
        });
    }
    else{
        $('.sidebarImageUpload').appendTo('.formPostAJob .contentForm');
        $('#post-sale .sidebarImageUpload').appendTo('#post-sale');
        $('#post-event .sidebarImageUpload').appendTo('#post-event');
        $('.colNext .TitleLast').appendTo('.bgButton .colNext');
    }

    $('form input').click(function () {
        $(this).attr('autocorrect', 'off');
        $(this).attr('autocapitalize', 'off');
        $(this).attr('autocomplete', 'off')
    });

    if(typeof $('form input').live === "function"){
        $('form input').live('click', function () {
            $(this).attr('autocorrect', 'off');
            $(this).attr('autocapitalize', 'off');
            $(this).attr('autocomplete', 'off')
        });
    }

    $('.sliderPostItem').bind('cssClassChanged',function(){
        addStyle();
    });

    $(document).on('click','.atcb-link',function (e) {
        var object = $(this).siblings('.atcb-list').find('.atcb-item:first-child a');
        window.open(object.attr('href'),object.attr('target'));
    })

    jQuery(".page-template-template-post-a-job .select").change(function(){
        var val=jQuery(this).val();
        if(val!=""){
            jQuery(this).parents(".userFormSelectWrapper").find(".userFormSelect").addClass('show-me');
        }
        else{
            jQuery(this).parents(".userFormSelectWrapper").find(".userFormSelect").removeClass('show-me');
        }
    })

    $('.atcb-link').hover(function () {
        $(this).css('border-color', $('.articleMeta a').css('color'));
    }, function () {
        $(this).css('border-color', $('.articleMeta a').css('background-color'));
    })

    var mainEl = $('#header nav .menu-category-menu-container #menu-category-menu li.current-post-ancestor a span');
    var articleMeta = $('.articleMeta a');
    var subMeta = $('.subMeta a');



    $(window).bind('load', function () {
        /*==== set color for add to calendar ====*/
        $('.addtocalendar .atcb-link').add('.addtocalendar .atcb-list').add('.addtocalendar .atcb-item').css('background-color', $('.articleMeta a').css('background-color'));
        $('.addtocalendar .atcb-link').css('border-color', $('.articleMeta a').css('background-color'));
        $('.addtocalendar .leftIcon').add('.addtocalendar .rightIcon').css('background-color', $('.articleMeta a').css('color'));
        $('.addtocalendar .atcb-item-link').add('.addtocalendar .atcb-link').css('color', $('.articleMeta a').css('color'));

        /*==== set color for add to reading/up next in header ====*/
        if($('.articleMeta').length){
            if(articleMeta.text() === 'Life' || articleMeta.text() === 'Beauty' || articleMeta.text() === 'Sales'){
                $('#header .wrapperContentPostHeader .upNext').css('color', $('.articleMeta a').css('background-color'));
                $('#header .wrapperContentPostHeader .status').css('color', $('.articleMeta a').css('background-color'));
            } else {
                $('#header .wrapperContentPostHeader .upNext').css('color', $('.articleMeta a').css('color'));
                $('#header .wrapperContentPostHeader .status').css('color', $('.articleMeta a').css('color'));
            }
        } else if($('.subMeta').length){
            $('#header .wrapperContentPostHeader .upNext').css('color', $('.subMeta a').css('color'));
            $('#header .wrapperContentPostHeader .status').css('color', $('.subMeta a').css('color'));
        }

        /*==== set color for "the detail" in event and sale page ====*/
        if($('.articleMeta').length) {
            $('.articleA .contentEvent .textBlockDetail h4').css('color', $('.articleMeta a').css('color'));
        }
    })

    /*==== Set color for links in single page ====*/
    if(mainEl.length){
        if(mainEl.text() === 'Life' || mainEl.text() === 'Beauty'){
            $('.single-post .wrapperContain a').css('color', mainEl.data('bg'));
        } else {
            $('.single-post .wrapperContain a').css('color', mainEl.data('color'));
        }
    } else if(articleMeta.length){
        if(articleMeta.text() === 'Sales'){
            $('.wrapperContain a').css('color', articleMeta.css('background-color'));
            $('.articleRight .textBlock a').css('color', articleMeta.css('background-color'));
        } else {
            $('.wrapperContain a').css('color', articleMeta.css('color'));
            $('.articleRight .textBlock a').css('color', articleMeta.css('color'));
        }
    } else if(subMeta.length) {
        if(subMeta.text() === 'Sales'){
            $('.wrapperContain a').css('color', subMeta.css('background-color'));
            $('.articleRight .textBlock a').css('color', subMeta.css('background-color'));
        } else {
            $('.wrapperContain a').css('color', subMeta.css('color'));
            $('.articleRight .textBlock a').css('color', subMeta.css('color'));
        }
    }

  /*===== Get length of shoot slide items =====*/
  var slideItems = $('.articleSliderShoot .articleShootSlide ').length;
  if(slideItems){
    $('.look-slide').text(slideItems + ' LOOKS')
  }

  /*=====  =====*/
  var articleHeader;
  var articleShootSlideCover = $('.articleShootSlideCover');

  if($('.articleSlider').length){
    articleHeader = $('.articleSlider');
  } else {
    articleHeader = $('.articleSliderShoot');
  }

  var articleHeaderWidth, articleHeaderHeigh, articleHeaderY, articleHeaderX;

  $(window).resize(function () {
    if(articleHeader.length) {
      articleHeaderWidth = articleHeader.width();
      articleHeaderHeigh = articleHeader.height();
      articleHeaderY = articleHeader.offset().top;
      articleHeaderX = articleHeader.offset().left;
    }
  })



  if(articleHeader.length){
    articleHeaderWidth = articleHeader.width();
    articleHeaderHeigh = articleHeader.height();
    articleHeaderY = articleHeader.offset().top;
    articleHeaderX = articleHeader.offset().left;
    articleHeader.click(function (e) {
      if(articleHeaderX < e.pageX < articleHeaderX + articleHeaderWidth && articleHeaderY < e.pageY < articleHeaderY + articleHeaderHeigh){
        if(e.pageX < (articleHeaderX + articleHeaderWidth) / 2){
          if(articleShootSlideCover.length){
            if(articleHeader.find('.slick-active').index() === 0){
              articleShootSlideCover.removeClass('hideShow');
              articleShootSlideCover.next().removeClass('showSlider');
            } else {
              articleHeader.slick('slickPrev');
            }
          } else {
            articleHeader.slick('slickPrev');
          }
        } else {
          if(articleShootSlideCover.length){
            if(articleHeader.find('.slick-active').index() === articleHeader.find('.articleShootSlide').length - 1){
              articleShootSlideCover.removeClass('hideShow');
              articleShootSlideCover.next().removeClass('showSlider');
            } else {
              articleHeader.slick('slickNext');
            }
          } else {
            articleHeader.slick('slickNext');
          }
        }
      }
    })
  }

  /*===== Hide cover image first =====*/
  if(articleShootSlideCover.length){
    articleShootSlideCover.click(function () {
      articleShootSlideCover.toggleClass('hideShow');
      articleShootSlideCover.next().toggleClass('showSlider');
    })
    $('.sliderPostItem').click(function () {
      articleShootSlideCover.addClass('hideShow');
      articleShootSlideCover.next().addClass('showSlider');
    })
  }

  /*===== use Left and right arrows to change image in slide =====*/
  $('.articleSlider .slick-arrow').click(function () {
    $(document).find('.slick-list').attr('tabindex', 0).focus();
  })
})