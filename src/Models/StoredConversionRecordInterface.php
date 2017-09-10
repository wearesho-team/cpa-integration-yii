<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 9/10/17
 * Time: 4:05 PM
 */

namespace Wearesho\Cpa\Yii\Models;


use Psr\Http\Message\ResponseInterface;
use Wearesho\Cpa\Interfaces\ConversionInterface;
use Wearesho\Cpa\Interfaces\StoredConversionInterface;
use yii\db\ActiveRecordInterface;

/**
 * Interface StoredConversionRecordInterface
 * @package Wearesho\Cpa\Yii\Models
 */
interface StoredConversionRecordInterface extends ActiveRecordInterface, StoredConversionInterface
{
    /**
     * @param ResponseInterface $response
     * @return StoredConversionRecordInterface
     */
    public function setResponse(ResponseInterface $response): StoredConversionRecordInterface;

    /**
     * @param ConversionInterface $conversion
     * @return StoredConversionRecordInterface
     */
    public function setConversion(ConversionInterface $conversion): StoredConversionRecordInterface;
}