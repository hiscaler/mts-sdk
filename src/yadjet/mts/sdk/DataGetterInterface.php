<?php

namespace yadjet\mts\sdk;

interface DataGetterInterface
{

    public static function parseQuery($fields = '*', $where = [], $orderBy = 'ordering.asc', $offset = 0, $limit = 10);
}
