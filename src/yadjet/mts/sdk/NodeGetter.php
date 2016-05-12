<?php

namespace yadjet\mts\sdk;

use Yii;

/**
 * 幻灯片数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class NodeGetter extends DataGetter implements DataGetterInterface
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

}
