<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 9/10/17
 * Time: 2:13 PM
 */

namespace Wearesho\Cpa\Yii\Models;


use Wearesho\Cpa\Interfaces\LeadInterface;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class StoredLead
 * @package Wearesho\Cpa\Yii\Models
 *
 * @property int $user_id
 * @property-read int $id
 * @property-read string $serialized_lead
 *
 * @property LeadInterface $lead
 */
class StoredLead extends ActiveRecord implements StoredLeadInterface
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['id', 'integer',],
            [['user_id', 'serialized_lead'], 'required',],
            ['user_id', 'integer', 'min' => 1,],
            ['serialized_lead', 'string'],
        ];
    }

    /**
     * @param LeadInterface $lead
     * @return $this
     */
    public function setLead(LeadInterface $lead): self
    {
        $this->serialized_lead = serialize($lead);
        return $this;
    }

    /**
     * @return LeadInterface
     */
    public function getLead(): LeadInterface
    {
        return unserialize($this->serialized_lead);
    }

    /**
     * @param IdentityInterface $identity
     * @return StoredLeadInterface
     */
    public function setIdentity(IdentityInterface $identity): StoredLeadInterface
    {
        $this->user_id = $identity->getId();
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }
}