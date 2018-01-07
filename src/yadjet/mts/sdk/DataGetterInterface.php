<?php

namespace yadjet\mts\sdk;

interface DataGetterInterface
{

    /**
     * Parse query condition
     *
     * @param string $fields
     * @param array $where
     * @param string $orderBy
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public static function parseQuery($fields = '*', $where = [], $orderBy = 'ordering.asc', $offset = 0, $limit = 10);

}
