// Пересчет rem в px для swiper
const rem = function (rem) {
    if ($(window).width() > 768) {
        // return 0.005208335 * $(window).width() * rem;
        return 0.004408335 * $(window).width() * rem;
    } else {
        // где 414 это ширина моб версии макета
        return (100/414) * (0.1 * $(window).width()) * rem;
    }
}

let checkAnimPlay = false;

const main_intro_slider = new Swiper('.main-intro__slider', {
    direction: 'horizontal',
    slidesPerView: 1,
    spaceBetween: rem(4),

    pagination: {
        el: '.main-intro__pagination',
        type: 'fraction',
        formatFractionCurrent: function (number) {
            if (number < 10) {
                return '0' + number;
            } else {
                return number;
            }
        },
        formatFractionTotal: function (number) {
            if (number < 10) {
                return '0' + number;
            } else {
                return number;
            }
        },
    },

    navigation: {
        nextEl: '.main-intro__arrow--right',
        prevEl: '.main-intro__arrow--left',
    },

    autoplay: {
        delay: 5000,
        disableOnInteraction: true,
    },

    on: {
        init: function () {
            let column = 0,
                row = 0;
            $('.main-intro__box').each(function () {
                $(this).attr('column', column).attr('row', row);
                if (column < 2) {
                    column++;
                } else {
                    column = 0;
                    row++;
                }
            });
        },

        slideChange: function () {
            if (!checkAnimPlay) {
                $('.main-intro__box').each(function () {
                    let column = Number($(this).attr('column')),
                        row = Number($(this).attr('row'));
                    checkAnimPlay = true;
                    setTimeout(() => checkAnimPlay = false, 500);
                    if (column === 0) {
                        if (row === 0) {
                            $(this).css('transform', 'translate3d(100%, 0, 0)');
                            $(this).attr('column', '1');
                        } else {
                            $(this).css('transform', 'translate3d(0, ' + ((row - 1) * 100) + '%, 0)');
                            $(this).attr('row', row - 1);
                        }
                    } else if (column === 1) {
                        if (row === 0) {
                            $(this).css('transform', 'translate3d(200%, 0, 0)');
                            $(this).attr('column', '2');
                        } else if (row === 3) {
                            $(this).css('transform', 'translate3d(0, 300%, 0)');
                            $(this).attr('column', '0');
                        }
                    } else {
                        if (row === 3) {
                            $(this).css('transform', 'translate3d(100%, 300%, 0)');
                            $(this).attr('column', '1');
                        } else {
                            $(this).css('transform', 'translate3d(200%, ' + ((row + 1) * 100) + '%, 0');
                            $(this).attr('row', row + 1);
                        }
                    }
                });
            }
        },
    }
});

const offers_slider = new Swiper('.offers__slider', {
    direction: 'horizontal',

    breakpoints: {
        769: {
            slidesPerView: 3,
            spaceBetween: rem(4.6),
        },
        0: {
            slidesPerView: 1.11,
            spaceBetween: rem(1),
        },
    },

    pagination: {
        el: '.offers__pagination',
        type: 'bullets',
        clickable: true,
    },

    navigation: {
        nextEl: '.offers__arrow-right',
        prevEl: '.offers__arrow-left',
    },

    autoplay: {
        delay: 7500,
        disableOnInteraction: true,
    },
});

let offers_current_slide = $('.offers__current-slide'),
    offers_slides = $('.offers__pagination').find('.swiper-pagination-bullet').length;

if (offers_slides < 10) {
    $('.offers__total-slide').text('0' + offers_slides);
} else {
    $('.offers__total-slide').text(offers_slides);
}
if (offers_slider.activeIndex < 9) {
    offers_current_slide.text('0' + (offers_slider.activeIndex + 1));
} else {
    offers_current_slide.text(offers_slider.activeIndex + 1);
}

offers_slider.on('slideChange', function () {
    if (offers_slider.activeIndex < 9) {
        offers_current_slide.text('0' + (offers_slider.activeIndex + 1));
    } else {
        offers_current_slide.text(offers_slider.activeIndex + 1);
    }
});



const reviews_slider = new Swiper('.reviews__slider', {
    direction: 'horizontal',
    loop: true,
    spaceBetween: rem(4.6),

    breakpoints: {
        769: {
            slidesPerView: 2.9,
        },
        0: {
            slidesPerView: 1,
        },
    },

    pagination: {
        el: '.reviews__pagination',
        type: 'bullets',
        clickable: true,
    },

    navigation: {
        nextEl: '.reviews__arrow-right',
        prevEl: '.reviews__arrow-left',
    },

    autoplay: {
        delay: 7500,
        disableOnInteraction: true,
    },
});

