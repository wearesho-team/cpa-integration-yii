<?php

namespace Wearesho\Cpa\Yii\Tests\Behaviors;

use yii\base\Component;
use yii\base\Controller;

use Wearesho\Cpa\Yii\Behaviors\LeadBehavior;
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;

use Wearesho\Cpa\PrimeLead\Lead as PrimeLeadLead;

/**
 * Class LeadBehaviorTest
 * @package Wearesho\Cpa\Yii\Tests\Behaviors
 */
class LeadBehaviorTest extends AbstractTestCase
{
    /** @var  LeadBehavior */
    protected $behavior;

    protected function setUp()
    {
        parent::setUp();
        $this->behavior = new LeadBehavior();
        $this->behavior->attach(new Component());
    }

    public function testGettingLeadRepository()
    {
        $this->assertInstanceOf(
            LeadSessionRepository::class,
            $this->behavior->getLeadRepository(),
            "Getter must return " . LeadSessionRepository::class . " instance by if not other set"
        );
        $repository = new LeadRepository();
        $this->behavior->setLeadRepository($repository);
        $this->assertEquals(
            $repository,
            $this->behavior->getLeadRepository(),
            "Lead repository getter should return repository instance passed to constructor"
        );
    }

    public function testGenerationNoLead()
    {
        $this->behavior->url = "https://google.com";
        $this->behavior->owner->trigger(Controller::EVENT_BEFORE_ACTION);

        $this->assertNull(
            $this->behavior->getLeadRepository()->pull(),
            "Behavior should not push lead if not correct url or cookie provided"
        );
    }

    public function testGeneratingFromUrl()
    {
        $validUrls = [
            "eagerLoaded" => "https://google.com/?utm_source=primelead&transaction_id=1",
            "lazyLoaded" => function () {
                return "https://google.com/?utm_source=primelead&transaction_id=1";
            },
        ];

        foreach ($validUrls as $validPrimeLeadUrl) {

            $this->behavior->setLeadRepository(new LeadSessionRepository(mt_rand()));
            $this->behavior->url = $validPrimeLeadUrl;
            $this->behavior->owner->trigger(Controller::EVENT_BEFORE_ACTION);

            $this->assertInstanceOf(
                PrimeLeadLead::class,
                $this->behavior->getLeadRepository()->pull(),
                "Behavior should push generated LeadInterface if correct url provided"
            );
        }
    }
}