<?php

namespace yadjet\mts\sdk;

use Yii;

/**
 * 广告数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class AdGetter extends DataGetter
{

    const TYPE_PICTURE = 0;
    const TYPE_FLASH = 1;
    const TYPE_TEXT = 2;

    public static function rows($alias)
    {
        $db = Yii::$app->getDb();
        $space = $db->createCommand('SELECT [[id]], [[width]], [[height]] FROM {{%ad_space}} WHERE [[tenant_id]] = :tenantId AND [[status]] = :status AND [[enabled]] = :enabled AND [[alias]] = :alias')->bindValues([
                ':tenantId' => self::getConstantValue('TENANT_ID'),
                ':status' => self::BOOLEAN_TRUE,
                ':enabled' => self::BOOLEAN_TRUE,
                ':alias' => strtolower(trim($alias))
            ])->queryOne();
        if ($space === false) {
            return null;
        }
        $ads = $db->createCommand('SELECT [[id]], [[type]], [[url]], [[file_path]], [[text]], [[end_datetime]], [[message]] FROM {{%ad}} WHERE [[space_id]] = :spaceId AND [[status]] = :status AND [[enabled]] = :enabled AND [[begin_datetime]] <= :now ORDER BY [[end_datetime]] DESC')->bindValues([
                ':spaceId' => $space['id'],
                ':status' => self::BOOLEAN_TRUE,
                ':enabled' => self::BOOLEAN_TRUE,
                ':now' => time()
            ])->queryAll();
        if (!$ads) {
            return [];
        } else {
            $rows = [];
            foreach ($ads as $ad) {

                if ($ad['end_datetime'] < time()) {
                    $output = $ad['message'];
                } else {
                    $db->createCommand('UPDATE {{%ad}} SET [[hits_count]] = [[hits_count]] + 1, [[views_count]] = [[views_count]] + 1 WHERE [[id]] = :id')->bindValue(':id', $ad['id'], PDO::PARAM_INT)->execute();
                    switch ($ad['type']) {
                        case static::TYPE_PICTURE:
                            $output = '<img src="' . $ad['file_path'] . '" width="' . $space['width'] . '" height="' . $space['height'] . '">';
                            break;
                        case static::TYPE_TEXT:
                            $output = $ad['text'];
                            break;
                        default:
                            $output = null;
                            break;
                    }
                    if (!empty($ad['url'])) {
                        $output = '<a href="' . $ad['url'] . '" target="_balnk">' . $output . '</a>';
                    }
                }
                $rows[] = $output;
            }

            return $rows;
        }
    }

    public static function one($alias)
    {
        $db = Yii::$app->getDb();
        $space = $db->createCommand('SELECT [[id]], [[width]], [[height]] FROM {{%ad_space}} WHERE [[tenant_id]] = :tenantId AND [[status]] = :status AND [[enabled]] = :enabled AND [[alias]] = :alias')->bindValues([
                ':tenantId' => self::getConstantValue('TENANT_ID'),
                ':status' => self::BOOLEAN_TRUE,
                ':enabled' => self::BOOLEAN_TRUE,
                ':alias' => strtolower(trim($alias))
            ])->queryOne();
        if ($space === false) {
            return null;
        }
        $ad = $db->createCommand('SELECT [[id]], [[type]], [[url]], [[file_path]], [[text]], [[end_datetime]], [[message]] FROM {{%ad}} WHERE [[space_id]] = :spaceId AND [[status]] = :status AND [[enabled]] = :enabled AND [[begin_datetime]] <= :now ORDER BY [[end_datetime]] DESC')->bindValues([
                ':spaceId' => $space['id'],
                ':status' => self::BOOLEAN_TRUE,
                ':enabled' => self::BOOLEAN_TRUE,
                ':now' => time()
            ])->queryOne();
        if ($ad === false) {
            return null;
        } else {
            if ($ad['end_datetime'] < time()) {
                $output = $ad['message'];
            } else {
                $db->createCommand('UPDATE {{%ad}} SET [[hits_count]] = [[hits_count]] + 1, [[views_count]] = [[views_count]] + 1 WHERE [[id]] = :id')->bindValue(':id', $ad['id'], PDO::PARAM_INT)->execute();
                switch ($ad['type']) {
                    case static::TYPE_PICTURE:
                        $output = '<img src="' . $ad['file_path'] . '" width="' . $space['width'] . '" height="' . $space['height'] . '">';
                        break;
                    case static::TYPE_TEXT:
                        $output = $ad['text'];
                        break;
                    default:
                        $output = null;
                        break;
                }
                if (!empty($ad['url'])) {
                    $output = '<a href="' . $ad['url'] . '" target="_balnk">' . $output . '</a>';
                }
            }

            return $output;
        }
    }

}
