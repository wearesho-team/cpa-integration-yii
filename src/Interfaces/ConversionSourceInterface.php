<?php

namespace Wearesho\Cpa\Yii\Interfaces;

use Wearesho\Cpa\Interfaces\LeadInterface;

/**
 * Interface ConversionSourceInterface
 * @package Wearesho\Cpa\Yii\Interfaces
 */
interface ConversionSourceInterface
{
    /**
     * This ID will be used to convert lead to conversion
     * @see LeadInterface::createConversion()
     * @return int
     */
    public function getConversionId(): int;
}