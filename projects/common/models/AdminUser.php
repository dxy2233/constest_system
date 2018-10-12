<?php

namespace common\models;

use common\dzbase\DzModel;
use yii\web\IdentityInterface;
use common\models\business\BUserAccessToken;

/**
 * This is the model class for table "{{%admin_user}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property string $password
 * @property string $pwd_salt
 * @property integer $status
 * @property integer $create_time
 * @property integer $last_login_time
 * @property string $allow_city;
 * @property string $allow_ip;
 * @property string $roles;

 */
class AdminUser extends DzModel implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'password', 'pwd_salt'], 'required'],
            [['status', 'create_time', 'last_login_time'], 'integer'],
            [['name', 'password'], 'string', 'max' => 45, 'min' => 5],
            [['mobile'], 'string', 'max' => 15],
            [['allow_city','allow_ip'], 'string', 'max' => 1000],
            [['name', 'mobile'], 'unique'],
            [['pwd_salt'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => '用户名',
            'mobile'        => '手机号码',
            'real_name'        => '姓名',
            'email'        => '邮箱',
            'department'        => '部门',
            'password'      => '密码',
            'status' => '状态',
            'create_time'    => '创建时间',
            'last_login_time' => '上次登陆',
            'allow_city' => '允许登陆城市',
            'allow_ip' => '允许登陆IP',
            'roles' => '角色'
        ];
    }


    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return md5($this->id, $this->password);
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $accessToken = BUserAccessToken::find()
        ->where(['access_token' => $token,'client_id' => \Yii::$app->controller->module->id])
        ->andWhere(['>=', 'expire_time', NOW_TIME])
        ->one();
        return !empty($accessToken['user_id'])
            ? static::findIdentity($accessToken['user_id'])
            : null;
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(AclUserRole::className(), ['user_id'=>'id']);
    }
}
