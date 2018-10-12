<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%Records}}".
 *
 * @property int $id
 * @property string $name 用户名
 * @property string $name_pin 姓名拼音
 * @property string $mobile 手机号码
 * @property string $urgent_name 紧急联系人
 * @property string $urgent_tel 紧急联系人方式
 * @property int $sex 性别
 * @property string $idcard 身份证号
 * @property string $shop_name 店铺名
 * @property int $is_passport 有无护照
 * @property string $photo 身份证正面照
 * @property string $created_at 添加时间
 * @property string $updated_at 更新时间
 */
class Record extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%records}}';
    }

    public $filePhotos;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ]
        ];
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'name_pin', 'shop_name', 'mobile', 'urgent_name', 'urgent_tel', 'idcard', 'is_passport', 'photo','photo2','my_identity','wallet_number','wallet_address'], 'required', 'message' => '{attribute}不能为空'],
            [['sex', 'is_passport'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['photo','photo2'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, jpeg', 'maxFiles' => 4, 'maxSize' => 5*1024*1024],
            // [['name', 'name_pin', 'urgent_name'], 'string', 'min' => 2, 'max' => 50, 'message' => '{attribute}长度错误'],
            // [['mobile', 'urgent_tel'], 'string', 'min' => 6, 'max' => 20, 'message' => '{attribute}长度错误'],
            [['idcard'], 'string', 'min' => 15, 'max' => 18, 'message' => '{attribute}长度错误'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $images = [];
            foreach ($this->photo as $file) {
                $rootPath = "uploads/record/";	//定义上传的根目录
                $ext = $file->extension;	//获取文件的后缀(*格式*)
                $randName = $this->name.'_身份证_'.date('YmdHis')  . "." . $ext;	//重新编译文件名称
                if (!file_exists($rootPath)) {	//判断该目录是否存在
                    mkdir($rootPath, true);
                }
                $path = $rootPath . $randName;
                if ($file->saveAs($path)) {
                    $images['photo'][] = $path;
                }
            }
            foreach ($this->photo2 as $file) {
                $rootPath = "uploads/record/";	//定义上传的根目录
                $ext = $file->extension;	//获取文件的后缀(*格式*)
                $randName = $this->name.'_凭证_'.date('YmdHis') . "." . $ext;	//重新编译文件名称
                if (!file_exists($rootPath)) {	//判断该目录是否存在
                    mkdir($rootPath, true);
                }
                $path = $rootPath . $randName;
                if ($file->saveAs($path)) {
                    $images['photo2'][] = $path;
                }
            }
            return $images;
        } else {
            return $this->getErrors();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '姓名',
            'name_pin' => '姓名拼音',
            'mobile' => '手机号码',
            'urgent_name' => '紧急联系人',
            'urgent_tel' => '紧急联系人手机号码',
            'sex' => '性别',
            'idcard' => '身份证号',
            'shop_name' => '店铺名',
            'my_identity' => '我的身份',
            'is_passport' => '有无护照',
            'photo' => '证件照',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
            'status' => '状态',
            'wallet_number' => '钱包账号',
            'wallet_address' => '充值地址',
        ];
    }
}
