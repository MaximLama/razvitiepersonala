BX.namespace('BX.PersonalForm');

(function(){
  BX.PersonalForm = {

    init: function(properties){
      console.log(properties);
      this.noticeCloseDuration = false;
      this.noticeClose = false;
      this.isFoundError = false;
      this.errorMessage = '';
      this.formClass = properties.formClass;
      this.sendClass = properties.sendClass;
      this.inputClass = properties.inputClass;
      this.containerId = properties.containerId;
      this.container = BX(this.containerId);
      this.forms = this.container.querySelectorAll('.'+this.formClass);
      this.bindEvents();
    },

    bindEvents: function(){
      var buttons = [];
      var instance = this;
      this.forms.forEach(function(item){
        buttons.push(BX(item).querySelector('.'+instance.sendClass));
      });
      for(var i = 0; i<buttons.length; i++){
        BX.bind(buttons[i], 'click', BX.delegate(instance.sendRequest, this));
      }
      BX.bind(BX('close'), "click", BX.proxy(this.closePopup, this));
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
          onfailure:BX.delegate(function(data){
            console.log(data);
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
      console.log(data);
      for(let key in data.data){
        switch(key){
          case "NAME":
          {
            if(/[^A-Za-zА-яа-яЁё\s]/ui.test(data.data[key])){
              if(!this.isFoundError){
                this.errorMessage = "Для ввода имени используйте только символы русского и английского алфавитов";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "CITY":
          {
            if(/[^-A-Za-zА-Яа-яЁё\s,]/ui.test(data.data[key])){
              if(!this.isFoundError){
                this.errorMessage = "Введите город и страну через запятую либо только город, используя символы русского и английского алфавитов, запятую и дефис";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "EMAIL":
          {
            if(!(/^([-A-Za-z0-9_\.])+@([-A-Za-z0-9_\.])+\.([A-Za-z]{2,4})$/ui.test(data.data[key]))){
              if(!this.isFoundError){
                this.errorMessage = "Введите корректный e-mail";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "PHONE":
          {
            if(!(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/ui.test(data.data[key]))){
              if(!this.isFoundError){
                this.errorMessage = "Введите корректный номер телефона";
                this.isFoundError = true;
              }
            }
            break;
          }

          case "STREET":
          {
            if(/[^-A-Za-zА-яа-яЁё\s,\.]/ui.test(data.data[key])){
              if(!this.isFoundError){
                this.errorMessage = "Для ввода адреса используйте символы русского и английского алфавитов, пробел, точку и запятую";
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

    showResult: function(data, form){
      console.log(data);
      for(let key in data.result){
        switch(key){
          case "NAME":{
            let input = BX(form).querySelector("."+this.inputClass+"[name='"+key+"']");
            input.value = data.result[key]?data.result[key]:"Имя не указано";
            break;
          }
          case "CITY":{
            let input = BX(form).querySelector('.'+this.inputClass+"[name='"+key+"']");
            console.log(input);
            input.value = data.result[key]?data.result[key]:"Город проживания не указан";
            break;
          }
          case "EMAIL":{
            let input = BX(form).querySelector("."+this.inputClass+"[name='"+key+"']");
            input.value = data.result[key];
            break;
          }
          case "PHONE":{
            let input = BX(form).querySelector("."+this.inputClass+"[name='"+key+"']");
            input.value = data.result[key]?data.result[key]:"";
            break;
          }
          case "STREET":{
            let input = BX(form).querySelector("."+this.inputClass+"[name='"+key+"']");
            input.value = data.result[key]?data.result[key]:"Место проживания не указано";
          }
        }
      }
      this.setFormDefaultState(form);
      if(data.error){
        BX('notice').querySelector('.notice__text').innerHTML = data.error;
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

    setFormDefaultState: function(form){
      let inputs = BX(form).querySelectorAll('.'+this.inputClass);
      for(let n in inputs){
        inputs[n].disabled = "disabled";
      }
      BX.hide(BX(form).querySelector('.'+this.sendClass));
    }
  };
})();