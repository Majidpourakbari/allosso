$(document).ready(function () {
    //Ripple
    let $btnRipple = $('.btn--ripple'),
        $btnRippleInk, btnRippleH, btnRippleX, btnRippleY;
    $btnRipple.on('mouseenter', function (e) {
        let $t = $(this);
        if ($t.find(".btn--ripple-ink").length === 0) {
            $t.prepend("<span class='btn--ripple-ink'></span>");
        }

        $btnRippleInk = $t.find(".btn--ripple-ink");
        $btnRippleInk.removeClass("btn--ripple-animate");
        if (!$btnRippleInk.height() && !$btnRippleInk.width()) {
            btnRippleH = Math.max($t.outerWidth(), $t.outerHeight());
            $btnRippleInk.css({height: btnRippleH, width: btnRippleH});
        }

        btnRippleX = e.pageX - $t.offset().left - $btnRippleInk.width() / 2;
        btnRippleY = e.pageY - $t.offset().top - $btnRippleInk.height() / 2;
        $btnRippleInk.css({top: btnRippleY + 'px', left: btnRippleX + 'px'}).addClass("btn--ripple-animate");
    });
    $('.choose-input .choose').on('click',function () {
        let $type = $(this).attr('data-choose')
        let $tar = $('.company-delete')
        if ($type === 'company'){
            $tar.css('display','none')
            $('.download-card').addClass('black-bg')
        }else {
            $tar.css({
                'display':'flex',
                'justify-content' : 'center'
            })
            $('.download-card').removeClass('black-bg')
        }
    })
    $("#login_register_form").validate({

    });
    $('.pay-method .input-radio input').on('click',function () {
        $('.pay-method .head').css('background-color','#fff')
        $('.pay-method .body').slideUp(300)
        $(this).closest('.pay-method').find('.body').slideDown(300)
        $(this).closest('.head').css('background-color','#E9EFEC')
    })
})