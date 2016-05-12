<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

class ArchiveGetter extends DataGetter implements DataGetterInterface
{

    public static function parseQuery($fields = '*', $where = [], $orderBy = 'ordering.asc', $offset = 0, $limit = 10)
    {
        $condition = [
            't.enabled' => self::BOOLEAN_TRUE,
        ];
        if (isset($where['node'])) {
            $node = $where['node'];
            if (is_string($node) && strpos($node, ',') !== false) {
                $node = explode(',', $node);
            }
            if (isset($where['children']) && !is_array($node)) {
                $ids = \app\models\Node::getChildrenIds($node);
                array_unshift($ids, $node);
            }
            $condition['t.node_id'] = is_array($node) ? $node : (int) $node;
        }
        // 推送位
        if (isset($where['label'])) {
            $label = trim($where['label']);
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
                $condition = ['AND', $condition, ['EXISTS', $subQuery]];
            } else {
                $condition[] = '0 = 1';
            }
        }

        if (isset($where['hasThumbnail'])) {
            if ($where['hasThumbnail']) {
                $condition['t.has_thumbnail'] = self::BOOLEAN_TRUE;
            }
        }

        $query = (new Query())->select($fields)
            ->from('{{%archive}} t')
            ->where($condition)
            ->offset($offset)
            ->limit($limit)
            ->orderBy(self::parseOrderBy($orderBy));

        return $query;
    }

    public static function one($id, $fields = '*', $expand = 'content')
    {
        $query = (new Query())->select(self::fixQueryFields($fields))
            ->from('{{%archive}} t')
            ->where(['t.id' => (int) $id]);
        if ($expand) {
            $expand = explode(',', $expand);
            foreach ($expand as $name) {
                switch ($name) {
                    case 'content':
                        $query->leftJoin('{{%archive_content}} ac', '[[t.id]] = [[ac.archive_id]]');
                        $query->addSelect(['ac.content']);
                        break;

                    default:
                        break;
                }
            }
        }

        return $query->one();
    }

}
