<?php

namespace Wearesho\Cpa\Yii\Tests\Exceptions;

use yii\base\Model;

use Wearesho\Cpa\Yii\Exceptions\ValidationException;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;

class ValidationExceptionTest extends AbstractTestCase
{
    public function testMessage()
    {
        $model = new Model();
        $model->addError(
            $errorAttribute = "attribute",
            $errorText = "Error occurred."
        );
        $exception = new ValidationException($model);
        $this->assertEquals(
            $model,
            $exception->getModel(),
            "Model getter should return model instance passed to constructor"
        );
        $this->assertContains(
            $errorText,
            $exception->getMessage(),
            "Exception message should contain model errors text"
        );
    }
}