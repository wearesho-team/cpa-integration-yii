<?php

namespace Wearesho\Cpa\Yii\Tests\Behaviors;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

use Wearesho\Cpa\Lead\LeadMemoryRepository;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidCallException;

use Wearesho\Cpa\SalesDoubler\Lead as SalesDoublerLead;
use Wearesho\Cpa\SalesDoubler\PostbackService as SalesDoublerPostbackService;
use Wearesho\Cpa\SalesDoubler\PostbackServiceConfig as SalesDoublerPostbackServiceConfig;

use Wearesho\Cpa\Repository\ConversionMemoryRepository;
use Wearesho\Cpa\Tests\Helpers\HttpTestClient;

use Wearesho\Cpa\Yii\Repositories\ConversionRepository;
use Wearesho\Cpa\Yii\Repositories\LeadRepository;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use Wearesho\Cpa\Yii\Behaviors\ConversionBehavior;

use Wearesho\Cpa\Yii\Tests\AbstractTestCase;
use Wearesho\Cpa\Yii\Tests\Mocks\ConversionSourceMock;

/**
 * Class ConversionBehaviorTest
 * @package Wearesho\Cpa\Yii\Tests\Behaviors
 */
class ConversionBehaviorTest extends AbstractTestCase
{
    /** @var  ConversionBehavior */
    protected $behavior;

    protected function setUp()
    {
        parent::setUp();
        $this->behavior = new ConversionBehavior();
        $this->behavior->attach(new Component);
    }

    public function testSettingConversionRepo()
    {
        $this->assertInstanceOf(
            ConversionRepository::class,
            $this->behavior->getConversionRepository(),
            "Conversion repository getter should return instance of " . ConversionRepository::class . " if not set before"
        );

        $conversionRepository = new ConversionMemoryRepository();
        $this->behavior->setConversionRepository($conversionRepository);

        $this->assertEquals(
            $conversionRepository,
            $this->behavior->getConversionRepository(),
            "Conversion repository getter should return object passed to setter"
        );
    }

    public function testSettingLeadRepo()
    {
        $this->assertInstanceOf(
            LeadSessionRepository::class,
            $this->behavior->getLeadRepository(),
            "Lead repository getter should return instance of " . LeadSessionRepository::class . "if no set before"
        );

        $leadRepository = new LeadRepository();
        $this->behavior->setLeadRepository($leadRepository);

        $this->assertEquals(
            $leadRepository,
            $this->behavior->getLeadRepository(),
            "Lead repository getter should return instance passed to setter"
        );
    }

    public function testPassingInvalidEvent()
    {
        $eventWithoutCorrectSource = new Event([
            'data' => [
                'id' => null,
            ]
        ]);
        $this->expectException(InvalidCallException::class);
        $this->behavior->owner->trigger(
            ConversionBehavior::EVENT_CONVERSION_REGISTERED,
            $eventWithoutCorrectSource
        );

    }

    public function testSkippingOnNoLead()
    {
        $this->behavior->httpClient = $httpTestClient = new HttpTestClient();

        $requestSent = false;
        $httpTestClient->setClosure(function () use (&$requestSent) {
            $requestSent = true;
            return new Response();
        });

        $event = new Event([
            'sender' => new ConversionSourceMock(1),
        ]);
        $this->behavior->owner->trigger(ConversionBehavior::EVENT_CONVERSION_REGISTERED, $event);
        $this->assertFalse(
            $requestSent,
            "Behavior should not register conversion if no lead found in repository"
        );
    }

    public function testSendingConversion()
    {
        $this->behavior->httpClient = $httpTestClient = new HttpTestClient();
        $event = new Event([
            'sender' => $conversionSource = new ConversionSourceMock(10),
        ]);

        $requestSent = false;
        $httpTestClient->setClosure(function (Request $request) use (&$requestSent, $conversionSource) {
            $url = $request->getUri()->__toString();
            $this->assertContains(
                (string)$conversionSource->getConversionId(),
                $url
            );
            $requestSent = true;
            return new Response;
        });

        $this->behavior->setLeadRepository($leadRepository = new LeadMemoryRepository);
        $leadRepository->push(new SalesDoublerLead($clickId = 2));

        $this->behavior->postbackConfig = [
            'SalesDoubler' => [
                'id' => 1,
                'token' => 'Token',
            ],
        ];

        $this->behavior->owner->trigger(ConversionBehavior::EVENT_CONVERSION_REGISTERED, $event);
        $this->assertTrue(
            $requestSent,
            "Behavior should create and send conversion if lead presents in repository"
        );
    }

    public function testSendingConversionWithLazyLoading()
    {
        $httpTestClient = new HttpTestClient();
        $this->behavior->httpClient = function () use ($httpTestClient) {
            return $httpTestClient;
        };
        $event = new Event([
            'sender' => $conversionSource = new ConversionSourceMock(10),
        ]);

        $requestSent = false;
        $httpTestClient->setClosure(function (Request $request) use (&$requestSent, $conversionSource) {
            $url = $request->getUri()->__toString();
            $this->assertContains(
                (string)$conversionSource->getConversionId(),
                $url
            );
            $requestSent = true;
            return new Response;
        });

        $this->behavior->setLeadRepository($leadRepository = new LeadMemoryRepository);
        $leadRepository->push(new SalesDoublerLead($clickId = 2));

        $this->behavior->postbackConfig = function () {
            return [];
        };
        $this->behavior->postbackServices = function () use ($httpTestClient) {
            $config = new SalesDoublerPostbackServiceConfig();
            $config->setBaseUrl("https://wearesho.com/");
            $config->setId(2);
            $config->setToken("Token");

            return [
                new SalesDoublerPostbackService(
                    $this->behavior->getConversionRepository(),
                    $httpTestClient,
                    $config
                ),
            ];
        };

        $this->behavior->owner->trigger(ConversionBehavior::EVENT_CONVERSION_REGISTERED, $event);
        $this->assertTrue(
            $requestSent,
            "Behavior should create and send conversion if lead presents in repository"
        );
    }

    public function testDefaultHttpClient()
    {
        $this->assertInstanceOf(
            Client::class,
            $this->behavior->httpClient,
            "Behavior HttpClient should be instance " . Client::class . " by default"
        );
    }
}