<?php
namespace admin\controllers;

use common\services\AclService;

/**
 * Site controller
 */
class IndexController extends BaseController
{


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', ['actionStr' => '']);
    }
}
