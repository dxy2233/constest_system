<?php
namespace common\services;

/**
 *
 * User: simonzhang
 * Date: 15/7/3
 * Time: 下午12:51
 */
class ServiceBase
{
    public static $platformId = 1;

    protected static $logDomain = 'service';

    private static $transaction;
    protected static $instance;

    public function init()
    {
    }

    /**
     * @return ServiceBase
     */
    public static function getServiceInstance() : ServiceBase
    {
        $className = get_called_class();
        $instance = new $className();
        $instance->init();

        return $instance;
    }

    public static function executeSql($sql)
    {
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($sql);

        return $command->execute();
    }

    public static function executeSqlAndGetRows($sql)
    {
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($sql);

        return $command->queryAll();
    }

    public static function executeSqlAndGetRow($sql)
    {
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($sql);

        return $command->query();
    }

    public static function executeSqlAndGetScalar($sql)
    {
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }

    public static function beginTransaction()
    {
        self::$transaction = \Yii::$app->db->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$transaction->commit();
    }

    public static function rollbackTransaction()
    {
        self::$transaction->rollBack();
    }
    
    protected static function info($msg, $cate = 'app')
    {
        \Yii::info($msg, $cate);
    }

    protected static function error($msg, $cate = 'app')
    {
        \Yii::error($msg, $cate);
    }

    protected static function warning($msg, $cate = 'app')
    {
        \Yii::warning($msg, $cate);
    }
}
