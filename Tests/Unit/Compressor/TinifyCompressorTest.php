<?php
declare(strict_types=1);
namespace Codemonkey1988\ImageCompression\Tests\Unit\Compressor;

/*
 * This file is part of the TYPO3 responsive images project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 *
 */

use Codemonkey1988\ImageCompression\Compressor\TinifyCompressor;
use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;

class TinifyCompressorTest extends UnitTestCase
{
    /**
     * @var ConfigurationService
     */
    protected $mockedConfigurationService;

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockedConfigurationService = $this->getAccessibleMock(
            ConfigurationService::class,
            ['getTinifySupportedExtensions', 'getTinifyMaxMonthlyCompressionCount', 'getTinifyApiKey']
        );
        $this->mockedConfigurationService->method('getTinifyApiKey')->willReturn('mytestapikey');
        $this->mockedConfigurationService->method('getTinifyMaxMonthlyCompressionCount')->willReturn(500);
    }

    /**
     * Tests if a file can be compressed.
     *
     * @test
     * @throws \PHPUnit\Framework\Exception
     */
    public function testIfFileCanCompressed()
    {
        $storageMock = $this->getAccessibleMock(ResourceStorage::class, ['getUid', 'getDriverType'], [], '', false);
        $storageMock->expects($this->once())->method('getDriverType')->willReturn('Local');
        $storageMock->expects($this->once())->method('getUid')->willReturn(1);

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->setStorage($storageMock);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('jpg');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->method('getCurrentCompressionCount')->willReturn(0);

        $this->assertTrue($tinifyCompressorMock->canCompress($fileMock));
    }

    /**
     * Tests if a file can not be compressed because of wrong file extension.
     *
     * @test
     * @throws \PHPUnit\Framework\Exception
     */
    public function testIfFileCannotCompressedWrongExtension()
    {
        $storageMock = $this->getAccessibleMock(ResourceStorage::class, ['getUid', 'getDriverType'], [], '', false);
        $storageMock->expects($this->never())->method('getDriverType')->willReturn('Local');
        $storageMock->expects($this->once())->method('getUid')->willReturn(1);

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->setStorage($storageMock);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('gif');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->expects($this->once())->method('getCurrentCompressionCount')->willReturn(0);

        $this->assertFalse($tinifyCompressorMock->canCompress($fileMock));
    }

    /**
     * Tests if a file can not be compressed because of too many compressions.
     *
     * @test
     * @throws \PHPUnit\Framework\Exception
     */
    public function testIfFileCannotCompressedMaxCompressionsExceeded()
    {
        $storageMock = $this->getAccessibleMock(ResourceStorage::class, ['getUid', 'getDriverType'], [], '', false);
        $storageMock->expects($this->never())->method('getDriverType')->willReturn('Local');
        $storageMock->expects($this->once())->method('getUid')->willReturn(1);

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension', 'getStorage'], [], '', false);
        $fileMock->setStorage($storageMock);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('jpg');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->expects($this->once())->method('getCurrentCompressionCount')->willReturn(9999);

        $this->assertFalse($tinifyCompressorMock->canCompress($fileMock));
    }
}
