<?php

namespace Wearesho\Cpa\Yii\Exceptions;


use Throwable;
use Wearesho\Cpa\Exceptions\CpaException;
use yii\base\Model;

/**
 * Class ValidationException
 * @package Wearesho\Cpa\Yii\Exceptions
 */
class ValidationException extends CpaException
{
    /** @var  Model */
    protected $model;

    /**
     * ValidationException constructor.
     * @param Model $model
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(Model $model, $code = 0, Throwable $previous = null)
    {
        $message = implode(";", $model->getFirstErrors());
        parent::__construct($message, $code, $previous);

        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}