<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

class ArchiveGetter implements DataGetterInterface
{

    use GetterTrait;

    public static function parseQuery($params = [])
    {
        $rawCondition = $params['condition'];
        $where = [
            't.enabled' => self::BOOLEAN_TRUE,
        ];
        if (isset($rawCondition['node'])) {
            $node = $rawCondition['node'];
            if (is_string($node) && strpos($node, ',') !== false) {
                $node = explode(',', $node);
            }
            if (isset($rawCondition['children']) && !is_array($node)) {
                $ids = \app\models\Node::getChildrenIds($node);
                array_unshift($ids, $node);
            }
            $where['t.node_id'] = is_array($node) ? $node : (int) $node;
        }
        // 推送位
        if (isset($rawCondition['label'])) {
            $label = trim($rawCondition['label']);
            $labelIds = (new Query())->select('id')
                ->from('{{%label}}')
                ->where(['alias' => strpos($label, ',') === false ? $label : explode(',', $label)])
                ->column();
            if ($labelIds) {
                $subQuery = (new Query())
                    ->select('archive_id')
                    ->from('{{%archive_label}}')
                    ->where('[[t.id]] = [[archive_id]] AND [[model_name]] = :modelName', [':modelName' => 'app-models-Archive'])
                    ->andWhere(['in', 'lable_id', $labelIds])
                    ->groupBy('archive_id')
                    ->having('COUNT(*) = ' . count($labelIds));
                $where = ['AND', $where, ['EXISTS', $subQuery]];
            } else {
                $where[] = '0 = 1';
            }
        }

        if (isset($rawCondition['hasThumbnail'])) {
            if ($rawCondition['hasThumbnail']) {
                $where['t.has_thumbnail'] = self::BOOLEAN_TRUE;
            }
        }
        $params['condition'] = $where;

        $query = (new Query())->select($params['fields'])
            ->from('{{%archive}} t')
            ->where($params['condition'])
            ->offset($params['offset'])
            ->limit($params['limit'])
            ->orderBy(self::parseOrderBy($params['orderBy']));

        return $query;
    }

    public static function all($params = [])
    {
        $params = static::parseParams($params, self::RETURN_ALL);
        $query = self::parseQuery($params);

        return self::toAll($query, $params);
    }

    public static function rows($params = [])
    {
        $params = static::parseParams($params, self::RETURN_ROWS);

        return self::parseQuery($params)->all();
    }

    public static function one($params = [])
    {
        $params = static::fixParams($params, self::RETURN_ROWS);
        return (new Query())->select($params['fields'])
                ->from('{{%archive}}')
                ->where($params['condition'])
                ->one();
    }

}
