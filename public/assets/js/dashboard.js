function scrollToBottom() {
    $(".box-b").scrollTop($(".box-b")[0].scrollHeight);
}
$(document).ready(function () {
    scrollToBottom();
    $('.menu-button').on('click',function () {
        $('.dashboard-header nav').css('left','0')
        $('.dashboard-header .cover-menu-nav').css({
            'opacity':'1',
            'visibility':'visible',
        })
    })
    $('.dashboard-header .cover-menu-nav').on('click',function () {
        $('.dashboard-header nav').css('left','-400px')
        $('.dashboard-header .cover-menu-nav').css({
            'opacity':'0',
            'visibility':'hidden',
        })
    })
    const emojis = ["😀", "😁", "😂", "🤣", "😍", "🥰", "😎", "🤩", "🙃", "😇"];
    const $emojiPicker = $("#emoji-picker");

    // ساخت داینامیک لیست ایموجی‌ها
    $.each(emojis, function (_, emoji) {
        $("<span>")
            .addClass("emoji")
            .text(emoji)
            .appendTo($emojiPicker)
            .click(function () {
                $("#chat-input").val($("#chat-input").val() + emoji);
                $emojiPicker.addClass("hidden");
            });
    });

    // باز و بسته کردن ایموجی پیکر
    $("#emoji-btn").click(function (event) {
        event.stopPropagation();
        $emojiPicker.toggleClass("hidden");
    });

    // بستن ایموجی پیکر وقتی بیرون کلیک شد
    $(document).click(function (event) {
        if (!$(event.target).closest("#emoji-picker, #emoji-btn").length) {
            $emojiPicker.addClass("hidden");
        }
    });
    $('.card-them-place .Contacts-card').on('click',function () {
        $('.side-Contacts-sec').css('display','none')
        $('.main-messages').css('display','block')
    })
    $('.icon-notification.notification').on('click',function () {
        $('.notification-hover,.cover-notification').css({
            'visibility':'visible',
            'opacity':'1',
        })
    })
    $('.cover-notification').on('click',function () {
        $('.notification-hover').css({
            'visibility':'hidden',
            'opacity':'0',
        })
        $(this).css({
            'visibility':'hidden',
            'opacity':'0',
        })
    })
    $('.items.back-to-chat').on('click',function () {
        $('.side-Contacts-sec').css('display','block')
        $('.main-messages').css('display','none')
    })
})