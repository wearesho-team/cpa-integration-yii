<?php

namespace Wearesho\Cpa\Yii\Tests\Behaviors;

use Wearesho\Cpa\Lead\LeadFactory;
use yii\base\Component;
use yii\base\Controller;
use yii\base\InvalidConfigException;

use yii\web\Cookie;

use Wearesho\Cpa\Yii\Behaviors\LeadBehavior;
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;

use Wearesho\Cpa\PrimeLead\Lead as PrimeLeadLead;

use Wearesho\Cpa\SalesDoubler\LeadFactory as SalesDoublerLeadFactory;

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

    public function testGeneratingFromCookie()
    {
        $transactionId = mt_rand();
        $primeLeadCookie = '{"utm_source":"primelead","transaction_id":' . $transactionId . '}';
        $this->behavior->cookie = new Cookie([
            'name' => mt_rand(),
            'value' => $primeLeadCookie,
        ]);
        $this->behavior->setLeadRepository(new LeadSessionRepository(mt_rand()));
        $this->behavior->owner->trigger(Controller::EVENT_BEFORE_ACTION);

        /** @var PrimeLeadLead $lead */
        $lead = $this->behavior->getLeadRepository()->pull();
        $this->assertInstanceOf(
            PrimeLeadLead::class,
            $lead,
            "Behavior should parse cookie and push LeadInterface into repository"
        );
        $this->assertEquals(
            $transactionId,
            $lead->getTransactionId(),
            "Behavior should correctly pass cookie into factory"
        );
    }

    public function testGeneratingFromLazyLoadedCookie()
    {
        $transactionId = mt_rand();
        $this->behavior->cookie = function () use ($transactionId) {
            $primeLeadCookie = '{"utm_source":"primelead","transaction_id":' . $transactionId . '}';
            return new Cookie([
                'name' => mt_rand(),
                'value' => $primeLeadCookie,
            ]);
        };
        $this->behavior->setLeadRepository(new LeadSessionRepository(mt_rand()));
        $this->behavior->owner->trigger(Controller::EVENT_BEFORE_ACTION);

        /** @var PrimeLeadLead $lead */
        $lead = $this->behavior->getLeadRepository()->pull();
        $this->assertInstanceOf(
            PrimeLeadLead::class,
            $lead,
            "Behavior should parse cookie and push LeadInterface into repository"
        );
        $this->assertEquals(
            $transactionId,
            $lead->getTransactionId(),
            "Behavior should correctly pass cookie into factory"
        );
    }

    public function testPassingInvalidCookieCallable()
    {
        $this->expectException(InvalidConfigException::class);
        $this->behavior->cookie = function () {
            return null;
        };
        $this->behavior->owner->trigger(Controller::EVENT_BEFORE_ACTION);
    }

    public function testLazyLoadingChildFactories()
    {
        $this->behavior->factories = function () {
            return [
                new SalesDoublerLeadFactory(),
            ];
        };
        $this->behavior->url = $notSalesDoublerUrl = "https://google.com/?utm_source=primelead&transaction_id=1";
        $this->behavior->owner->trigger(Controller::EVENT_BEFORE_ACTION);
        $this->assertNull(
            $this->behavior->getLeadRepository()->pull(),
            "Behavior must correctly pass lazy loaded factories list to common " . LeadFactory::class
        );
    }
}