<?php

namespace yadjet\mts\sdk;

/**
 * 助手类
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class ApplicationHelper
{

    /**
     * 翻页信息
     * @param array $meta
     * @return string
     */
    public static function pagination($meta)
    {
        $output = null;
        if (is_array($meta) && isset($meta['totalCount']) && isset($meta['perPage'])) {
            $pagination = new \yii\data\Pagination([
                'totalCount' => $meta['totalCount'],
                'defaultPageSize' => $meta['perPage'],
            ]);
            $output = \yii\widgets\LinkPager::widget([
                    'pagination' => $pagination
            ]);
        }

        return $output;
    }

}