let reviews_current_slide = $('.reviews__current-slide'),
    reviews_slides = $('.reviews__pagination').find('.swiper-pagination-bullet').length;

if (reviews_slides < 10) {
    $('.reviews__total-slide').text('0' + reviews_slides);
} else {
    $('.reviews__total-slide').text(reviews_slides);
}
if (reviews_slider.realIndex < 9) {
    reviews_current_slide.text('0' + (reviews_slider.realIndex + 1));
} else {
    reviews_current_slide.text(reviews_slider.realIndex + 1);
}

reviews_slider.on('slideChange', function () {
    if (reviews_slider.realIndex < 9) {
        reviews_current_slide.text('0' + (reviews_slider.realIndex + 1));
    } else {
        reviews_current_slide.text(reviews_slider.realIndex + 1);
    }
});



const partners_slider = new Swiper('.partners__slider', {
    direction: 'horizontal',

    breakpoints: {
        769: {
            slidesPerView: 4,
            spaceBetween: rem(1),
            grid: {
                fill: 'row',
                rows: 2,
            },
        },
        0: {
            slidesPerView: 1.13,
            spaceBetween: rem(1),
        },
    },

    pagination: {
        el: '.partners__pagination',
        type: 'bullets',
        clickable: true,
        dynamicBullets: true,
    },

    navigation: {
        nextEl: '.partners__arrow-right',
        prevEl: '.partners__arrow-left',
    },

    autoplay: {
        delay: 7500,
        disableOnInteraction: true,
    },
});

let partners_current_slide = $('.partners__current-slide'),
    partners_slides = $('.partners__pagination').find('.swiper-pagination-bullet').length;

if (partners_slides < 10) {
    $('.partners__total-slide').text('0' + partners_slides);
} else {
    $('.partners__total-slide').text(partners_slides);
}
if (partners_slider.activeIndex < 9) {
    partners_current_slide.text('0' + (partners_slider.activeIndex + 1));
} else {
    partners_current_slide.text(partners_slider.activeIndex + 1);
}

partners_slider.on('slideChange', function () {
    if (partners_slider.activeIndex < 9) {
        partners_current_slide.text('0' + (partners_slider.activeIndex + 1));
    } else {
        partners_current_slide.text(partners_slider.activeIndex + 1);
    }
});



const courses_distance_slider = new Swiper('.courses__slider--distance', {
    direction: 'horizontal',

    breakpoints: {
        769: {
            slidesPerView: 2,
            spaceBetween: rem(5),
        },
        0: {
            slidesPerView: 1.1,
            spaceBetween: rem(2),
        },
    },

    pagination: {
        el: '.courses__pagination--distance',
        type: 'bullets',
        clickable: true,
    },

    navigation: {
        nextEl: '.courses__arrow-right--distance',
        prevEl: '.courses__arrow-left--distance',
    },

    autoplay: {
        delay: 5000,
        disableOnInteraction: true,
    },
});

let courses_distance_current_slide = $('.courses__current-slide--distance'),
    courses_distance_slides = $('.courses__pagination--distance').find('.swiper-pagination-bullet').length;

if (courses_distance_slides < 10) {
    $('.courses__total-slide--distance').text('0' + courses_distance_slides);
} else {
    $('.courses__total-slide--distance').text(courses_distance_slides);
}
if (courses_distance_slider.activeIndex < 9) {
    courses_distance_current_slide.text('0' + (courses_distance_slider.activeIndex + 1));
} else {
    courses_distance_current_slide.text(courses_distance_slider.activeIndex + 1);
}

courses_distance_slider.on('slideChange', function () {
    if (courses_distance_slider.activeIndex < 9) {
        courses_distance_current_slide.text('0' + (courses_distance_slider.activeIndex + 1));
    } else {
        courses_distance_current_slide.text(courses_distance_slider.activeIndex + 1);
    }
});



const courses_watched_slider = new Swiper('.courses__slider--watched', {
    direction: 'horizontal',

    breakpoints: {
        769: {
            slidesPerView: 3,
            spaceBetween: rem(4.4),
        },
        0: {
            slidesPerView: 1.1,
            spaceBetween: rem(2),
        },
    },

    pagination: {
        el: '.courses__pagination--watched',
        type: 'bullets',
        clickable: true,
    },

    navigation: {
        nextEl: '.courses__arrow-right--watched',
        prevEl: '.courses__arrow-left--watched',
    },

    autoplay: {
        delay: 5000,
        disableOnInteraction: true,
    },
});

