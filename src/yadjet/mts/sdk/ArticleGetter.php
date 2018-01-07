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

    private static $_fields = ['alias', 'title', 'keywords', 'tags', 'keywords', 'description', 'picture_path', 'content'];

    public static function one($alias, $fields = '*')
    {
        if ($fields == '*') {
            $fields = static::$_fields;
        } else {
            $_fields = static::$_fields;
            $fields = array_filter(explode(',', $fields), function ($field) use ($_fields) {
                return in_array($field, self::$_fileds);
            });
            $fields = $fields ?: $_fields;
        }

        return (new Query())->select($fields)
            ->from('{{%article}}')
            ->where([
                'tenant_id' => self::getTenantId(),
                'alias' => trim($alias),
                'enabled' => self::BOOLEAN_TRUE
            ])
            ->one();
    }

    /**
     * 查询文章个具体列的值
     *
     * @param $alias
     * @param $columnName
     * @return false|null|string
     * @throws \yii\db\Exception
     */
    public static function scalar($alias, $columnName)
    {
        $value = null;
        if ($columnName = trim($columnName)) {
            if (in_array($columnName, static::$_fields)) {
                $value = \Yii::$app->getDb()->createCommand("SELECT [[$columnName]] FROM {{%article}} WHERE [[alias]] = :alias", [':alias' => $alias])->queryScalar();
            }
        }

        return $value;
    }

}
