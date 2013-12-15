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



/*=====================
==== MathObsessed ===*/
Array of "Images of Russia" entities with more than 1 videos:
    1475, 812, 1490, 1456, 1497, 792, 742, 1211, 793, 796, 797, 725, 783, 791, 772, 788, 731, 789, 790, 756