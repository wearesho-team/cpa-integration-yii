<?php

namespace Wearesho\Cpa\Yii\Tests\Repositories;

use GuzzleHttp\Psr7\Response;
use Wearesho\Cpa\Exceptions\DuplicatedConversionException;
use Wearesho\Cpa\Yii\Repositories\ConversionRepository;
use Wearesho\Cpa\Yii\Tests\AbstractTestCase;

use Wearesho\Cpa\SalesDoubler\Conversion as SalesDoublerConversion;
use Wearesho\Cpa\SalesDoubler\Lead as SalesDoublerLead;

class ConversionRepositoryTest extends AbstractTestCase
{
    /** @var  ConversionRepository */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = new ConversionRepository();
    }

    public function testSaving()
    {
        $conversion = new SalesDoublerConversion(new SalesDoublerLead(1), 1);
        $response = new Response();
        $storedConversion = $this->repository->push($conversion, $response);

        $this->assertEquals(
            $conversion,
            $storedConversion->getConversion(),
            "Repository should pass conversion instance to StoredConversion"
        );
        $this->assertEquals(
            $response,
            $storedConversion->getResponse(),
            "Repository should pass response instance to StoredConversion"
        );

        $loadedConversion = $this->repository->pull(
            $conversion->getId(),
            get_class($conversion)
        );
        $this->assertEquals(
            $storedConversion->getConversion(),
            $loadedConversion->getConversion(),
            "Repository should load conversion instance"
        );
        $this->assertEquals(
            $storedConversion->getResponse(),
            $loadedConversion->getResponse(),
            "Repository should load response instance"
        );
    }

    public function testDuplication()
    {
        $conversion = new SalesDoublerConversion(new SalesDoublerLead(1), 1);
        $this->repository->push($conversion, new Response());

        $this->expectException(DuplicatedConversionException::class);
        $this->repository->push($conversion, new Response());
    }
}