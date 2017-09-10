<?php

namespace Wearesho\Cpa\Yii\Repositories;


use Psr\Http\Message\ResponseInterface;

use Wearesho\Cpa\Exceptions\DuplicatedConversionException;
use Wearesho\Cpa\Interfaces\ConversionInterface;
use Wearesho\Cpa\Interfaces\ConversionRepositoryInterface;
use Wearesho\Cpa\Interfaces\StoredConversionInterface;

use Wearesho\Cpa\Yii\Exceptions\ValidationException;
use Wearesho\Cpa\Yii\Models\StoredConversionRecord;
use Wearesho\Cpa\Yii\Models\StoredConversionRecordInterface;
use yii\base\Model;

/**
 * Class ConversionRepository
 * @package Wearesho\Cpa\Yii\Repositories
 */
class ConversionRepository implements ConversionRepositoryInterface
{
    /** @var  StoredConversionRecordInterface */
    protected $storeConversionModel;

    /**
     * ConversionRepository constructor.
     * @param StoredConversionRecordInterface $conversionRecord
     */
    public function __construct(StoredConversionRecordInterface $conversionRecord = null)
    {
        $this->storeConversionModel = $conversionRecord ?? new StoredConversionRecord;
    }

    /**
     * Saving sent conversion in storage
     *
     * @param ConversionInterface $conversion
     * @param ResponseInterface $response
     *
     * @throws DuplicatedConversionException
     * @throws ValidationException
     *
     * @return StoredConversionInterface
     */
    public function push(ConversionInterface $conversion, ResponseInterface $response): StoredConversionInterface
    {
        $storedConversionClass = get_class($this->storeConversionModel);

        /** @var StoredConversionRecordInterface $storedConversion */
        $storedConversion = new $storedConversionClass;
        $storedConversion->setConversion($conversion);
        $storedConversion->setResponse($response);

        if (!$storedConversion->save() && $storedConversion instanceof Model) {
            if ($storedConversion->hasErrors('type')) {
                throw new DuplicatedConversionException($conversion);
            }
            // @codeCoverageIgnoreStart
            throw new ValidationException($storedConversion);
            // @codeCoverageIgnoreEnd
        }

        return $storedConversion;
    }

    /**
     * @param $conversionId
     * @param string $type Class name that extends ConversionInterface
     *
     * @return null|StoredConversionInterface
     */
    public function pull($conversionId, string $type)
    {
        return $this->storeConversionModel->find()
            ->andWhere(['=', 'type', $type])
            ->andWhere(['=', 'id', $conversionId])
            ->one();
    }
}