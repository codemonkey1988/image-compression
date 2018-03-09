<?php
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
        $this->mockedConfigurationService->method('getTinifySupportedExtensions')->willReturn(['jpg', 'jpeg', 'png']);
    }

    /**
     * Tests if a file can be compressed.
     *
     * @test
     * @throws \PHPUnit\Framework\Exception
     */
    public function testIfFileCanCompressed()
    {
        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
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
        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('gif');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->method('getCurrentCompressionCount')->willReturn(0);

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
        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('jpg');

        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['getCurrentCompressionCount']);
        $tinifyCompressorMock->injectConfigurationService($this->mockedConfigurationService);
        $tinifyCompressorMock->method('getCurrentCompressionCount')->willReturn(9999);

        $this->assertFalse($tinifyCompressorMock->canCompress($fileMock));
    }
}
