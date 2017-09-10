<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 9/10/17
 * Time: 3:17 PM
 */

namespace Wearesho\Cpa\Yii\Tests;


use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        init_application();
    }

    protected function tearDown()
    {
        parent::tearDown();
        destroy_application();
    }
}