<?php

namespace Wearesho\Cpa\Yii\Tests\Repositories;

use Wearesho\Cpa\PrimeLead\Lead;

use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;

/**
 * Class LeadSessionRepositoryTest
 * @package Wearesho\Cpa\Yii\Tests\Repositories
 */
class LeadSessionRepositoryTest extends AbstractTestCase
{
    /** @var  LeadSessionRepository */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = new LeadSessionRepository();
    }

    public function testSetting()
    {
        $lead = new Lead(mt_rand());
        $this->repository->push($lead);

        /** @var Lead $storedLead */
        $storedLead = $this->repository->pull();
        $this->assertInstanceOf(
            get_class($lead),
            $storedLead,
            "Must pull same class instance after push"
        );
        $this->assertEquals(
            $lead->getTransactionId(),
            $storedLead->getTransactionId(),
            "Repository should correctly save lead properties"
        );
    }

    public function testOverriding()
    {
        $this->repository->push($firstLead = new Lead(1));
        $this->repository->push($secondLead = new Lead(2));

        $storedLead = $this->repository->pull();
        $this->assertNotEquals(
            $firstLead,
            $storedLead,
            "Repository should override lead"
        );
        $this->assertEquals(
            $secondLead,
            $storedLead,
            "Repository should contain latest pushed lead"
        );
    }

    public function testUsingRightSessionKey()
    {
        $this->repository->push(new Lead(1));
        $this->repository->setSessionKey(mt_rand());
        $this->assertNull(
            $this->repository->pull(),
            "Repository should read lead from set session key"
        );
    }

    public function testSettingSessionKey()
    {
        $this->repository->setSessionKey($newSessionKey = mt_rand());
        $this->assertEquals(
            $newSessionKey,
            $this->repository->getSessionKey(),
            "Repository session key getter must return value from setter"
        );
    }
}