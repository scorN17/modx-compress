Сжимает один или несколько файлов.

Несколько файлов можно сжимать в один.

Редактировать нужно оригиналы, а сниппет сам будет пересоздавать сжатый файл.

- Создаем сниппет
- Пихаем код
- Используем так:

<link rel="stylesheet" type="text/css" href="[!Compress? &files=`template/css/: main.css, dop.css, catalog.css, catalogfilter.css, shop.css, superslider.css` &tofile=`template/css/all.compress.css`!]" />

<noscript><link rel="stylesheet" type="text/css" href="[!Compress? &file=`template/css/noscript.css`!]" /></noscript>

<script type="text/javascript" src="[!Compress? &files=`template/js/: dopscript.js, superslider.js, catalogfilter.js, shop.js, javascript.js` &tofile=`template/js/all.compress.js`!]"></script>


[!!] - используйте именно этот вариант, иначе сжатый файл не будет пересоздаваться при измении оригиналов.
