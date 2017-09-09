<?php

namespace Wearesho\Cpa\Yii\Behaviors;

use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;
use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use yii\base\Event;
use yii\base\Behavior;

use GuzzleHttp\Client;

use Wearesho\Cpa\Interfaces\LeadInterface;
use Wearesho\Cpa\Interfaces\PostbackServiceInterface;
use Wearesho\Cpa\Postback\PostbackService;
use Wearesho\Cpa\Postback\PostbackServiceConfig;
use Wearesho\Cpa\Yii\Repositories\ConversionRepository;

/**
 * Class SendConversionBehavior
 * @package Wearesho\Cpa\Yii\Behaviors
 *
 * @property \Closure $id
 * @property \Closure $config
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

    /**
     * This event will be triggered by behavior when conversion created and sent successfully
     * Behavior will provide event with data:
     *
     * ```php
     * new Event([
     *  'data' => [
     *    'conversion' => $conversion, // StoredConversionInterface instance
     *  ],
     * ]);
     * ```
     */
    const EVENT_CONVERSION_SENT = 'onConversionSent';

    /**
     * This event will be triggered if some exception thrown while sending conversion.
     * Behavior will provide event with data:
     *
     * ```php
     * new Event([
     *  'data' => [
     *    'exception' => $exception,
     *  ],
     * ]);
     * ```
     * @see PostbackServiceInterface::send() - List of exceptions may be thrown
     */
    const EVENT_CONVERSION_ERROR = 'onConversionError';

    /** @var string Key for session storage where lead will be stored */
    public $key = "cpa-lead";

    /** @var string Class implement \GuzzleHttp\ClientInterface */
    public $httpClient = Client::class;

    /**
     * @return callable
     */
    public function getConfig(): callable
    {
        return $this->config;
    }

    /**
     * @param callable $config
     * @return $this
     */
    public function setConfig(callable $config): self
    {
        $this->config = $config;
        return $this;
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
    protected function sendConversion(Event $event)
    {
        $conversionId = $event->data['id'] ?? null;
        if (is_null($conversionId)) {
            return;
        }

        $lead = $this->leadRepository->pull();
        if (!$lead instanceof LeadInterface) {
            return;
        }

        $conversion = $lead->createConversion($conversionId);
        $config = new PostbackServiceConfig(call_user_func($this->getConfig()));
        $client = new $this->httpClient;

        $service = new PostbackService(
            new ConversionRepository(),
            $client,
            $config
        );

        try {
            $service->send($conversion);
            $this->owner->trigger(
                static::EVENT_CONVERSION_SENT,
                new Event([
                    'data' => [
                        'conversion' => $conversion,
                    ],
                ])
            );
        } catch (\Exception $exception) {
            $this->owner->trigger(
                static::EVENT_CONVERSION_ERROR,
                new Event([
                    'data' => [
                        'exception' => $exception,
                    ],
                ])
            );
        }
    }
}