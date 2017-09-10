<?php

namespace Wearesho\Cpa\Yii\Exceptions;

use yii\web\IdentityInterface;

use Wearesho\Cpa\Exceptions\CpaException;

/**
 * Class InvalidIdException
 * @package Wearesho\Cpa\Yii\Exceptions
 */
class InvalidIdException extends CpaException
{
    /** @var IdentityInterface */
    protected $identity;

    /**
     * InvalidIdException constructor.
     * @param IdentityInterface $identity
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(IdentityInterface $identity, $code = 0, \Throwable $previous = null)
    {
        $message = "Invalid or empty ID of " . get_class($identity);
        parent::__construct($message, $code, $previous);

        $this->identity = $identity;
    }

    /**
     * @return IdentityInterface
     */
    public function getIdentity(): IdentityInterface
    {
        return $this->identity;
    }
}