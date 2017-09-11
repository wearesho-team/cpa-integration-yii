<?php

namespace Wearesho\Cpa\Yii\Behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;

use yii\console\Controller;

use yii\web\Cookie;

use Wearesho\Cpa\Interfaces\LeadFactoryInterface;
use Wearesho\Cpa\Interfaces\LeadInterface;
use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;
use Wearesho\Cpa\Lead\LeadFactory;

use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;

/**
 * Class StoreLeadBehavior
 * @package Wearesho\Cpa\Yii\Behaviors
 *
 * @property LeadRepositoryInterface $leadRepository
 */
class LeadBehavior extends Behavior
{
    const DEFAULT_COOKIE_NAME = "cpa";

    /** @var LeadFactoryInterface[]|callable See common LeadFactory from wearesho-team/cpa-integration */
    public $factories = null;

    /** @var string|callable Url to parse (\Yii::$app->request->url will be used by default) */
    public $url;

    /** @var string Cookie|null|callable to parse lead from if no lead found in URL */
    public $cookie = false;

    /**
     * @return array
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'generateLead',
        ];
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
     * @return void
     */
    public function generateLead()
    {
        $factory = new LeadFactory(
            is_callable($this->factories)
                ? call_user_func($this->factories)
                : $this->factories
        );

        $lead = $factory->fromUrl($this->getUrl());
        if ($lead instanceof LeadInterface) {
            $this->getLeadRepository()->push($lead);
            return;
        }

        $cookie = $this->getCookie();
        $lead = $factory->fromCookie($cookie ? $cookie->value : "");
        if ($lead instanceof LeadInterface) {
            $this->getLeadRepository()->push($lead);
            \Yii::$app->response->cookies->remove($cookie->name);
        }
    }

    /**
     * @return string
     */
    private function getUrl(): string
    {
        if (is_null($this->url)) {
            try {
                return \Yii::$app->request->url;
            } catch (InvalidConfigException $exception) {
                return "/";
            }

        }

        return is_callable($this->url) ? call_user_func($this->url) : (string)$this->url;
    }

    /**
     * @throws InvalidConfigException
     * @return Cookie|null
     */
    private function getCookie()
    {
        if ($this->cookie === false) {
            return \Yii::$app->request->cookies->get(static::DEFAULT_COOKIE_NAME);
        }

        if ($this->cookie instanceof Cookie || is_null($this->cookie)) {
            return $this->cookie;
        }

        if (
            is_callable($this->cookie) &&
            ($cookie = call_user_func($this->cookie)) instanceof Cookie
        ) {
            return $cookie;
        }

        throw new InvalidConfigException(
            "Cookie must be instance of " . Cookie::class . " or callable returns " . Cookie::class
        );
    }
}