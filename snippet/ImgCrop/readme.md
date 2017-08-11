#ImgCrop
##Работа с изображениями

Текущая версия: 8.0 (16.06.2017)

- Создаете сниппет с именем "ImgCrop00" (желательно с номером версии)
- Пихаем код.
- Изменяем путь к "nophoto.png"

###Возможности
- Кроп картинки с сохранением пропорций. Вписывание в прямоугольник (с полями и без) и заполнение прямоугольника.

![sdf](http://april-inter.ru/tmp1.jpg)
![sdf](http://april-inter.ru/tmp2.jpg)
![sdf](http://april-inter.ru/tmp3.jpg)

- Можно задать фон полей (цвет, цвет точки в исх.картинке или сама картинка (с применением фильтров)

![sdf](http://april-inter.ru/tmp4.jpg)
![sdf](http://april-inter.ru/tmp5.jpg)
![sdf](http://april-inter.ru/tmp6.jpg)

- Наложение водяного знака

![sdf](http://april-inter.ru/tmp7.jpg)

- Применение графических фильтров

![sdf](http://april-inter.ru/tmp8.jpg)

- Округление картинки

![sdf](http://april-inter.ru/tmp9.jpg)
![sdf](http://april-inter.ru/tmp10.jpg)

- Наложение дополнительной картинки

![sdf](http://april-inter.ru/tmp11.jpg)

- Сохранение в определенный файл

- Изменение качества картинки на выходе

![sdf](http://april-inter.ru/tmp12.jpg)

###В будущих версиях
- Путь к картинке для фона
- Очистка старых миниатюр

####Запомнить
- Делает картинку красивой:
```php
array( 'filter' => IMG_FILTER_BRIGHTNESS.';5|'.IMG_FILTER_CONTRAST.';-10|'.IMG_FILTER_SMOOTH.';-20' )
```
![sdf](http://april-inter.ru/tmp13.jpg)
![sdf](http://april-inter.ru/tmp14.jpg)
