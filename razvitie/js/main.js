'use strict'

$(document).ready(function () {

    // header функционал
    $('.header__services-item-name').click(function () {
        $(this).toggleClass('open')
            .parent().find('.header__services-sub-list').slideToggle();
    });

    // custom checkbox
    $('.label-checkbox').click(function () {
        if ($(this).find('input').prop('checked')) {
            $(this).addClass('check');
        } else {
            $(this).removeClass('check');
        }
    });

    // Маски для input
    $('.mask-snils').mask('999-999-999 99');
    $('.mask-diplom').mask('999999 9999999');

    // reviews rating block
    let rating = Number($('.average-rating').attr('rating')).toFixed(1); //Беру значение и округляю до 1 знака после запятой
    $('.rating-number').text(rating.slice(0, 1) + ',' + rating.slice(-1)); //Вставляю в текст оценку
    $('.rating-icons-full').css('width', Math.trunc(rating) * 20 + 4 + Number(rating.slice(-1)) / 10 * 12 + '%'); //Высчитываю ширину для коректного отображения звезд

    // cтилизация input-range
    let input_range = $('.input-range');
    $('.range-min').text(input_range.attr('min'));
    $('.range-max').text(input_range.attr('max'));
    $('.input-range-value').text(input_range.val());
    input_range.css({
        '--value': input_range.val(),
        '--min': input_range.attr('min'),
        '--max': input_range.attr('max'),
    });
    $(document).on('input change', '.input-range', function() {
        input_range.css({
            '--value': $(this).val(),
            '--min': $(this).attr('min'),
            '--max': $(this).attr('max'),
        });
        $('.input-range-value').text($(this).val());
        input_range.parent().removeClass('disabled');

        if ($(this).hasClass('desktop')) {
            $('.input-range.mobile').val($(this).val());
        } else {
            $('.input-range.desktop').val($(this).val());
        }
    });

    // about-content text limited
    $('.about-content__next').click(function () {
        $(this).css('display', 'none')
            .parent().find('article').css('display', 'block');
    });

    // services функционал левого меню
    $('.services__services-item-name').click(function () {
        $(this).toggleClass('open')
            .parent().find('.services__services-sub-list').slideToggle();
    });
    $('.mobile .services__services-menu-name').click(function () {
        let this_btn = $(this);
        if ($(this).hasClass('open')) {
            $('.services__filter-block').slideUp(100);
            $(this).parent().find('.services__services-list').slideUp(300);
            setTimeout(function () {
                this_btn.removeClass('open');
            }, 250);
        } else {
            $(this).addClass('open')
                .parent().find('.services__services-list').slideDown(300);
            setTimeout(function () {
                $('.services__filter-block').slideDown(100);
            }, 250)
        }
    });
    $('.mobile .services__filter-name').click(function () {
        $(this).toggleClass('open');
        $('.services__range-block').slideToggle();
    });

    // burger menu
    $('.header__burger-menu-btn').click(function () {
        if ($('.personal__menu--burger').is(':visible')) {
            $('.personal__menu--burger').slideUp();
            $('.header__mobile-personal').removeClass('open');
            $('.header__burger-menu').slideToggle();
            $(this).toggleClass('open');
        } else {
            $('body').toggleClass('lock');
            $('.header__burger-menu').slideToggle();
            $(this).toggleClass('open');
        }
    });
    $('.header__mobile-personal').click(function () {
        if ($('.header__burger-menu').is(':visible')) {
            $('.header__burger-menu').slideUp();
            $('.header__burger-menu-btn').removeClass('open');
            $('.personal__menu--burger').slideToggle();
            $(this).toggleClass('open');
        } else {
            $('body').toggleClass('lock');
            $('.personal__menu--burger').slideToggle();
            $(this).toggleClass('open');
        }
    });

    // Search mobile
    $('.search-mobile').click(function () {
        $('.header__block--mobile').addClass('active');
    });
    $('.header__search-bg').click(function () {
        $('.header__block--mobile').removeClass('active');
    });

    // order function
    $('.calculate__input--select').change(function () {
        if ($(this).val() === 'corporate') {
            $('.calculate').addClass('corporate');
        } else {
            $('.calculate').removeClass('corporate');
            $('.calculate__item--corporate').find('input').val('');
        }
    });

    // personal edit
    $('.personal__edit-btn').click(function () {
        $(this).parent().parent().find('.calculate__input').removeAttr('disabled');
        $(this).parent().find('.calculate__btn').css('display', 'flex');
    });
    $('textarea.calculate__input').each(function () {
        $(this)[0].style.height = 'auto';
        $(this)[0].style.height = $(this)[0].scrollHeight + 2 + "px";
    });
    $('textarea.calculate__input').on('input', function () {
        $(this)[0].style.height = 'auto';
        $(this)[0].style.height = $(this)[0].scrollHeight + 2 + "px";
    });

    // Services sub list
    $('button.services__btn').click(function () {
        let _this = $(this);

        _this.parent().find('.services__card-sub-item').slideToggle(250);
        if (_this.parent().hasClass('open')) {
            $('.services__card-open-bg').hide();
            setTimeout(function () {
                _this.parent().toggleClass('open');
            }, 250);
        } else {
            _this.parent().toggleClass('open');
            $('.services__card-open-bg').show();
        }
    });
    $('.services__card-open-bg').click(function () {
        $(this).hide();
        $('.services__card-sub-item').slideUp(250);
        setTimeout(function () {
            $('.services__item.open').removeClass('open');
        }, 250);
    });

    // Main sub list
    $('.prog-study__item-btn').click(function () {
        let _this = $(this);

        _this.parent().find('.prog-study__card-sub-item').slideToggle(250);
        if (_this.parent().hasClass('open')) {
            $('.prog-study__card-open-bg').hide();
            setTimeout(function () {
                _this.parent().toggleClass('open');
            }, 250);
        } else {
            _this.parent().toggleClass('open');
            $('.prog-study__card-open-bg').show();
        }
    });
    $('.prog-study__card-open-bg').click(function () {
        $(this).hide();
        $('.prog-study__card-sub-item').slideUp(250);
        setTimeout(function () {
            $('.prog-study__item.open').removeClass('open');
        }, 250);
    });

    // Disable filter
    $('.filter-disable').click(function () {
        $('.services__label-range').addClass('disabled');
    });

    // Document open
    $('.review-img').click(function () {
        $(this).find('.review-doc').fadeIn();
        $('.review-doc-bg').fadeIn();
        console.log('123')
    });
    $('.review-doc-bg').click(function () {
        $(this).fadeOut();
        $('.review-doc').fadeOut();
    });
    $('.review-img-slider').click(function () {
        $('.review-doc-img-box').attr('src', $(this).find('.review-doc').attr('src'));
        $('.review-doc-bg').fadeIn();
    });

    // modal
    $('.modal-cart .close, .modal-sent .close').click(function () {
        $('.modal-cart, .modal-sent').hide();
        $('body').removeClass('lock');
    });

    // order-pay
    $('.order-pay input').change(function () {
        $('.order-pay').removeClass('active');
        $(this).parent().addClass('active');
    });

    // img for about-intro--service
    let contentHeight = $('.about-intro').height();
    if ($(window).width() > 768) {
        $('.about-intro__img-box').css('max-height', contentHeight + 'px');
        $('.about-intro__img-box img').css('max-height', contentHeight + 'px');

        contentHeight = $('.about-intro__title-content').height();
        $('.about-intro--service .about-intro__img-box').css('max-height', contentHeight + rem(15) + 'px');
        $('.about-intro--service .about-intro__img-box img').css('max-height', contentHeight + rem(15) + 'px');
    }

    $(window).resize(function () {
        contentHeight = $('.about-intro').height();
        if ($(window).width() > 768) {
            $('.about-intro__img-box').css('max-height', contentHeight + 'px');
            $('.about-intro__img-box img').css('max-height', contentHeight + 'px');

            contentHeight = $('.about-intro__title-content').height();
            $('.about-intro--service .about-intro__img-box').css('max-height', contentHeight + rem(15) + 'px');
            $('.about-intro--service .about-intro__img-box img').css('max-height', contentHeight + rem(15) + 'px');
        } else {
            $('.about-intro__img-box').css('max-height', 'auto');
            $('.about-intro__img-box img').css('max-height', 'auto');
        }
    });

    // Search
    $('.header__search-del').click(function () {
        $('.header__search-input').val('');
    });

    // Login
    $('.order-complete-btn').click(function () {
        $('.calculate__block').hide();
        $('.complete').show();
    });

    // Inputs focus - svg change
    $('input').focusin(function () {
        $(this).parent().find('svg').addClass('active');
    });
    $('input').focusout(function () {
        $(this).parent().find('svg').removeClass('active');
    });

    // up-btn
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 600) {
            $('.up-btn').fadeIn(200);
        } else {
            $('.up-btn').fadeOut(200);
        }
    });

});