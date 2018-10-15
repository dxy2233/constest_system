<?php

namespace common\models;

use Yii;
use common\dzbase\DzModel;
use yii\web\IdentityInterface;
use common\models\business\BUserAccessToken;

/**
 * This is the model class for table "gr_user".
 *
 * @property int $id 用户ID
 * @property string $username 用户名
 * @property string $nickname 用户昵称
 * @property string $realname 真实名
 * @property string $pwd_salt 密码salt
 * @property string $password 密码
 * @property string $mobile 手机
 * @property string $email 邮箱
 * @property string $trans_password 交易密码
 * @property int $is_identified 是否实名认证，0否，1是
 * @property int $status 状态，0 冻结，1 正常
 * @property string $last_login_ip 最后登录IP
 * @property int $last_login_time 最后登录时间
 * @property string $equipment_number 设备号
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class User extends DzModel implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mobile'], 'required'],
            [['is_identified', 'status', 'last_login_time', 'create_time', 'update_time'], 'integer'],
            [['username', 'nickname', 'password', 'email'], 'string', 'max' => 50],
            [['realname', 'mobile', 'last_login_ip'], 'string', 'max' => 20],
            [['pwd_salt'], 'string', 'max' => 32],
            [['equipment_number', 'trans_password'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '用户ID'),
            'username' => Yii::t('app', '用户名'),
            'nickname' => Yii::t('app', '用户昵称'),
            'realname' => Yii::t('app', '真实名'),
            'pwd_salt' => Yii::t('app', '密码salt'),
            'password' => Yii::t('app', '密码'),
            'mobile' => Yii::t('app', '手机'),
            'email' => Yii::t('app', '邮箱'),
            'trans_password' => Yii::t('app', '交易密码'),
            'is_identified' => Yii::t('app', '是否实名认证，0否，1是'),
            'status' => Yii::t('app', '状态，0 冻结，1 正常'),
            'last_login_ip' => Yii::t('app', '最后登录IP'),
            'last_login_time' => Yii::t('app', '最后登录时间'),
            'equipment_number' => Yii::t('app', '设备号'),
            'create_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '修改时间'),
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
        return static::findOne(['id' => $id, 'status' => static::STATUS_ACTIVE]);
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
}
