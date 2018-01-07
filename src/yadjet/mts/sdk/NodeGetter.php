<?php

namespace yadjet\mts\sdk;

use Yii;
use yii\db\Query;

/**
 * 幻灯片数据拉取
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class NodeGetter extends DataGetter
{

    private static function _toTree($arr, $keyNodeId, $keyParentId = 'parent_id', $keyChildren = 'children', &$refs = null)
    {
        $refs = [];
        foreach ($arr as $offset => $row) {
            $arr[$offset][$keyChildren] = [];
            $refs[$row[$keyNodeId]] = &$arr[$offset];
        }

        $tree = [];
        foreach ($arr as $offset => $row) {
            $parent_id = $row[$keyParentId];
            if ($parent_id) {
                if (!isset($refs[$parent_id])) {
                    $tree[] = &$arr[$offset];
                    continue;
                }
                $parent = &$refs[$parent_id];
                $parent[$keyChildren][] = &$arr[$offset];
            } else {
                $tree[] = &$arr[$offset];
            }
        }

        return $tree;
    }

    /**
     * 节点树
     *
     * @return array
     */
    public static function tree()
    {
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]], [[name]], [[parent_id]], [[level]] FROM {{%node}} WHERE [[tenant_id]] = :tenantId')->bindValue(':tenantId', self::getTenantId(), \PDO::PARAM_INT)->queryAll();

        return [
            'items' => static::_toTree($rawData, 'id', 'parent_id')
        ];
    }

    /**
     * 返回查询的节点数据（不带分页）
     *
     * @param string $parentId
     * @param string $rejectId
     * @param string $id
     * @param string $enabled
     * @param integer $offset
     * @param mixed $limit
     * @return array
     */
    public static function rows($parentId = null, $rejectId = null, $id = null, $enabled = 'y', $offset = 0, $limit = 10)
    {
        $where = ['tenant_id' => self::getTenantId()];
        if (!empty($enabled)) {
            $where['enabled'] = strtolower(trim($enabled)) == 'y' ? Option::BOOLEAN_TRUE : Option::BOOLEAN_FALSE;
        }
        $query = (new Query())
            ->select(['id', 'alias', 'name', 'parent_id', 'level', 'parent_ids', 'parent_names', 'enabled'])
            ->from('{{%node}}')
            ->where($where);
        if (!empty($parentId)) {
            $query->andWhere(['parent_id' => (int) $parentId]);
            $rejectId = $this->cleanIntegerNumbers($rejectId);
            if ($rejectId) {
                $query->andWhere(['NOT IN', 'id', $rejectId]);
            }
        } else {
            $query->andWhere(['id' => $this->cleanIntegerNumbers($id)]);
        }

        $query->offset($offset ?: 0);
        if ((int) $limit) {
            $query->limit((int) $limit);
        }

        return [
            'items' => $this->adjustFieldNames($query->all())
        ];
    }

    public static function name($id)
    {
        return (new Query())->select('name')->from('{{%node}}')->where(['id' => (int) $id])->scalar();
    }

    /**
     * 获取子节点
     *
     * @param integer $parentId
     * @param string $rejectId
     * @param string $enabled
     * @return array
     */
    public static function children($parentId, $rejectId = null, $enabled = 'y')
    {
        $sql = 'SELECT [[t.id]], [[t.alias]], [[t.name]], [[t.parent_id]] AS parentId, t.level, [[t.parent_ids]] AS [[parentIds]], [[t.parent_names]] AS parentNames FROM {{%node}} t JOIN {{%node_closure}} c ON [[t.id]] = [[c.child_id]] WHERE [[t.tenant_id]] = :tenantId AND [[c.parent_id]] = :parentId AND [[c.child_id]] <> :childId';
        $bindValues = [
            ':tenantId' => self::getTenantId(),
            ':parentId' => (int) $parentId,
            ':childId' => (int) $parentId
        ];
        if (!empty($enabled)) {
            $sql .= ' AND [[t.enabled]] = :enabled';
            $bindValues[':enabled'] = strtolower(trim($enabled)) == 'y' ? self::BOOLEAN_TRUE : self::BOOLEAN_FALSE;
        }
//        $rejectId = $this->cleanIntegerNumbers($rejectId);
        if ($rejectId) {
            $count = count($rejectId);
            if ($count == 1) {
                $sql .= ' AND [[t.id]] <> ' . (int) $rejectId[0];
            } elseif ($count > 1) {
                $sql .= ' AND [[t.id]] NOT IN (' . implode(', ', $rejectId) . ')';
            }
        }
        $sql .= ' ORDER BY [[ordering]] ASC';
        $children = Yii::$app->getDb()->createCommand($sql)->bindValues($bindValues)->queryAll();

        return $children;
    }

    /**
     * 获取子节点编号
     *
     * @param integer $parentId
     * @return array
     */
    public function actionChildrenIds($parentId)
    {
        $childrenIds = Yii::$app->getDb()->createCommand('SELECT [[child_id]] FROM {{%node_closure}} WHERE [[parent_id]] = :parentId AND [[child_id]] IN (SELECT [[id]] FROM {{%node}} WHERE [[tenant_id]] = :tenantId) AND [[child_id]] <> :childId')->bindValues([
            ':tenantId' => $this->tenantId,
            ':parentId' => (int) $parentId,
            ':childId' => (int) $parentId
        ])->queryColumn();

        return [
            'items' => $childrenIds,
        ];
    }

}
