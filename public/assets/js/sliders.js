let category_slider = new Swiper(".category-slider", {
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 24,
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 24,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
        1200: {
            slidesPerView: 4,
            spaceBetween: 24,
        },
        1500:{
            slidesPerView: 5,
            spaceBetween: 24,
        },
        1700:{
            slidesPerView: 6,
            spaceBetween: 24,
        },
    },
    navigation: {
        nextEl: ".button-next",
        prevEl: ".button-prev",
    },
});
let testimonials = new Swiper(".testimonials", {
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".button-next",
        prevEl: ".button-prev",
    },
});
var freelancers = new Swiper(".freelancers", {
    spaceBetween: 19,
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 3,
        },
        1200: {
            slidesPerView: 4,
        },
    },
});
var portfolio = new Swiper(".portfolio-slider", {
    spaceBetween : 20,
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        576: {
            slidesPerView: 2,
        },
        767: {
            slidesPerView: 3,
        },
        992: {
            slidesPerView: 4,
        },
        1500: {
            slidesPerView: 5,
        },

    },
    navigation: {
        nextEl: ".portfolio-prev",
        prevEl: ".portfolio-next",
    },
    loop : true,
});
var talent_slider = new Swiper(".talent-slider", {
    spaceBetween: 20,
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        576:{
            slidesPerView: 1.19,
        },
        767: {
            slidesPerView: 1.8,
        },
        992: {
            slidesPerView: 2.2,
        },
        1200: {
            slidesPerView: 2.5,
        },

    },
});
var employer_slider = new Swiper(".employer-slider", {
    navigation: {
        nextEl: ".next-slide",
        prevEl: ".prev-slide",
    },
    loop: true
});