<?php
namespace Armd\SearchBundle\Model;

class SearchEnum
{
    const OBJECT_TYPE_NEWS = 'news';
    const OBJECT_TYPE_LECTURE = 'lecture';
    const OBJECT_TYPE_ATLAS = 'atlas_object';
    const OBJECT_TYPE_VIRTUAL_MUSEUM = 'virtual_museum';
    const OBJECT_TYPE_PERFOMANCE = 'perfomance';

    const START_INDEX_NEWS = 0;
    const START_INDEX_LECTURE = 1000000000;
    const START_INDEX_ATLAS = 2000000000;
    const START_INDEX_VIRTUAL_MUSEUM = 3000000000;
    const START_INDEX_PERFOMANCE = 4000000000;
    const START_INDEX_THEATER = 5000000000;

}