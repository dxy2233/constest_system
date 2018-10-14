<?php
/**
 * Created by IntelliJ IDEA.
 * User: MacPro
 * Date: 2017/12/11
 * Time: 下午9:37
 */

namespace common\components\Behavior;

use yii\web\UnauthorizedHttpException;
use Yii;
use yii\web\HttpException;

class HttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    public $isThrowException = false;

    public function handleFailure($response)
    {
        if ($this->isThrowException) {
//            throw new UnauthorizedHttpException('授权验证失败');
            throw new HttpException(200, '授权验证失败');
            return false;
        }
        return true;
    }

    /**
     * 重写返回true，继续执行后续逻辑
     * @param \yii\base\Action $action
     * @return bool
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        $response = $this->response ?: Yii::$app->getResponse();

        try {
            $identity = $this->authenticate(
                $this->user ?: Yii::$app->getUser(),
                $this->request ?: Yii::$app->getRequest(),
                $response
            );
        } catch (UnauthorizedHttpException $e) {
            if ($this->isOptional($action)) {
                return true;
            }

            throw $e;
        }

        if ($identity !== null || $this->isOptional($action)) {
            return true;
        }

        $this->challenge($response);
        $res = $this->handleFailure($response);

        if ($res) {
            return true;
        }

        return false;
    }
}
