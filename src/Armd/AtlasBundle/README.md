Модуль "Атлас"

- Отображение объектов
    - Памятники
    - Музеи
    - Библиотеки
- Фильтрация объектов по типам



Сущности (таблички):
    atlas_object - объекты на карте
        id
        title
        lat
        lon
        user_id: 1977
        images: []

    atlas_region - регионы России
        id
        title

    atlas_object_category - дерево категорий объектов
        id
        title
        parent_id
        lft
        rgt
        lvl

    atlas_route - маршруты
        id: 1234
        title: "Название маршрута"
        points: []
        user_id: 1977