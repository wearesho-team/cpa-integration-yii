<?php

namespace Wearesho\Cpa\Yii\Tests\Models;

use Wearesho\Cpa\SalesDoubler\Lead as SalesDoublerLead;

use Wearesho\Cpa\Yii\Models\StoredLead;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;
use Wearesho\Cpa\Yii\Tests\User;

class StoredLeadTest extends AbstractTestCase
{
    public function testSettingIdentity()
    {
        $identity = new User(mt_rand() * 100);
        $model = new StoredLead();
        $model->setIdentity($identity);
        $this->assertEquals(
            $identity->getId(),
            $model->getUserId(),
            "User Id getter should return id of identity passed to setter"
        );
    }

    public function testSettingLead()
    {
        $lead = new SalesDoublerLead(mt_rand());
        $model = new StoredLead();
        $model->setLead($lead);
        $this->assertEquals(
            $lead,
            $model->getLead(),
            "Lead getter should return lead instance passed to setter"
        );
    }
}