$(document).ready(function() {
    const searchInput = $("#search-voice");
    const voiceSearchBtn = $("#voiceSearch");

    // بررسی پشتیبانی از Web Speech API
    if ('webkitSpeechRecognition' in window) {
        let recognition = new webkitSpeechRecognition();
        // recognition.lang = "fa-IR"; // تنظیم زبان فارسی
        recognition.interimResults = false; // نمایش فقط نتیجه نهایی
        recognition.maxAlternatives = 1;

        voiceSearchBtn.click(function() {
            recognition.start();
        });

        recognition.onresult = function(event) {
            let transcript = event.results[0][0].transcript;
            searchInput.val(transcript);
        };

        recognition.onerror = function(event) {
            alert("مشکلی در تشخیص صدا به وجود آمد: " + event.error);
        };

    } else {
        voiceSearchBtn.prop("disabled", true).text("🚫 پشتیبانی نمی‌شود");
    }

    $("#searchBtn").click(function() {
        let query = searchInput.val();
        if (query.trim() !== "") {
            alert("در حال جستجو برای: " + query);
            // اینجا می‌توانی کد ارسال درخواست جستجو را اضافه کنی
        } else {
            alert("لطفا یک متن وارد کنید.");
        }
    });

});