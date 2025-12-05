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
    $(window).scroll(function() {
        if ($(this).scrollTop() > 0) { // Adjust scroll distance as needed
            $('header').css({
                'background-color' : '#fff',
                'box-shadow' : '0 0 1rem rgba(0,0,0,.2)',
            });
        } else {
            $('header').css({
                'background-color' : 'transparent',
                'box-shadow' : 'none',
            });
        }
    });
    $('.menu-button').on('click',function () {
        $('header nav').css('left','0')
        $('.cover-menu-nav').css({
            'opacity':1,
            'visibility': 'visible'
        })
    })
    $('.cover-menu-nav').on('click',function () {
        $('header nav').css('left','-400px')
        $(this).css({
            'opacity':0,
            'visibility': 'hidden'
        })
    })
    $('.select-place').on('click',function () {
        $('.category-overlay').css({
            'opacity' : 1,
            'visibility' : 'visible'
        })
    })
    $('.category-overlay .option').on('click',function () {
        let $val = $(this).find('.name').text()
        $('.category-overlay').css({
            'opacity' : 0,
            'visibility' : 'hidden'
        })
        $('.select-place input').val($val)
        $('.select-place .select-label').text($val)
    })
    $('.category-overlay .cover').on('click',function () {
        $('.category-overlay').css({
            'opacity' : 0,
            'visibility' : 'hidden'
        })
    })
})