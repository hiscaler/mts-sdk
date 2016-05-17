<?php

namespace yadjet\mts\sdk;

class ApplicationGetter extends DataGetter
{

    public static function viewFile($id, $name, $defaultViewFile = null)
    {
        $viewFile = null;
        $paramaters = Yii::$app->getDb()->createCommand('SELECT [[paramaters]] FROM {{%node}} WHERE [[id]] = :id AND [[tenant_id]] = :tenantId')->bindValues([
                ':id' => (int) $id,
                ':tenantId' => self::getConstantValue('TENANT_ID')
            ])->queryScalar();
        if (!empty($paramaters)) {
            foreach (explode("\r\n", $paramaters) as $paramater) {
                if (substr($paramater, 0, 1) == $name) {
                    $params = explode('~', $paramater);
                    $viewFile = isset($params[2]) ? $params[2] : null;
                    break;
                }
            }
        }

        return !empty($viewFile) ? $viewFile : $defaultViewFile;
    }

    public static function urlRules($rejectIds = [])
    {
        $rules = [];
        $sql = 'SELECT [[id]], [[alias]], [[paramaters]] FROM {{%node}} WHERE [[tenant_id]] = :tenantId';
        if ($rejectIds) {
            $sql .= ' AND [[id]] NOT IN (' . implode(',', $rejectIds) . ')';
        }
        $nodes = Yii::$app->getDb()->createCommand($sql)->bindValue(':tenantId', self::getConstantValue('TENANT_ID'), PDO::PARAM_INT)->queryAll();

        foreach ($nodes as $node) {
            foreach (explode("\r\n", $node['paramaters']) as $paramater) {
                if (!empty($paramater)) {
                    // Example: i:news/index~~/news/index.fashion.twig
                    $params = explode('~', substr($paramater, 2));
                    if (!empty($params[0])) {
                        $rule = [
                            'pattern' => trim($node['alias'], '/') . (isset($params[1]) && !empty($params[1]) ? ('/' . trim($params[1], '/')) : ''),
                            'route' => $params[0],
                            'defaults' => [
                                'node' => $node['id']
                            ],
                        ];
                        if (isset($params[3]) && substr(trim($params[3]), 0, 1) == '.') {
                            $rule['suffix'] = $params[3];
                        } else {
                            $rule['suffix'] = '/';
                        }
                        $rules[] = $rule;
                    }
                }
            }
        }

        return $rules;
    }

}
