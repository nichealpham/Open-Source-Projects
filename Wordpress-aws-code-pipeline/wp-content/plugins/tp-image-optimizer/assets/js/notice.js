jQuery(function ($) {

    $.fn.serializeObject = function () {
        var values = $(this).serializeArray();
        var data = {};
        $.map(values, function (n, i) {
            data[n.name] = n.value;
        });
        return data;
    }
    /**
     * Add coupon
     * 
     * @since 1.0.7
     */
    $('.coupon-form button').on('click', function (e) {
        var $this = $(this);
        var $form = $this.closest('form');
        var $result = $form.find('.result_alert');

        if ($this.hasClass('disabled')) {
            return false;
        }

        var data = $form.serializeObject();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function () {
                $this.attr('data-current_text', $this.text());
                $this.addClass('disabled').text(tp_image_optimizer_lang.wait);
                $this.prev('input').attr('readonly', '');
                $result.removeClass('result_alert--error result_alert--warning').empty().hide();
            },
            success: function (res) {
                if (res.success) {
                    $result.html(res.data).show();
                    $form.find('input,button').remove();

                    $('.account_info .account_info__text').text(tp_image_optimizer_lang.pro);
                    $('.account_info__icon').attr('class', 'account_info__icon account_info__icon--pro');

                } else {
                    $result.text(res.data).addClass('result_alert--error').fadeIn();
                    setTimeout(function(){
                        $('.result_alert--error').fadeOut();
                    }, 1500);
                }

                $this.prev('input').removeAttr('readonly');
                $this.text($this.attr('data-current_text')).removeClass('disabled');
            }
        });

        e.preventDefault();
    });

});