<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 9/10/17
 * Time: 3:02 PM
 */

namespace Wearesho\Cpa\Yii\Tests;


use Wearesho\Cpa\Exceptions\NotImplementedException;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package Wearesho\Cpa\Yii\Tests
 */
class User implements IdentityInterface
{
    /** @var  int|null */
    protected $id;

    /**
     * User constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null)
    {
        $this->id = $id;
    }

    /**
     * @param int $id
     * @return IdentityInterface
     */
    public function setId(int $id): IdentityInterface
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public static function findIdentity($id)
    {
        throw new NotImplementedException();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotImplementedException();
    }

    public function getAuthKey()
    {
        throw new NotImplementedException();
    }

    public function validateAuthKey($authKey)
    {
        throw new NotImplementedException();
    }
}