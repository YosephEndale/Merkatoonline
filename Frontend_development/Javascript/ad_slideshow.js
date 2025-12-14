var swiper = new Swiper('.slideshow-container', {
    slidesPerView: 1,
    spaceBetween: 0,
    loop: true,
    autoplay: {
        delay: 5000, // 5 seconds
        disableOnInteraction: false, // Enable autoplay even when touching slide
    },
    // Other options...
});
