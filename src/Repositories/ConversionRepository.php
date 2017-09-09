<?php

namespace Wearesho\Cpa\Yii\Repositories;


use Psr\Http\Message\ResponseInterface;

use Wearesho\Cpa\Exceptions\DuplicatedConversionException;
use Wearesho\Cpa\Interfaces\ConversionInterface;
use Wearesho\Cpa\Interfaces\ConversionRepositoryInterface;
use Wearesho\Cpa\Interfaces\StoredConversionInterface;
use Wearesho\Cpa\Yii\Exceptions\ValidationException;

/**
 * Class ConversionRepository
 * @package Wearesho\Cpa\Yii\Repositories
 */
class ConversionRepository implements ConversionRepositoryInterface
{

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
        $storedConversion = new StoredConversion();
        $storedConversion->setConversion($conversion);
        $storedConversion->setResponse($response);

        if (!$storedConversion->save()) {
            if ($storedConversion->hasErrors('type')) {
                throw new DuplicatedConversionException($conversion);
            }
            throw new ValidationException($storedConversion);
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
        return StoredConversion::find()
            ->andWhere(['=', 'type', $type])
            ->andWhere(['=', 'id', $conversionId])
            ->one();
    }
}