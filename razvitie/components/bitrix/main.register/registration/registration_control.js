BX.namespace('BX.RegistrationForm');

(function(){
  BX.RegistrationForm = {

    init: function(properties){
      this.formID = properties.formID;
      this.buttonClass = properties.buttonClass;
      this.errorMessage = '';
      this.isFoundError = false;
      this.bindEvents();
    },

    bindEvents: function(){
      this.form = BX(this.formID);
      button = this.form.querySelector("."+this.buttonClass);
      BX.bind(this.form, "submit", BX.delegate(this.formSubmit, this));
      BX.bind(BX('close'), "click", BX.proxy(this.closePopup, this));
    },

    formSubmit: function(event){
      if(this.isValidForm(this.form)){
        this.form.action=window.location;
        this.form.submit();
        return true;
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
        return false;
      }
    },
    sendRequest: function(event){
      var form = BX(event.target).closest('form');
      if(this.isValidForm(form)){
        console.log(BX.ajax.prepareForm(form));
        BX.ajax({
          method: "POST",
          dataType: 'json',
          url: '/bitrix/templates/razvitie/ajax/profile_changing.php',
          data: BX.ajax.prepareForm(form).data,
          onsuccess:BX.delegate(function(data){
            this.showResult(data, form);
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
      for(let key in data.data["REGISTER"]){
        console.log(key);
        switch(key){
          case "NAME":
          {
            if(/[^A-Za-zА-яа-яЁё\s]/ui.test(data.data["REGISTER"][key])){
              if(!this.isFoundError){
                this.errorMessage = "Для ввода имени используйте только символы русского и английского алфавитов";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "LOGIN":
          {
            if(/[^-A-Za-z0-9_,]/ui.test(data.data["REGISTER"][key])){
              if(!this.isFoundError){
                this.errorMessage = "Для ввода логина используйте только символы английского алфавита, цифры, - и _";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "EMAIL":
          {
            if(!(/^([-A-Za-z0-9_\.])+@([-A-Za-z0-9_\.])+\.([A-Za-z]{2,4})$/ui.test(data.data["REGISTER"][key]))){
              if(!this.isFoundError){
                this.errorMessage = "Введите корректный e-mail";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "PASSWORD":
          {
            if(/[^-A-Za-z0-9_!@#$%\^&*()+]/ui.test(data.data["REGISTER"][key])){
              if(!this.isFoundError){
                this.errorMessage = "Пароль должен содержать только символы английского алфавита, а также: 0-9 ! # $ % ^ & * ( ) _ - +";
                this.isFoundError = true;
              }
            }
            if(data.data["REGISTER"][key].length<6){
              if(!this.isFoundError){
                this.errorMessage = "Пароль должен содержать не менее 6 символов";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "CONFIRM_PASSWORD":
          {
            if(data.data["REGISTER"][key]!==data.data["REGISTER"]["PASSWORD"]){
              if(!this.isFoundError){
                this.errorMessage = "Пароли не совпадают";
                this.isFoundError = true;
              }
            }
            break;
          }
        }
      }
      return !this.isFoundError;
    },

    closePopup: function(event){
      BX.removeClass(BX.findParent(event.target, {'class':'notice'}), "open");
      clearTimeout(this.noticeClose);
      this.noticeCloseDuration = false;
    },
  };
})();