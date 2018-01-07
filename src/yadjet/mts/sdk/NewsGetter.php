<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

/**
 * 资讯数据拉取
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class NewsGetter extends DataGetter implements DataGetterInterface
{

    use DataGetterTrait;

    public static function parseQuery($fields = null, $where = array(), $orderBy = 'ordering.asc', $offset = 0, $limit = 10)
    {
        if (empty($fields)) {
            $fields = 'title,keywords,description,content,clicks_count,ordering,published_at';
        }
        $getContent = false;
        foreach ($fields as &$field) {
            if ($fields == 'content') {
                $getContent = true;
                $prefix = 'n';
            } else {
                $prefix = 't';
            }
            $field = "$prefix.$field";
        }

        $condition = [
            't.tenant_id' => self::getTenantId()
        ];

        $query = (new Query())
            ->select($fields)
            ->from('{{%news}} t')
            ->where($condition)
            ->orderBy(self::parseOrderBy($orderBy))
            ->offset($offset)
            ->limit($limit);
        if ($getContent) {
            $query->leftJoin('{{%news_content}} n', '[[t.id]] = [[n.news_id]]');
        }

        return $query;
    }

}
