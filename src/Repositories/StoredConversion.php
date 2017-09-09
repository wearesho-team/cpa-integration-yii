<?php

namespace Wearesho\Cpa\Yii\Repositories;


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
class StoredConversion extends ActiveRecord implements StoredConversionInterface
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return "conversion";
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
     * @return StoredConversion
     */
    public function setConversion(ConversionInterface $conversion): self
    {
        $this->id = $conversion->getId();
        $this->type = get_class($conversion);
        $this->conversion_serialized = $conversion;

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
     * @return StoredConversion
     */
    public function setResponse(ResponseInterface $response): self
    {
        $this->response_serialized = $response;
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