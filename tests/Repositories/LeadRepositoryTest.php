<?php

namespace Wearesho\Cpa\Yii\Tests\Repositories;

use Wearesho\Cpa\PrimeLead\Lead;
use Wearesho\Cpa\Yii\Exceptions\InvalidIdException;
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;
use Wearesho\Cpa\Yii\Tests\User;

/**
 * Class LeadRepositoryTest
 * @package Wearesho\Cpa\Yii\Tests\Repositories
 */
class LeadRepositoryTest extends AbstractTestCase
{
    /** @var  LeadRepository */
    protected $repository;

    /** @var  User */
    protected $user;


    protected function setUp()
    {
        parent::setUp();
        $this->repository = new LeadRepository($this->user = new User);
    }

    public function testPullInvalidUser()
    {
        $this->expectException(InvalidIdException::class);
        $this->repository->pull();
    }

    public function testPushInvalidUser()
    {
        $this->expectException(InvalidIdException::class);
        $this->repository->push($lead = new Lead(1));
    }

    public function testCreating()
    {
        $this->user->setId($userId = 1);
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

    public function testMissingLead()
    {
        $this->user->setId(1);
        $storedLead = $this->repository->pull();
        $this->assertNull(
            $storedLead,
            "Repository should pull null if no lead stored"
        );
    }

    public function testOverriding()
    {
        $this->user->setId($userId = 1);

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
}