<?php

namespace Wearesho\Cpa\Yii\Models;

use yii\db\ActiveRecordInterface;

use Wearesho\Cpa\Interfaces\LeadInterface;
use yii\web\IdentityInterface;

/**
 * Interface StoredLeadInterface
 * @package Wearesho\Cpa\Yii\Models
 */
interface StoredLeadInterface extends ActiveRecordInterface
{
    /**
     * @param LeadInterface $lead
     * @return StoredLeadInterface
     */
    public function setLead(LeadInterface $lead): StoredLeadInterface;

    /**
     * @return LeadInterface
     */
    public function getLead(): LeadInterface;

    /**
     * @param IdentityInterface $identity
     * @return StoredLeadInterface
     */
    public function setIdentity(IdentityInterface $identity): StoredLeadInterface;

    /**
     * @return int
     */
    public function getUserId(): int;
}