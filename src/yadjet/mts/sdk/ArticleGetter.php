<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

/**
 * 单文章数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class ArticleGetter extends DataGetter
{

    public static function one($alias)
    {
        return (new Query())->select(['alias', 'title', 'keywords', 'tags', 'keywords', 'description', 'picture_path', 'content'])
                ->from('{{%article}}')
                ->where([
                    'tenant_id' => self::getTenantId(),
                    'alias' => trim($alias),
                    'enabled' => self::BOOLEAN_TRUE
                ])
                ->one();
    }

}
