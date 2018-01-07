<?php

namespace yadjet\mts\sdk;

use Yii;

/**
 * 案例数据拉取
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class CaseGetter extends DataGetter implements DataGetterInterface
{

    use DataGetterTrait;

    public static function parseQuery($fields = null, $where = array(), $orderBy = 'ordering.asc', $offset = 0, $limit = 10)
    {
        if (empty($fields)) {
            $fields = 'title,keywords,description,content,clicks_count,ordering,published_at';
        }

        $condition = [
            'tenant_id' => self::getTenantId()
        ];

        return (new \yii\db\Query())
            ->select($fields)
            ->from('{{%slide}}')
            ->where($condition)
            ->orderBy(self::parseOrderBy($orderBy))
            ->offset($offset)
            ->limit($limit);
    }

}
