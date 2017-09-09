<?php

namespace Wearesho\Cpa\Yii\Repositories;

use Yii;

use Wearesho\Cpa\Interfaces\LeadInterface;
use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;

/**
 * Class LeadSessionRepository
 * @package Wearesho\Cpa\Yii\Repositories
 */
class LeadSessionRepository implements LeadRepositoryInterface
{
    /** @var string */
    protected $sessionKey = "cpa";

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    /**
     * @param string $sessionKey
     * @return $this
     */
    public function setSessionKey(string $sessionKey = "cpa")
    {
        $this->sessionKey = $sessionKey;
        return $this;
    }

    /**
     * Saving sent conversion in storage
     *
     * @param LeadInterface $conversion
     * @return void
     */
    public function push(LeadInterface $conversion)
    {
        \Yii::$app->session->set($this->sessionKey, serialize($conversion));
    }

    /**
     * @return null|LeadInterface
     */
    public function pull()
    {
        $serializedLead = Yii::$app->session->get($this->sessionKey);
        $lead = unserialize($serializedLead);

        return $lead instanceof LeadInterface ? $lead : null;
    }
}