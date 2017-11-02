<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


use Salamek\PplMyApi\Enum\LabelDecomposition;
use Salamek\PplMyApi\Model\Package;
use Salamek\PplMyApi\Tools;
use Salamek\PplMyApi\Label;

final class PublicTest extends BaseTest
{
    /**
     * @test
     */
    public function testIsHealthy()
    {
        $this->assertInternalType('boolean', $this->pplMyApi->isHealthy());
    }

    /**
     * @test
     */
    public function testGetVersion()
    {
        $this->assertRegExp('/^\d{1,2}\.\d{1,2}\.\d+\.\d+$/', $this->pplMyApi->getVersion());
    }

    /**
     * @test
     */
    public function testPackageNumber()
    {
        $this->assertEquals('40950000115', $this->package->getPackageNumber());
    }

    /**
     * @test
     */
    public function testGeneratePackageNumber()
    {
        $packageNumberInfo = new \Salamek\PplMyApi\Model\PackageNumberInfo(
            114,
            \Salamek\PplMyApi\Enum\Product::PPL_PARCEL_CZ_PRIVATE,
            \Salamek\PplMyApi\Enum\Depo::CODE_09
        );
        $this->assertEquals('40950000114', Tools::generatePackageNumber($packageNumberInfo));
    }

    /**
     * @test
     */
    public function testParsePackageNumber()
    {
        $packageNumberInfo = Tools::parsePackageNumber('40950000114');
        
        $this->assertEquals('4', $packageNumberInfo->getProductType());
        $this->assertEquals('000114', $packageNumberInfo->getSeriesNumberId());
        $this->assertEquals('09', $packageNumberInfo->getDepoCode());
        $this->assertFalse($packageNumberInfo->isCod());
    }

    /**
     * @test
     * @throws Exception
     */
    public function testGetParcelShops()
    {
        $this->assertNotEmpty($this->pplMyApi->getParcelShops());
    }

    /**
     * @test
     */
    public function testGetSprintRoutes()
    {
        $this->assertNotEmpty($this->pplMyApi->getSprintRoutes());
    }

    /**
     * @test
     */
    public function testGeneratePdfFullSinglePackage()
    {
        $raw = Label::generateLabels([$this->package], LabelDecomposition::FULL);

        $this->assertNotEmpty($raw);

        $filePath = __DIR__ . '/../tmp/' . $this->package->getPackageNumber() . '-full.pdf';

        file_put_contents($filePath, $raw);

        $this->assertFileExists($filePath);
    }

    /**
     * @test
     */
    public function testGeneratePdfFullMultiplePackages()
    {
        $raw = Label::generateLabels($this->packages, LabelDecomposition::FULL);

        $this->assertNotEmpty($raw);

        $packageNumbers = [];

        /** @var Package $package */
        foreach ($this->packages AS $package) {
            $packageNumbers[] = $package->getPackageNumber();
        }

        $filePath = __DIR__ . '/../tmp/' . implode('-', $packageNumbers) . '-full.pdf';

        file_put_contents($filePath, $raw);

        $this->assertFileExists($filePath);
    }

    /**
     * @test
     */
    public function testGeneratePdfQuarterSinglePackage()
    {
        $raw = Label::generateLabels([$this->package], LabelDecomposition::QUARTER);

        $this->assertNotEmpty($raw);

        $filePath = __DIR__ . '/../tmp/' . $this->package->getPackageNumber() . '-quarter.pdf';

        file_put_contents($filePath, $raw);

        $this->assertFileExists($filePath);
    }

    /**
     * @test
     */
    public function testGeneratePdfQuarterMultiplePackages()
    {
        $raw = Label::generateLabels($this->packages, LabelDecomposition::QUARTER);

        $this->assertNotEmpty($raw);

        $packageNumbers = [];

        /** @var Package $package */
        foreach ($this->packages AS $package) {
            $packageNumbers[] = $package->getPackageNumber();
        }

        $filePath = __DIR__ . '/../tmp/' . implode('-', $packageNumbers) . '-quarter.pdf';

        file_put_contents($filePath, $raw);

        $this->assertFileExists($filePath);
    }
}