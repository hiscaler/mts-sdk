<?php

namespace yadjet\mts\sdk;

/**
 * 幻灯片数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class NodeGetter extends DataGetter
{

    private static function toTree($arr, $keyNodeId, $keyParentId = 'parent_id', $keyChildren = 'children', &$refs = null)
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
     * 获取节点名称
     * @param integer $id 节点编号
     * @return string|boolean
     */
    public static function name($id)
    {
        return (new \yii\db\Query())->select('name')->from('{{%node}}')->where(['id' => (int) $id])->scalar();
    }

}
