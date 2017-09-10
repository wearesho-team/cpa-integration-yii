<?php

namespace Wearesho\Cpa\Yii\Repositories;


use Wearesho\Cpa\Interfaces\LeadInterface;
use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;

use Wearesho\Cpa\Yii\Exceptions\InvalidIdException;
use Wearesho\Cpa\Yii\Exceptions\ValidationException;
use Wearesho\Cpa\Yii\Models\StoredLead;
use Wearesho\Cpa\Yii\Models\StoredLeadInterface;

use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Class LeadRepository
 * @package Wearesho\Cpa\Yii\Repositories
 */
class LeadRepository implements LeadRepositoryInterface
{
    /** @var StoredLeadInterface */
    protected $storeLeadModel;

    /** @var  IdentityInterface */
    protected $identity;

    /**
     * LeadRepository constructor.
     *
     * @param StoredLeadInterface|null $lead
     * @param IdentityInterface|null $identity
     */
    public function __construct(IdentityInterface $identity = null, StoredLeadInterface $lead = null)
    {
        $this->storeLeadModel = $lead ?? new StoredLead;
        $this->identity = $identity ?? \Yii::$app->user;
    }

    /**
     * Saving sent conversion in storage
     *
     * @param LeadInterface $lead
     *
     * @throws InvalidIdException
     * @throws ValidationException
     *
     * @return void
     */
    public function push(LeadInterface $lead)
    {
        if (!$this->identity->getId()) {
            throw new InvalidIdException($this->identity);
        }

        $previousModel = $this->findModel();
        if ($previousModel instanceof StoredLeadInterface) {
            $previousModel->delete();
        }

        $modelClass = get_class($this->storeLeadModel);

        /** @var StoredLeadInterface $model */
        $model = new $modelClass;
        $model->setIdentity($this->identity);
        $model->setLead($lead);
        // @codeCoverageIgnoreStart
        if (!$model->save() && $model instanceof Model) {
            throw new ValidationException($model);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @throws InvalidIdException
     * @return null|LeadInterface
     */
    public function pull()
    {
        if (empty($this->identity->getId())) {
            throw new InvalidIdException($this->identity);
        }

        $model = $this->findModel();

        if ($model instanceof StoredLeadInterface) {
            return $model->getLead();
        }

        return null;
    }

    /**
     * @return StoredLeadInterface|null
     */
    private function findModel()
    {
        return $this->storeLeadModel->find()
            ->andWhere(['=', 'user_id', $this->identity->getId()])
            ->one();
    }
}