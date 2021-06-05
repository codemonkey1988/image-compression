<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Tests\Unit\Compressor;

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
        $storageMock->expects(self::once())->method('getDriverType')->willReturn('Local');
        $storageMock->expects(self::once())->method('getUid')->willReturn(1);

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->setStorage($storageMock);
        $fileMock->expects(self::once())->method('getExtension')->willReturn('jpg');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->method('getCurrentCompressionCount')->willReturn(0);

        self::assertTrue($tinifyCompressorMock->canCompress($fileMock));
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
        $storageMock->expects(self::never())->method('getDriverType')->willReturn('Local');
        $storageMock->expects(self::once())->method('getUid')->willReturn(1);

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->setStorage($storageMock);
        $fileMock->expects(self::once())->method('getExtension')->willReturn('gif');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->expects(self::once())->method('getCurrentCompressionCount')->willReturn(0);

        self::assertFalse($tinifyCompressorMock->canCompress($fileMock));
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
        $storageMock->expects(self::never())->method('getDriverType')->willReturn('Local');
        $storageMock->expects(self::once())->method('getUid')->willReturn(1);

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension', 'getStorage'], [], '', false);
        $fileMock->setStorage($storageMock);
        $fileMock->expects(self::once())->method('getExtension')->willReturn('jpg');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->expects(self::once())->method('getCurrentCompressionCount')->willReturn(9999);

        self::assertFalse($tinifyCompressorMock->canCompress($fileMock));
    }
}
