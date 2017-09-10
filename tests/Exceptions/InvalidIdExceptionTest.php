<?php

namespace Wearesho\Cpa\Yii\Tests\Exceptions;

use PHPUnit\Framework\TestCase;

use Wearesho\Cpa\Yii\Exceptions\InvalidIdException;
use Wearesho\Cpa\Yii\Tests\User;

class InvalidIdExceptionTest extends TestCase
{
    public function testMessage()
    {
        $identity = new User();
        $exception = new InvalidIdException($identity);

        $this->assertEquals(
            $identity,
            $exception->getIdentity(),
            "Identity getter should return identity instance passed to construcot"
        );
        $this->assertContains(
            User::class,
            $exception->getMessage(),
            "Exception message should contain identity class name"
        );
    }
}