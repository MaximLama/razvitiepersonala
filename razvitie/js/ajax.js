$(document).ready(function(){
  $(".services__input-range").on("change", function(){
    let value = $(this).val();
    console.log(value);
    data = {
      value:value
    };
    $.ajax({
      type:"POST",
      url:window.location,
      data:data,
      success:function(data){
        let container = $(data).find('.services__cards-block');
        $('.services__cards-block')[0].innerHTML = container[0].innerHTML;

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
      },
      error:function(data){
        console.log(data);
      }
    });
  });
  $(".filter-disable").on("click", function(){
    $.ajax({
      type:"POST",
      url:window.location,
      data:{},
      success:function(data){
        let container = $(data).find('.services__cards-block');
        $('.services__cards-block')[0].innerHTML = container[0].innerHTML;

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
      },
      error:function(data){
        console.log(data);
      }
    });
  });
  $('.buy').on("click", function(){
    let id = $(this).attr("id");
    $.ajax({
      type:'POST',
      url: '/bitrix/templates/razvitie/ajax/hasItemInBasket.php',
      data:{
        "ID":id
      },
      dataType:'json',
      success:function(data){
        if(data.hasItem!==true){
          var ajax = $.ajax({
                      type: 'POST',
                      url: location.pathname + '?action=ADD2BASKET&id=' + id,
                      data: {
                        ajax_basket: 'Y',
                        quantity: '1'
                      },
                      dataType:'json',
                      success:function(){
                        $('.modal-cart').css('display', 'flex');
                        $('body').addClass('lock');
                      }
                    });
          ajax.done(function(data) {
              BX.onCustomEvent('OnBasketChange');
          });
        }
        else{
          $('.modal-cart').css('display', 'flex');
          $('body').addClass('lock');
        }
      },
      error:function(data){
        console.log(data);
      }
    });
  });

  $('.header__search-del').on("click", function(event){
    $(event.target).closest('form').find('input').attr("value", "");
  });
  /*$('.personal__buttons .calculate__btn').click(function () {
      let form = $(this).closest('form');
      let inputs = form.find('.calculate__input');
      let errorMessage = "";
      let isFoundError = false;
      inputs.each(function(i, e){
        name = $(e).attr('name');
        switch(name){
          case "NAME":
            if(/[^A-Za-zА-яа-яЁё\s]/ui.test($(e).attr("value"))){
              if(!isFoundError){
                errorMessage = "Для ввода имени используйте только символы русского и английского алфавитов";
                isFoundError = true;
              }
            }
            break;
        }
      });
      if(errorMessage){
        $('.notice__text')[0].innerHTML = errorMessage;
        $('#notice').addClass('open');
        if (!noticeCloseDuration) {
            noticeCloseDuration = true;
            noticeClose = setTimeout(function () {
              BX.removeClass(BX('notice'), "open");
                noticeCloseDuration = false;
            }, 2000);
        }
      }
      else{
        $.ajax({
          type: 'post',
          url: '/bitrix/templates/razvitie/ajax/profile_changing.php',
          data: form.serializeArray(),
          dataType: 'json',
          success:function(data) {
            console.log(data);
          },
          error:function(data){
            console.log(data);
          }
        });
        $(this).parent().parent().find('.calculate__input').attr('disabled', 'disabled');
        $(this).parent().find('.calculate__btn').hide();
      }
  });
  /*BX('notice').querySelector('.notice__text').innerHTML = propsErrors[0];
  BX.addClass(BX('notice'), 'open');
  if (!this.noticeCloseDuration) {
      this.noticeCloseDuration = true;
      this.noticeClose = setTimeout(function () {
        BX.removeClass(BX('notice'), "open");
          this.noticeCloseDuration = false;
      }, 2000);
  }*/
  /*$("#close").on("click", function(event){
    console.log($(event.target).closest('.notice'));
  })*/
  /*BX.bind(BX('close'), "click", BX.proxy(function(event){
          BX.removeClass(BX.findParent(event.target, {'class':'notice'}), "open");
          clearTimeout(this.noticeClose);
          this.noticeCloseDuration = false;
        }, this))*/
});