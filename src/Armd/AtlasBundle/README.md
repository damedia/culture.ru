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

Мысли вслух

AT.init({
    map: 'map',
    center: [97.759578,63.44936],
    zoom: 3
});

AT.addPoint({
    coords: [38.633253,55.648098]
});

AT.removePoints() //  2 или [10,2,64]

AT.clearAll() // полная очистка карты

- Отрисовать интерфейс
    - карта
    - фильтр тегов

-
