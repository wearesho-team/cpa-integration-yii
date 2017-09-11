<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 9/11/17
 * Time: 11:19 AM
 */

namespace Wearesho\Cpa\Yii\Tests\Mocks;


use Wearesho\Cpa\Yii\Interfaces\ConversionSourceInterface;

/**
 * Class ConversionSourceMock
 * @package Wearesho\Cpa\Yii\Tests\Mocks
 */
class ConversionSourceMock implements ConversionSourceInterface
{
    /** @var int */
    protected $id;

    /**
     * ConversionSourceMock constructor.
     * @param int $conversionId
     */
    public function __construct(int $conversionId)
    {
        $this->id = $conversionId;
    }

    /**
     * This ID will be used to convert lead to conversion
     * @see LeadInterface::createConversion()
     * @return int
     */
    public function getConversionId(): int
    {
        return $this->id;
    }
}