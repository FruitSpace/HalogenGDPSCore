# Halogen GDPS Core
> Это исходники HalogenCore с того самого HalogenHost. Есть ветка [ref](https://github.com/FruitSpace/HalogenGDPSCore/tree/ref), предназначенная для изучения и возможного использования другими хостингами.
> Основная ветка `master` предназначена для отдельных приваток с вырезанными зависимостями от хостингов
> Из минусов - не работает кастомная музыка, реализовывать ее будете сами.
>
> Как настраивается - можете посмотреть в [install.sh](install.sh). Основные конфигурации в папке `conf`. Папку `database` можно переименовывать, все файлы в ней указывают на нормальные из `api`.
>
> Модули и как с ними работать - все в папке [halcore/plugins](halcore/plugins).

> Создать установщики можно здесь: [Windows](https://gmdworld.xyz/create-gdps) и [Android](https://gmdworld.xyz/create-android-gdps)

> ⚠️ [Лицензия MIT](LICENSE): Вы можете использовать данный код в коммерческих и личных целях, изменять его и создавать свой на его основе. Единственное условие - наличие файла `LICENSE` в ваших репозиториях и сайтах/серверах, на которых используется ядрою
> Спасибо
<h4 align="center">Есть вопросы? Присоединяйтсь к Discord серверам FruitSpace и  HalogenCore Dev</p>
<p align="center">
  <a href="https://discord.gg/HgBQmMRKTB"><img src="https://discord.com/api/guilds/1146094673203581108/widget.png?style=banner2"></a>
  <a href="https://discord.gg/fruitspace"><img src="https://discord.com/api/guilds/1025382676875726898/widget.png?style=banner2"></a>
</p>

## Geometry Dash Private Server
**File Tree:**
```
📁 [ROOT]
|__ 📁 database | GD Redirect Endpoints
|__ 📁 api | GD Actual Endpoints
|__ 📁 conf | Configuration files
|__ 📁 halcore | Core itsef
```
