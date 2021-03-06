<?php

use Wearesho\Cpa\Yii\Tests\User;

use yii\console\Application;

use yii\db\Migration;
use yii\db\Connection;

use yii\web\Session;
use yii\web\Request as WebRequest;
use yii\web\Response as WebResponse;
use yii\web\User as WebUser;

require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

function init_application()
{
    if (file_exists($_ENV['DB_PATH'])) {
        unlink($_ENV['DB_PATH']);
    }

    $config = [
        'id' => 'cpa-integration-yii-test',
        'basePath' => dirname(__DIR__),
        'components' => [
            'db' => [
                'class' => Connection::class,
                'dsn' => 'sqlite:' . $_ENV['DB_PATH'],
            ],
            'user' => [
                'class' => WebUser::class,
                'identityClass' => User::class,
            ],
            'session' => [
                'class' => Session::class,
            ],
            'request' => [
                'class' => WebRequest::class,
                'cookieValidationKey' => mt_rand(),
            ],
            'response' => [
                'class' => WebResponse::class,
            ],
        ],
    ];

    \Yii::$app = new Application($config);
    \Yii::$app->user->setIdentity(new User);
    foreach (new DirectoryIterator(dirname(__DIR__) . "/migrations") as $file) {
        if (!$file->isFile()) {
            continue;
        }
        include_once $file->getRealPath();
        $class = str_replace('.php', '', $file);
        $migration = new $class;
        if (!$migration instanceof Migration) {
            continue;
        }

        ob_start();
        $migration->up();
        ob_end_clean();
    }
}

function destroy_application()
{
    \Yii::$app = null;
    if (file_exists($_ENV['DB_PATH'])) {
        unlink($_ENV['DB_PATH']);
    }
}
