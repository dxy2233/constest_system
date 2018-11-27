<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;

use admin\services\AdminLogin;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BAdminRole;
use common\models\business\BAdminRule;
use common\models\business\BAdminUser;

/**
 * Site controller
 */
class ManagerController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    public function actionIndex()
    {
        $user = $this->user;
        $return['id'] = $user->id;
        $return['name'] = $user->name;
        $return['role_id'] = $user->role_id;
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionChangePassword()
    {
        $user = $this->user;
        $password = $this->pString('password');
        if (empty($password)) {
            return $this->respondJson(1, '原密码不能为空');
        }
        $new_password = $this->pString('new_password');
        if (empty($password)) {
            return $this->respondJson(1, '新密码不能为空');
        }
        $new_password_2 = $this->pString('new_password_2');
        if ($new_password_2 != $new_password) {
            return $this->respondJson(1, '两次密码输入不一致');
        }
        if (strlen($new_password_2)>18 || strlen($new_password)<6) {
            return $this->respondJson(1, '新密码长度必须大于5并小于18');
        }
        $res = AdminLogin::login($user->name, $password);
        if ($res === false) {
            return $this->respondJson(1, "用户名或密码错误！");
        }
        $user->password = md5($new_password_2 . $user->pwd_salt);
        if (!$user->save()) {
            return $this->respondJson(1, '修改失败', $user->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }
    // 添加管理员
    public function actionCreateAdmin()
    {
        $name = $this->pString('name');
        if (empty($name)) {
            return $this->respondJson(1, 'name不能为空');
        }
        $real_name = $this->pString('realName', '');
        $department = $this->pString('department', '');
        $mobile = $this->pString('mobile', '');
        $role_id = $this->pInt('roleId', 0);
        if (empty($role_id)) {
            return $this->respondJson(1, 'role_id不能为空');
        }
        $status = $this->pInt('status', 1);
        $admin_user = new BAdminUser();
        $admin_user->name = $name;
        $admin_user->real_name = $real_name;
        $admin_user->department = $department;
        
        $admin_user->pwd_salt = md5(NOW_TIME . $name);
        $admin_user->password = md5('123456' . $admin_user->pwd_salt);
        $admin_user->mobile = $mobile;
        $admin_user->role_id = $role_id;
        $admin_user->status = $status;
        if (!$admin_user->save()) {
            return $this->respondJson(1, '添加失败', $admin_user->getFirstErrorText());
        }
        return $this->respondJson(0, '添加成功');
    }
    //修改管理员
    public function actionEditAdmin()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'id不能为空');
        }
        $name = $this->pString('name');
        if (empty($name)) {
            return $this->respondJson(1, 'name不能为空');
        }
        $real_name = $this->pString('realName', '');
        $department = $this->pString('department', '');
        $mobile = $this->pString('mobile', '');
        $role_id = $this->pInt('roleId', 0);
        if (empty($role_id)) {
            return $this->respondJson(1, 'role_id不能为空');
        }
        $status = $this->pInt('status', 1);
        $admin_user = BAdminUser::find()->where(['id' => $id])->one();
        if (empty($admin_user)) {
            return $this->respondJson(1, '管理员不存在');
        }
        $admin_user->name = $name;
        $admin_user->real_name = $real_name;
        $admin_user->department = $department;
        
        $admin_user->mobile = $mobile;
        $admin_user->role_id = $role_id;
        $admin_user->status = $status;
        if (!$admin_user->save()) {
            return $this->respondJson(1, '修改失败', $admin_user->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }
    // 添加角色
    public function actionCreateRole()
    {
        $name = $this->pString('name');
        if (empty($name)) {
            return $this->respondJson(1, 'name不能为空');
        }

        $admin_user = new BAdminRole();
        $admin_user->name = $name;

        if (!$admin_user->save()) {
            return $this->respondJson(1, '添加失败', $admin_user->getFirstErrorText());
        }
        return $this->respondJson(0, '添加成功');
    }
    //修改角色
    public function actionEditRole()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'id不能为空');
        }
        $name = $this->pString('name');
        if (empty($name)) {
            return $this->respondJson(1, 'name不能为空');
        }

        $admin_role = BAdminRole::find()->where(['id' => $id])->one();
        if (empty($admin_role)) {
            return $this->respondJson(1, '角色不存在');
        }
        $admin_role->name = $name;

        if (!$admin_role->save()) {
            return $this->respondJson(1, '修改失败', $admin_role->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }

    // 删除角色
    public function actionDelRole()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'id不能为空');
        }
        if ($id < 3) {
            return $this->respondJson(1, '不能删除的角色');
        }
        $data = BAdminRole::find()->where(['id' => $id])->one();
        if (!$data) {
            return $this->respondJson(1, '角色不存在');
        }
        if (!$data->delete()) {
            return $this->respondJson(1, '删除失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '删除成功');
    }
    //管理员列表
    public function actionGetAdminList()
    {
        $search_name = $this->pString('searchName');
        $find = BAdminUser::find();
        if ($search_name != '') {
            $find->andWhere(['or',['likg', 'mobile', $search_name], ['like', 'real_name', $search_name]]);
        }
        $page = $this->pInt('page', 1);
        $find->page($page);
        $role = BAdminRole::find()->where(['status' => BAdminRole::STATUS_ON])->asArray()->all();
        $role_id = [];
        foreach ($role as $v) {
            $role_id[$v['id']] = $v['name'];
        }
        $count = $find->count();
        $data = $find->orderBy('role_id')->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['last_login_time'] = date('Y-m-d H:i:s', $v['last_login_time']);
            $v['role_name'] = $role_id[$v['role_id']];
            $v['status'] = BAdminUser::getStatus($v['status']);
        }
        $return = [ 'count' => $count, 'data' => $data];
        return  $this->respondJson(0, '获取成功', $return);
    }
    // 获取角色列表
    public function actionGetRoleList()
    {
        $data = BAdminRole::find()->where(['status' => BAdminRole::STATUS_ON])->asArray()->all();
        return  $this->respondJson(0, '获取成功', $data);
    }

    // 停用管理员
    public function actionAdminOff()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $admin_user = BAdminUser::find()->where(['id' => $id])->one();
        if (empty($admin_user)) {
            return $this->respondJson(1, '管理员不存在');
        }
        $admin_user->status = BAdminUser::STATUS_OFF;
        if (!$admin_user->save()) {
            return $this->respondJson(1, '修改失败', $admin_user->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }

    // 启用管理员
    public function actionAdminOn()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $admin_user = BAdminUser::find()->where(['id' => $id])->one();
        if (empty($admin_user)) {
            return $this->respondJson(1, '管理员不存在');
        }
        $admin_user->status = BAdminUser::STATUS_ON;
        if (!$admin_user->save()) {
            return $this->respondJson(1, '修改失败', $admin_user->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }

    // 获取单个角色的权限
    public function actionGetRoleRuleList()
    {
        // 所有权限
        $rule = BAdminRule::find()->orderBy('parent_id')->asArray()->all();
        $new_data = [];
        foreach ($rule as $v) {
            if ($v['parent_id'] == 0) {
                $new_data[$v['id']] = $v;
                $new_data[$v['id']]['child'] = [];
            } else {
                $new_data[$v['parent_id']]['child'][$v['id']] = $v;
            }
        }
        // 当前用户权限
        $role_id = $this->pInt('roleId');
        if (empty($role_id)) {
            return $this->respondJson(1, 'id不能为空');
        }
        $data = BAdminRole::find()->where(['id' => $role_id])->one();
        if (!$data) {
            return $this->respondJson(1, '角色不存在');
        }
        if ($data->rule_list == null) {
            $this_rule = [];
        } else {
            $this_rule = json_decode($data->rule_list, true);
        }
        $r = [];
        foreach ($new_data as $v) {
            if (!empty($v['child'])) {
                $it = [];
                foreach ($v['child'] as $val) {
                    if (in_array($val['id'], $this_rule)) {
                        $val['is_have'] = 1;
                    } else {
                        $val['is_have'] = 0;
                    }
                    $it[]=$val;
                }
                $v['child'] = $it;
            }
            if (in_array($v['id'], $this_rule)) {
                $v['is_have'] = 1;
            } else {
                $v['is_have'] = 0;
            }
            $r[] = $v;
        }

        return  $this->respondJson(0, '获取成功', $r);
    }
    // 权限编辑
    public function actionSetRoleRule()
    {
        $role_id = $this->pInt('roleId');
        if (empty($role_id)) {
            return $this->respondJson(1, 'id不能为空');
        }
        $data = BAdminRole::find()->where(['id' => $role_id])->one();
        if (!$data) {
            return $this->respondJson(1, '角色不存在');
        }
        $rule_list = $this->pString('ruleList');
        $rule_arr = json_decode($rule_list, true);
        $data->rule_list = json_encode($rule_arr);
        if (!$data->save()) {
            return $this->respondJson(1, '修改失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }
}
