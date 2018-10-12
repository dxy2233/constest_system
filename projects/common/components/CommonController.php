<?php
namespace common\components;
use common\traits\BaseControllerTrait;
use yii\web\Controller;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CommonController extends Controller
{

    public $cssFiles = array();
    public $jsFiles = array();
    
    //seo
    public $pageTitle = '';
    public $pageKeyWords = '';
    public $pageDescription = '';

    //缓存时间 相关类只需要覆盖此参数即可 默认不缓存
    protected $expires = 0;

    public $errors = array();
    public $infos = array();
    public $summitErrors = array();


    use BaseControllerTrait;

}