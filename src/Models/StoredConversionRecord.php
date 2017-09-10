<?php

namespace Wearesho\Cpa\Yii\Models;


use yii\db\ActiveRecord;

use Psr\Http\Message\ResponseInterface;

use Wearesho\Cpa\Interfaces\ConversionInterface;
use Wearesho\Cpa\Interfaces\StoredConversionInterface;

/**
 * Class StoredConversion
 * @package Wearesho\Cpa\Yii\Repositories
 *
 * @property integer $id
 * @property string $type
 * @property-read string $conversion_serialized
 * @property-read string $response_serialized
 */
class StoredConversionRecord extends ActiveRecord implements StoredConversionRecordInterface
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return "stored_conversion";
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'type', 'conversion_serialized', 'response_serialized'], 'required'],
            ['id', 'integer', 'min' => 1,],
            [['type', 'conversion_serialized', 'response_serialized'], 'string',],
            [['id', 'type'], 'unique', 'targetAttribute' => ['id', 'type',]],
        ];
    }

    /**
     * @param ConversionInterface $conversion
     * @return StoredConversionRecordInterface
     */
    public function setConversion(ConversionInterface $conversion): StoredConversionRecordInterface
    {
        $this->id = $conversion->getId();
        $this->type = get_class($conversion);
        $this->conversion_serialized = serialize($conversion);

        return $this;
    }

    /**
     * @return ConversionInterface
     */
    public function getConversion(): ConversionInterface
    {
        return unserialize($this->conversion_serialized);
    }

    /**
     * @param ResponseInterface $response
     * @return StoredConversionRecordInterface
     */
    public function setResponse(ResponseInterface $response): StoredConversionRecordInterface
    {
        $this->response_serialized = serialize($response);
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return unserialize($this->response_serialized);
    }
}