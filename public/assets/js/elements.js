$('.select-options .option').on('click',function () {
    let $val = $(this).text()
    $(this).closest('.select-options').find('input').val($val)
    $(this).closest('.select-options').find('.option-place').slideUp(300)
    $(this).closest('.select-options').find('.label .name').text($val)
    $(this).closest('.select-options').find('.label img').css('transform','rotate(0deg)')

})
$('.select-options .label').on('click',function () {
    $(this).parent().find('.option-place').slideDown(300)
    $(this).find('img').css('transform','rotate(180deg)')
})
$(document).on('click', function (event) {
    if (!$(event.target).closest('.select-options').length) {
        $('.select-options').find('.option-place').slideUp(300)
        $('.select-options').find('.label img').css('transform','rotate(0deg)')

    }
});
// $('input[type=number]').on('change',function () {
//     let $val = $(this).val()
// })
$(".number-fill").on("input", function() {
    let input = $(this);
    let cursorPosition = this.selectionStart;
    let rawValue = input.val().replace(/,/g, ''); // حذف کاماهای قبلی

    if (!isNaN(rawValue) && rawValue !== "") {
        let formattedValue = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // سه رقم سه رقم جدا کن
        input.val(formattedValue);

        // تنظیم مکان‌نما در جای درست
        let newCursorPosition = cursorPosition + (formattedValue.length - rawValue.length);
        this.setSelectionRange(newCursorPosition, newCursorPosition);
    }
});
$('.filter-icon').on('click',function () {
    let $status = $(this).find('.title').text()
    if ($status === 'click to filter'){
        $(this).parent().find('.open-in-mobile').slideDown(300)
        $(this).css('margin-bottom','1rem')
        $(this).find('.title').text('click to close')
    }else {
        $(this).parent().find('.open-in-mobile').slideUp(300)
        $(this).css('margin-bottom','0')
        $(this).find('.title').text('click to filter')
    }
})