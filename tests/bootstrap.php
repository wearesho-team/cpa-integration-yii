<?php
require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$config = [
    'id' => 'cpa-integration-yii-test',
    'basePath' => dirname(__DIR__),
];

$application = new yii\web\Application($config);