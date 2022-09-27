BX.namespace('BX.Feedback');

(function(){
  BX.Feedback = {

    init: function(properties){
      this.formID = properties.formID;
      this.buttonClass = properties.buttonClass;
      this.elementName = "elementName" in properties? properties.elementName : "";
      this.sectionName = "sectionName" in properties? properties.sectionName : "";
      this.errorMessage = '';
      this.isFoundError = false;
      console.log(this);
      this.bindEvents();
    },

    bindEvents: function(){
      this.form = BX(this.formID);
      button = this.form.querySelector("."+this.buttonClass);
      BX.bind(this.form, "submit", BX.delegate(this.sendRequest, this));
      BX.bind(BX('close'), "click", BX.proxy(this.closePopup, this));
      BX.bind(BX('success_close'), "click", BX.proxy(this.closeSuccessPopup, this));
      BX.bind(BX('second_success_close'), "click", BX.proxy(this.closeSuccessPopup, this));
    },

    sendRequest: function(event){
      var form = BX(event.target).closest('form');
      if(this.isValidForm(form)){
        data = BX.ajax.prepareForm(form).data;
        if(this.elementName){
          data.elementName = this.elementName;
        }
        else if(this.sectionName){
          data.sectionName = this.sectionName;
        }
        BX.ajax({
          method: "POST",
          dataType: 'json',
          url: '/bitrix/templates/razvitie/ajax/consultation.php',
          data: data,
          onsuccess:BX.delegate(function(data){
            this.showResult(data);
          }, this),
          onfailure:BX.delegate(function(data, index, error, error2){
            console.log(eval('('+data.responseText+')'));
            console.log(error2);
          })
        });
      }
      else{
        BX('notice').querySelector('.notice__text').innerHTML = this.errorMessage;
        BX.addClass(BX('notice'), 'open');
        if (!this.noticeCloseDuration) {
            this.noticeCloseDuration = true;
            this.noticeClose = setTimeout(function () {
              BX.removeClass(BX('notice'), "open");
                this.noticeCloseDuration = false;
            }, 2000);
        }
      }
    },

    isValidForm: function(form){
      let data = BX.ajax.prepareForm(form);
      for(let key in data.data){
        switch(key){
          case "USER_NAME":
          {
            if(/[^A-Za-zА-яа-яЁё\s]/ui.test(data.data[key])){
              if(!this.isFoundError){
                this.errorMessage = "Для ввода имени используйте только символы русского и английского алфавитов";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "USER_PHONE":
          {
            if(!(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/ui.test(data.data[key]))){
              if(!this.isFoundError){
                this.errorMessage = "Введите корректный номер телефона";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "POLICY":
          {
            if(!(data.data[key])){
              if(!this.isFoundError){
                this.errorMessage = "Для того, чтобы с Вами связались, нужно Ваше согласие на обработку Ваших персональных данных";
                this.isFoundError = true;
              }
            }
          }
          
        }
      }
      if(!("POLICY" in data.data)){
        if(!this.isFoundError){
          this.errorMessage = "Для того, чтобы с Вами связались, нужно Ваше согласие на обработку Ваших персональных данных";
          this.isFoundError = true;
        }
      }
      return !this.isFoundError;
    },

    closePopup: function(event){
      BX.removeClass(BX.findParent(event.target, {'class':'notice'}), "open");
      clearTimeout(this.noticeClose);
      this.noticeCloseDuration = false;
    },

    closeSuccessPopup: function(event){
      BX.removeClass(BX('body'), "lock");
      BX.findParent(event.target, {'class':'modal-sent'}).style.display = "none";
    },

    showResult: function(data) {
      console.log(data);
      //BX('modal-sent').style.display = "flex";
      //BX.addClass(BX('body'), "lock");
    }
  };
})();