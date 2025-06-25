# Yii2 DateTimePicker widget

### Описание

Виджет DateTimePicker ( http://xdsoft.net/jqplugins/datetimepicker ) для Yii2

### Скриншоты

![ScreenShot](https://raw.githubusercontent.com/vakorovin/yii2-datetimepicker/master/picker/screen/1.png)

![ScreenShot](https://raw.githubusercontent.com/vakorovin/yii2-datetimepicker/master/picker/screen/2.png)

![ScreenShot](https://raw.githubusercontent.com/vakorovin/yii2-datetimepicker/master/picker/screen/3.png)

### Установка

В корне вашего проекта в composer.json в раздел "require" пропишите:

    "vakorovin/yii2-datetimepicker": "dev-master"

и после выполните команду:

    php composer.phar install

Или же выполните команду:

    php composer.phar require --prefer-dist vakorovin/yii2-datetimepicker "dev-master"

### Использование

    use vakorovin\datetimepicker\Datetimepicker;

    echo $form->field($model, 'created')->widget(Datetimepicker::className(),[
        'options' => [
            'lang' => 'en',
        ]
    ]);

### Список доступных пареметров находится на сайте автора плагина DateTimePicker: 

http://xdsoft.net/jqplugins/datetimepicker