let courses_watched_current_slide = $('.courses__current-slide--watched'),
    courses_watched_slides = $('.courses__pagination--watched').find('.swiper-pagination-bullet').length;

if (courses_watched_slides < 10) {
    $('.courses__total-slide--watched').text('0' + courses_watched_slides);
} else {
    $('.courses__total-slide--watched').text(courses_watched_slides);
}
if (courses_watched_slider.activeIndex < 9) {
    courses_watched_current_slide.text('0' + (courses_watched_slider.activeIndex + 1));
} else {
    courses_watched_current_slide.text(courses_watched_slider.activeIndex + 1);
}

courses_watched_slider.on('slideChange', function () {
    if (courses_watched_slider.activeIndex < 9) {
        courses_watched_current_slide.text('0' + (courses_watched_slider.activeIndex + 1));
    } else {
        courses_watched_current_slide.text(courses_watched_slider.activeIndex + 1);
    }
});


if ($(window).width() <= 768) {
    $('.partner-stages--five .partner-stages__cards').addClass('swiper')
        .find('.partner-stages__wrapper').addClass('swiper-wrapper')
        .find('.partner-stages__item').addClass('swiper-slide')
}

const partner_stages_slider = new Swiper('.partner-stages__cards.swiper', {
    direction: 'horizontal',
    slidesPerView: 1.4,
    spaceBetween: rem(4.6),

    navigation: {
        nextEl: '.partner-stages__arrow-right',
        prevEl: '.partner-stages__arrow-left',
    },

    autoplay: {
        delay: 5000,
        disableOnInteraction: true,
    },
});


const news_slider = new Swiper('.news__slider', {
    direction: 'horizontal',
    loop: true,
    spaceBetween: rem(5),

    breakpoints: {
        769: {
            slidesPerView: 2,
            spaceBetween: rem(5),
        },
        0: {
            slidesPerView: 1.08,
            spaceBetween: rem(1.2),
        },
    },

    pagination: {
        el: '.news__pagination',
        type: 'bullets',
        clickable: true,
    },

    navigation: {
        nextEl: '.news__arrow-right',
        prevEl: '.news__arrow-left',
    },
});

let news_current_slide = $('.news__current-slide'),
    news_slides = $('.news__pagination').find('.swiper-pagination-bullet').length;

if (news_slides < 10) {
    $('.news__total-slide').text('0' + news_slides);
} else {
    $('.news__total-slide').text(news_slides);
}
if (news_slider.realIndex < 9) {
    news_current_slide.text('0' + (news_slider.realIndex + 1));
} else {
    news_current_slide.text(news_slider.realIndex + 1);
}

news_slider.on('slideChange', function () {
    if (news_slider.realIndex < 9) {
        news_current_slide.text('0' + (news_slider.realIndex + 1));
    } else {
        news_current_slide.text(news_slider.realIndex + 1);
    }
});

if ($(window).width() <= 768) {
    $('.all-reviews__cards').addClass('swiper');
    $('.all-reviews__wrapper').addClass('swiper-wrapper').removeClass('all-reviews__wrapper');
    $('.all-reviews__item').addClass('swiper-slide');

    let all_reviews_slider = new Swiper('.all-reviews__cards', {
        direction: 'horizontal',
        loop: true,
        spaceBetween: rem(4.6),
        slidesPerView: 1,

        pagination: {
            el: '.all-reviews__pagination',
            type: 'bullets',
            clickable: true,
        },

        navigation: {
            nextEl: '.all-reviews__arrow-right',
            prevEl: '.all-reviews__arrow-left',
        },

        autoplay: {
            delay: 7500,
            disableOnInteraction: true,
        },
    });

    let all_reviews_current_slide = $('.all-reviews__current-slide'),
        all_reviews_slides = $('.all-reviews__pagination').find('.swiper-pagination-bullet').length;

    if (all_reviews_slides < 10) {
        $('.all-reviews__total-slide').text('0' + all_reviews_slides);
    } else {
        $('.all-reviews__total-slide').text(all_reviews_slides);
    }
    if (all_reviews_slider.realIndex < 9) {
        all_reviews_current_slide.text('0' + (all_reviews_slider.realIndex + 1));
    } else {
        all_reviews_current_slide.text(all_reviews_slider.realIndex + 1);
    }

    all_reviews_slider.on('slideChange', function () {
        if (all_reviews_slider.realIndex < 9) {
            all_reviews_current_slide.text('0' + (all_reviews_slider.realIndex + 1));
        } else {
            all_reviews_current_slide.text(all_reviews_slider.realIndex + 1);
        }
    });
}