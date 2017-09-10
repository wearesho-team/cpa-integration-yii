<?php

namespace Wearesho\Cpa\Yii\Behaviors;

use Wearesho\Cpa\Yii\Repositories\LeadSessionRepository;
use yii\base\Behavior;
use yii\console\Controller;

use Wearesho\Cpa\Interfaces\LeadFactoryInterface;
use Wearesho\Cpa\Interfaces\LeadInterface;
use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;
use Wearesho\Cpa\Lead\LeadFactory;

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

    /** @var string Cookie to parse lead from if no lead found in URL */
    public $cookieName = self::DEFAULT_COOKIE_NAME;

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

        $lead = $factory->fromCookie(\Yii::$app->request->cookies->get($this->cookieName) ?? "");
        if ($lead instanceof LeadInterface) {
            $this->getLeadRepository()->push($lead);
            \Yii::$app->response->cookies->remove($this->cookieName);
        }
    }

    /**
     * @return string
     */
    private function getUrl(): string
    {
        if (is_null($this->url)) {
            return \Yii::$app->request->url;
        }

        return is_callable($this->url) ? call_user_func($this->url) : (string)$this->url;
    }
}