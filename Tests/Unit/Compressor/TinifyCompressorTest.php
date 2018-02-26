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
use Codemonkey1988\ImageCompression\Service\CompressionLogService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Resource\File;

class TinifyCompressorTest extends UnitTestCase
{
    /**
     * Tests if a file can be compressed.
     *
     * @test
     */
    public function testIfFileCanCompressed()
    {
        $logServiceMock = $this->getAccessibleMock(CompressionLogService::class, ['count']);
        $logServiceMock->expects($this->once())->method('count')->willReturn(10); // current images compressed

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('jpg');

        $mockedTinifyCompressor = $this->getMockedTinifyCompressor($logServiceMock);

        $this->assertTrue($mockedTinifyCompressor->_callRef('canCompress', $fileMock));
    }

    /**
     * Tests if a file can not be compressed because of wrong file extension.
     *
     * @test
     */
    public function testIfFileCannotCompressedWrongExtension()
    {
        $logServiceMock = $this->getAccessibleMock(CompressionLogService::class, ['count']);
        $logServiceMock->expects($this->once())->method('count')->willReturn(10); // current images compressed

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('gif');

        $mockedTinifyCompressor = $this->getMockedTinifyCompressor($logServiceMock);

        $this->assertFalse($mockedTinifyCompressor->_callRef('canCompress', $fileMock));
    }

    /**
     * Tests if a file can not be compressed because the maximum file size exceeded.
     *
     * @test
     */
    public function testIfFileCannotCompressedExceededCount()
    {
        $logServiceMock = $this->getAccessibleMock(CompressionLogService::class, ['count']);
        $logServiceMock->expects($this->once())->method('count')->willReturn(600); // current images compressed

        $fileMock = $this->getAccessibleMock(File::class, ['getExtension'], [], '', false);
        $fileMock->expects($this->once())->method('getExtension')->willReturn('jpg');

        $mockedTinifyCompressor = $this->getMockedTinifyCompressor($logServiceMock);

        $this->assertFalse($mockedTinifyCompressor->_callRef('canCompress', $fileMock));
    }

    protected function getMockedTinifyCompressor($logServiceMock)
    {
        $mockedTinifyCompressor = $this->getAccessibleMock(TinifyCompressor::class, ['getApiKey', 'getCompressionCount', 'getSupportedExtensions']);
        $mockedTinifyCompressor->expects($this->once())->method('getApiKey')->willReturn('mytestapikey');
        $mockedTinifyCompressor->expects($this->any())->method('getCompressionCount')->willReturn(500);
        $mockedTinifyCompressor->expects($this->once())->method('getSupportedExtensions')->willReturn(['jpg', 'jpeg', 'png']);
        $mockedTinifyCompressor->_set('compressionLogService', $logServiceMock);

        return $mockedTinifyCompressor;
    }
}
