- Необходимо подключение 2-х файлов JQuery ("jquery-2.x.x.min.js" и "jquery-ui.min.js")
- Создать сниппет с именем "SuperPuperForms" и описанием "v001"
- Вставляем код из файла "snipper.txt"
- Заливаем на сервер папку "superpuperforms"

- В коде сниппета настраиваем параметры

Если нужна просто форма на странице:
[!SuperPuperForms? &form=`1`!]
- первая форма
[!SuperPuperForms? &form=`2`!]
- вторая форма (обратный звонок)

Если нужна Popup-форма:
[!SuperPuperForms? &popup=`1;2` &class=`className1;className2`!]
- По нажатию на элемент с классом "className1" - будет показываться форма 1, "className2" - вторая форма.

&class=`className1;className21,className22`
- вторая форма будет показываться по нажатию на элементы с классом "className21" или "className22"
