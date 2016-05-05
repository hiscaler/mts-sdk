<?php

namespace yadjet\mts\sdk;

class DataGetter
{

    public static function friendlyLinkAll()
    {
        return [
            'items' => [],
            '_meta' => [
                'pageCount' => 1,
                'count' => 1,
                'pageSize' => 1,
                'currentPage' => 1,
            ]
        ];
    }

    public static function friendlyLinkList($params = array())
    {
        return [];
    }

}
