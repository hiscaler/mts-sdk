<?php

namespace yadjet\mts\sdk;

use Yii;

class ApplicationGetter extends DataGetter
{

    /**
     * 获取节点对应的视图文件名称
     * @param string $id 节点名称
     * @param string $name 视图名称
     * @param string $defaultViewFile 默认的视图文件
     * @return string
     */
    public static function viewFile($id, $name, $defaultViewFile = null)
    {
        $viewFile = null;
        $parameters = Yii::$app->getDb()->createCommand('SELECT [[parameters]] FROM {{%node}} WHERE [[id]] = :id AND [[tenant_id]] = :tenantId')->bindValues([
                ':id' => (int) $id,
                ':tenantId' => self::getTenantId()
            ])->queryScalar();
        if (!empty($parameters)) {
            foreach (explode("\r\n", $parameters) as $paramater) {
                if (substr($paramater, 0, 1) == $name) {
                    $params = explode('~', $paramater);
                    $viewFile = isset($params[2]) ? $params[2] : null;
                    break;
                }
            }
        }

        return !empty($viewFile) ? $viewFile : $defaultViewFile;
    }

    /**
     * 获取 URL 规则
     * @param array $rejectIds 不显示的节点
     * @return string
     */
    public static function urlRules($rejectIds = [])
    {
        $rules = [];
        $sql = 'SELECT [[id]], [[alias]], [[parameters]] FROM {{%node}} WHERE [[tenant_id]] = :tenantId';
        if ($rejectIds) {
            $sql .= ' AND [[id]] NOT IN (' . implode(',', $rejectIds) . ')';
        }
        $nodes = Yii::$app->getDb()->createCommand($sql)->bindValue(':tenantId', self::getTenantId(), \PDO::PARAM_INT)->queryAll();

        foreach ($nodes as $node) {
            foreach (explode("\r\n", $node['parameters']) as $paramater) {
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
