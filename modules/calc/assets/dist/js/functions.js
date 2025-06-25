  // Удаление дублей из массива
  function getUnique(arr){var i=0,current,length=arr.length,unique=[];for(;i<length;i++){current = arr[i];if (!~unique.indexOf(current))unique.push(current)}return unique}

  // Полифил closest(this, parent)
  function closest(el,s){var mf,p;['matches','webkitMatchesSelector','mozMatchesSelector','msMatchesSelector','oMatchesSelector'].some(function(f){if(typeof document.body[f]=='function'){mf=f;return true}return false});while(el){p=el.parentElement;if(p&&p[mf](s)){return p}el=p;}return null}

  // Отключение клавиш: 13 enter, 38/40 вверх/вниз
  // f.FIELD.disableKeys([13,38,40]);
  Number.isInteger=Number.isInteger||function(value){return typeof value==='number'&&Number.isFinite(value)&&!(value%1)};if(!Array.isArray){Array.isArray=function(arg){return Object.prototype.toString.call(arg)==='[object Array]'}}Element.prototype.disableKeys=function(key){this.addEventListener('keydown',function(e){if((Number.isInteger(key)&&e.keyCode==key)||(Array.isArray(key)&&key.indexOf(e.keyCode)>-1))e.preventDefault();return false})};

  // Маска телефона: element.maskPhone()
  Element.prototype.maskPhone=function(){var s;function t(event){var e=event||window.event;e.keyCode&&(s=e.keyCode),this.selectionStart<3&&e.preventDefault();var t="+7 (___) ___ ____",n=0,a=t.replace(/\D/g,""),r=this.value.replace(/\D/g,""),l=t.replace(/[_\d]/g,function(e){return n<r.length?r.charAt(n++)||a.charAt(n):e});-1!=(n=l.indexOf("_"))&&(n<5&&(n=3),l=l.slice(0,n));var i=t.substr(0,this.value.length).replace(/_+/g,function(e){return"\\d{1,"+e.length+"}"}).replace(/[+()]/g,"\\$&");(!(i=new RegExp("^"+i+"$")).test(this.value)||this.value.length<5||47<s&&s<58)&&(this.value=l),"blur"==e.type&&this.value.length<5&&(this.value="")}this.addEventListener("input",t,!1),this.addEventListener("focus",t,!1),this.addEventListener("blur",t,!1),this.addEventListener("keydown",t,!1)};

  // Округление: Math.round10(number, -2)
  function decimal_adjust(t,v,e){if(typeof e==='undefined'||+e===0){return Math[t](v)}v=+v;e=+e;if(isNaN(v)||!(typeof e==='number'&&e%1===0)){return NaN}v=v.toString().split('e');v=Math[t](+(v[0]+'e'+(v[1]?(+v[1]-e):-e)));v=v.toString().split('e');return +(v[0]+'e'+(v[1]?(+v[1]+e):e))}if(!Math.round10){Math.round10=function(v,e){return decimal_adjust('round',v,e)}}

  // Склонение существительных с числительными: declination(number, ['грамм', 'грамма', 'граммов'])
  function declination(n,form){n=Math.abs(n)%100;if(n>=5&&n<=20)return form[2];n%=10;if(n==1)return form[0];if(n>=2&&n<=4)return form[1];return form[2]}

  // Получение значения выбранного radio: radio_checked_value(колекция_элементов_radio)
  function radio_checked_value(el){for(var i=0;i<el.length;i++)if(el[i].checked)return el[i].value}

  // Разрядность числа
  new Intl.NumberFormat('ru-RU').format(ЧИСЛО)

  // Ввод только чисел
  this.value = this.value.replace(/[^\d;]/g,'');

  // Переключение класса c
  Element.prototype.toggleClassRadio=function(c){$(this).siblings().removeClass(c);this.classList.add(c)};
  Element.prototype.toggleClass=function(c){this.classList.contains(c)?this.classList.remove(c):this.classList.add(c)};

  // Первая бука в верхний регистр
  // CSS :first-letter {text-transform: capitalize}
  //String.prototype.capitalize=function(){return this.charAt(0).toUpperCase()+this.slice(1)}

  // Вызов события
  element.dispatchEvent(new Event('СОБЫТИЕ'));

  // Получение параметра из option: element.valueAttr(атрибут)
  Element.prototype.valueAttr = function(attr)
  {
    return this.getElementsByTagName('option')[this.selectedIndex].getAttribute(attr);
  };

  // Заполнение select: element.fillSelect(объект_параметров)
  Element.prototype.fillSelect = function(d)
  {
    var o = '';
    d.forEach(function(item){
      o += '<option value="'+item.name+'"';
      for (var key in item) {
        if (key != 'name' && item[key]) {
          o += ' data-'+key+'="'+item[key]+'"';
        }
      }
      o += '>'+item.name+'</option>';

      // Все параметры в json
      //o += '<option value="'+item.name+'" data-json=\''+JSON.stringify(item)+'\'>'+item.name+'</option>';
    });
    this.innerHTML = o;
  };
  // Заполнение select: 'Параметр': {'Параметр': {},}
  Element.prototype.fillSelect=function(d){var o='';for(var key in d)o+='<option value="'+key+'" data-json=\''+JSON.stringify(d[key])+'\'>'+key+'</option>';this.innerHTML=o};


  // Стилизация полей в select2: element.select2Init(объект_параметров)
  // {placeholder:'',minimumResultsForSearch:-1,width:'100%'}
  Element.prototype.select2Init = function(d){if(typeof($(this).data('select2')) === undefined)$(this).select2('destroy');$(this).select2(d)};

      // Слайдер
      sliderInit({
        value:   s.square.value,
        min:   s.square.min,
        max:   s.square.max,
        step:  1,
      }, f.q2, f.q2s);
    // Инициализация слайдера
    function sliderInit(d, input, inputSlider)
    {
      // Input
      $(input).attr({
        min:   d.min,
        max:   d.max,
        step:  d.step,
        value: d.value,
      });

      // Слайдер
      $(inputSlider).ionRangeSlider({
        grid: false,
        //grid_num: 5,
        min: d.min,
        max: d.max,
        from: d.value,
        step: d.step,
        hide_min_max: true,
        hide_from_to: true,

        // Изменение слайдера
        onChange: function(d){
          $(input).val(d.from);
        },
      });
      var slider = $(inputSlider).data('ionRangeSlider');

      // Изменение input
      $(input).on('input', function(){
        slider.update({
          from: $(this).val(),
        });
      });
    }