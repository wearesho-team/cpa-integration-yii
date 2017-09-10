<?php
/**
 * Created by PhpStorm.
 * User: horat1us
 * Date: 9/10/17
 * Time: 9:39 AM
 */

namespace Wearesho\Cpa\Yii\Tests;


use PHPUnit\Framework\TestCase;
use Wearesho\Cpa\Interfaces\LeadRepositoryInterface;
use Wearesho\Cpa\Lead\LeadMemoryRepository;

/**
 * Class LeadBehaviorTest
 * @package Wearesho\Cpa\Yii\Tests
 */
class LeadBehaviorTest extends TestCase
{
    /** @var  LeadRepositoryInterface */
    protected $repository;


    protected function setUp()
    {
        parent::setUp();
        $this->repository = new LeadMemoryRepository();
    }
}