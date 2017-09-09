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
    /**
     * This event will be triggered on owner by behavior if lead correctly generated.
     * It will pass Event with data:
     *
     * ```php
     * new Event([
     *   'data' => [
     *      'lead' => $lead, // lead implement \Wearesho\Cpa\Interfaces\LeadInterface,
     *   ],
     * ]);
     * ```
     *
     * @see LeadInterface
     */
    const EVENT_LEAD_GENERATED = "onLeadGenerated";

    /** @var LeadFactoryInterface[] See common LeadFactory from wearesho-team/cpa-integration */
    public $factories = null;

    /** @var string Url to parse (\Yii::$app->request->url will be used by default) */
    public $url;

    /** @var string Cookie to parse lead from if no lead found in URL */
    public $cookieName;

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
    protected function generateLead()
    {
        $factory = new LeadFactory($this->factories);
        $lead = $factory->fromUrl($this->url ?? \Yii::$app->request->url);
        if ($lead instanceof LeadInterface) {
            $this->getLeadRepository()->push($lead);
        }
    }
}