<?php
namespace admin\controllers;

use common\services\AclService;
use admin\services\AdminLogin;
use yii\filters\AccessControl;
use Yii;
use common\models\AdminUser;

/**
 * Site controller
 */
class LoginController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles'=>['?']
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles'=>['@']
                    ]
                ],
            ],
        ];
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $user = AdminLogin::login($this->pString('username'), $this->pString('password'));

            if ($user === false) {
                $this->errorMsg = '用户名或密码错误！';
                return $this->render('login');
            }
            if ($user->status == AdminUser::STATUS_DELETED) {
                $this->errorMsg = '账号状态异常，请联系管理员！';
                return $this->render('login');
            }



            \Yii::$app->user->login($user);


            $this->redirect('index');
        } else {
            return $this->render('index');
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
