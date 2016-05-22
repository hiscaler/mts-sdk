<?php

namespace yadjet\mts\sdk;

use Yii;

/**
 * 幻灯片数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class SlideGetter extends DataGetter implements DataGetterInterface
{

    use DataGetterTrait;

    public static function parseQuery($fields = null, $where = array(), $orderBy = 'ordering.asc', $offset = 0, $limit = 10)
    {
        if (empty($fields)) {
            $fields = 'title,url,url_open_target,description';
        }

        $condition = [
            'tenant_id' => self::getTenantId()
        ];
        if ($where) {
            if (isset($where['group'])) {
                $condition['group_id'] = strpos($where['group'], ',') === false ? (int) $where['group'] : explode(',', $where['group']);
            }
        }

        return (new \yii\db\Query())
                ->select($fields)
                ->from('{{%slide}}')
                ->where($condition)
                ->orderBy(self::parseOrderBy($orderBy))
                ->offset($offset)
                ->limit($limit);
    }

}
