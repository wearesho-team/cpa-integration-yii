<?php

namespace Wearesho\Cpa\Yii\Behaviors;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;

use Wearesho\Cpa\Interfaces\PostbackServiceInterface;
use Wearesho\Cpa\Yii\Interfaces\ConversionSourceInterface;
use yii\base\Event;
use yii\base\Behavior;

use Wearesho\Cpa\Interfaces\LeadInterface;
use Wearesho\Cpa\Interfaces\ConversionRepositoryInterface;
use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;
use Wearesho\Cpa\Postback\PostbackService;
use Wearesho\Cpa\Postback\PostbackServiceConfig;
use Wearesho\Cpa\Yii\Repositories\ConversionRepository;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use yii\base\InvalidCallException;

/**
 * Class SendConversionBehavior
 * @package Wearesho\Cpa\Yii\Behaviors
 *
 * @property ConversionRepositoryInterface $conversionRepository
 * @property LeadRepositoryInterface $leadRepository
 */
class ConversionBehavior extends Behavior
{
    /**
     * This event should be triggered by owner when it needs to create conversion
     * Event should have data with conversion id:
     *
     * ```php
     * new Event([
     *  'data' => [
     *    'id' => 1, // owner conversion id here
     *  ],
     * ]);
     * ```
     */
    const EVENT_CONVERSION_REGISTERED = 'onConversionRegistered';

    /** @var ClientInterface|callable */
    public $httpClient = null;

    /** @var array|callable */
    public $postbackConfig = [];

    /** @var PostbackServiceInterface[]|callable */
    public $postbackServices = null;

    public function init()
    {
        parent::init();
        $this->httpClient = $this->httpClient ?? new Client;
    }

    /**
     * @return LeadRepositoryInterface
     */
    public function getLeadRepository(): LeadRepositoryInterface
    {
        return $this->leadRepository ?? ($this->leadRepository = new LeadSessionRepository);
    }

    /**
     * @param LeadRepositoryInterface $leadRepository
     * @return $this
     */
    public function setLeadRepository(LeadRepositoryInterface $leadRepository): self
    {
        $this->leadRepository = $leadRepository;
        return $this;
    }

    /**
     * @return ConversionRepositoryInterface
     */
    public function getConversionRepository(): ConversionRepositoryInterface
    {
        return $this->conversionRepository ?? ($this->conversionRepository = new ConversionRepository());
    }

    /**
     * @param ConversionRepositoryInterface $conversionRepository
     * @return $this
     */
    public function setConversionRepository(ConversionRepositoryInterface $conversionRepository): self
    {
        $this->conversionRepository = $conversionRepository;
        return $this;
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            static::EVENT_CONVERSION_REGISTERED => 'sendConversion',
        ];
    }

    /**
     * @param Event $event
     * @throws \Exception
     * @return void
     */
    public function sendConversion(Event $event)
    {
        if (!$event->sender instanceof ConversionSourceInterface) {
            throw new InvalidCallException("Event sender must implement " . ConversionSourceInterface::class);
        }

        $lead = $this->leadRepository->pull();
        if (!$lead instanceof LeadInterface) {
            return;
        }

        $conversion = $lead->createConversion($event->sender->getConversionId());
        $service = new PostbackService(
            $this->getConversionRepository(),
            $this->getClient(),
            new PostbackServiceConfig($this->getConfig()),
            $this->getServices()
        );

        $service->send($conversion);
    }

    /**
     * @return PostbackServiceInterface[]|null
     */
    private function getServices()
    {
        return is_callable($this->postbackServices)
            ? call_user_func($this->postbackServices)
            : $this->postbackServices;
    }

    /**
     * @return ClientInterface
     */
    private function getClient(): ClientInterface
    {
        return is_callable($this->httpClient)
            ? call_user_func($this->httpClient)
            : $this->httpClient;
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        return is_callable($this->postbackConfig)
            ? call_user_func($this->postbackConfig)
            : $this->postbackConfig;
    }
}