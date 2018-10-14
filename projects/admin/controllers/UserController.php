<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BSetting;

/**
 * Site controller
 */
class UserController extends BaseController
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
        $find = BUser::find();
        $pageSize = $this->pInt('pageSize');
        $page = $this->pInt('page');
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['like','username',$searchName]);
        }
        $str_time = $this->pString('str_time');
        $end_time = $this->pString('end_time');
        
        $count = $find->count();
        $list = $find->page($page, $pageSize)->asArray()->all();
    }
}
