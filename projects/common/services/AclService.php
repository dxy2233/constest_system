<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/31
 * Time: 上午9:49
 */

namespace common\services;


use common\models\AclAction;
use common\models\AclRole;
use common\models\AclUserRole;
use yii\helpers\ArrayHelper;

class AclService extends ServiceBase {

    public static function getInstance() : AclService {
        return parent::getServiceInstance();
    }


    /**
     * @param $userId
     * @param $fileUrl
     * @return array|bool
     * info : 是否拥有控制器操作权限判断(增、删、改、显示)
     */
    public function checkViewOptionPermission($userId, $fileUrl)
    {
        if (empty($userId) || empty($fileUrl)) {
            return false;
        }

        $arr = [];
        $actionStr = $this->getUserAdminAllPermission($userId);
        // 拥有所有权限
        if ($actionStr === '*') {
            return ['create' => true, 'update' => true, 'delete' => true, 'view' => true];
        }

        // 拥有控制器权限
        if (stripos($actionStr, $fileUrl . '/create') !== false) {
            $arr['create'] = true;
        } else {
            $arr['create'] = false;
        }
        if (stripos($actionStr, $fileUrl . '/delete') !== false) {
            $arr['delete'] = true;
        } else {
            $arr['delete'] = false;
        }
        if (stripos($actionStr, $fileUrl . '/update') !== false) {
            $arr['update'] = true;
        } else {
            $arr['update'] = false;
        }
        if (stripos($actionStr, $fileUrl . '/view') !== false) {
            $arr['view'] = true;
        } else {
            $arr['view'] = false;
        }

        return $arr;
    }


    /**
     * @param $userId
     * @return null|string
     * info : 获取管理员所有权限（具体到能够访问的action，例:welcome/index）
     */
    public function getUserAdminAllPermission($userId)
    {
        // 获取该用户的所有角色、子角色
        $roleArr = AclUserRole::find()->where(['user_id' => $userId])->select('role_id')->asArray()->all();
        if (empty($roleArr)) {
            return null;
        }
        // 取列
        $roleIdColumn = ArrayHelper::getColumn($roleArr, 'role_id');

        // 获取角色对应的action_id
        $actionArr = AclRole::find()->where(['in', 'id', $roleIdColumn])->select('actions')->asArray()->all();
        if (empty($actionArr)) {
            return null;
        }
        $actionIdColumn = ArrayHelper::getColumn($actionArr, 'actions');
        if (count($actionIdColumn) > 1) {
            $actionIdColumn = implode(',', $actionIdColumn);
        }
        $actionIdColumnArr = explode(',', $actionIdColumn[0]);

        // 获取id对应的action
        $actionDetailArr = AclAction::find()->where(['in', 'id', $actionIdColumnArr])->select('detail')->asArray()->all();
        if (empty($actionDetailArr)) {
            return null;
        }
        $actionDetailColumn = ArrayHelper::getColumn($actionDetailArr, 'detail');

        // 将数组转换为字符串
        $actionStr = implode(' ', $actionDetailColumn);
        $defaultActionStr = implode(' ', \Yii::$app->params['defaultMenus']);
        $actionStr .= ' ' . $defaultActionStr;

        // 找到*，所有权限
        if (stripos($actionStr, '*') !== false) {
            return '*';
        }

        return $actionStr;
    }


    public function updateUserRoles($userId, $roleIds) {

        //首先删除用户的已有角色
        self::executeSql('delete from ' . AclUserRole::tableName() . ' where user_id = ' . $userId);

        //增加用户的角色
        foreach ($roleIds as $roleId) {
            $userRole = new AclUserRole();
            $userRole->user_id = $userId;
            $userRole->role_id = $roleId;
            $userRole->insert();
        }
    }


    public function getUserRoles($userId) {
        return AclUserRole::findAll(['user_id' => $userId]);
    }


    /**
     * 检查用户权限
     * @param $userId
     * @param $action
     * @return boolean
     */
    public function hasPermission($userId, $action) {
        $userRoles = AclUserRole::findAll(['user_id' => $userId]);

        if ($userRoles) {
            foreach ($userRoles as $userRole) {
                $hasPermission = $this->checkRolePermission($userRole->role_id, $action);
                if ($hasPermission) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 检查某个角色是否对当前action有访问权限
     * @param $roleId
     * @param $requestedAction
     * @return bool
     */
    private function checkRolePermission($roleId, $requestedAction) {

        $role = AclRole::findOne($roleId);
        if (!$role) {
            return false;
        }


        //判断角色的直接动作权限
        $actions = $this->getRoleActions($role->id);


        foreach ($actions as $action) {

            $actionItems = explode(" ", $action->detail);
            if (is_array($actionItems)) {
                foreach ($actionItems as $actionItem) {
                    $actionDetail = explode('/', $actionItem);
                    $requestedActionDetail = explode('/', $requestedAction);

                    $matched = true;
                    for ($i = 0; $i < count($actionDetail); $i++) {
                        if ($actionDetail[$i] == '*') {
                            break;
                        }

                        if ($actionDetail[$i] != ($requestedActionDetail[$i] ?? '')) {
                            $matched = false;
                            break;
                        }
                    }

                    if ($matched) {
                        return true;
                    }
                }
            }
        }

        //判断角色的子角色权限
        $subRoles = $role->roles;
        if (!$subRoles) {
            return false;
        }

        $subRoleIds = explode(' ', $subRoles);
        foreach ($subRoleIds as $subRoleId) {
            $subRole = AclRole::findOne($subRoleId);
            if (!$subRole) {
                continue;
            }

            $hasPermisson = $this->checkRolePermission($subRoleId, $requestedAction);
            if ($hasPermisson) {
                return true;
            }
        }

        return false;
    }

    public function getRoleActions($roleId) {

        $role = AclRole::findOne($roleId);
        if (empty($role->actions)) {
            return [];
        }

        $actionIds = explode(',', $role->actions);

        return AclAction::findAll($actionIds);

    }

    public function getRoleSubroles($roleId) {

        $role = AclRole::findOne($roleId);
        if (empty($role->roles)) {
            return array();
        }

        $roleIds = explode(' ', $role->roles);

        return AclRole::findAll($roleIds);

    }

}