<?php
declare(strict_types=1);
namespace Codemonkey1988\ImageCompression\Tests\Functional\Service;

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
use Codemonkey1988\ImageCompression\Resource\FileRepository;
use Codemonkey1988\ImageCompression\Resource\ProcessedFileRepository;
use Codemonkey1988\ImageCompression\Service\CompressionService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Test class for \Codemonkey1988\ImageCompression\Service\CompressionService
 */
class CompressionServiceTest extends FunctionalTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/image_compression'];

    /**
     * @var string
     */
    protected $backendUserFixture = __DIR__ . '/../Fixtures/be_users.xml';

    /**
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function setUp()
    {
        parent::setUp();
        $this->setUpBackendUserFromFixture(1);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $GLOBALS['LANG']->init('en');
    }

    /**
     * Test if uncompressed original images can be determined.
     *
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function testUpdateCompressionStatusForOriginalFile()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_metadata.xml');

        /** @var CompressionService $compressionService */
        $compressionService = $this->objectManager->get(CompressionService::class);

        $files = $compressionService->getUncompressedOriginalFiles(99);

        $this->assertEquals(5, count($files), 'There must be exactly 5 images that are not compressed');

        // Compress the first image.
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression']['compressors'] = [$this->getCompressorMock()];

        $compressionService->compress($files[0]);

        $newFiles = $compressionService->getUncompressedOriginalFiles(99);
        $this->assertEquals(4, count($newFiles), 'There must be exactly 3 images that are not compressed');
    }

    /**
     * Test if uncompressed processed images can be determined.
     *
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function testUpdateCompressionStatusForProcessedFile()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_metadata.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_processedfile.xml');

        /** @var CompressionService $compressionService */
        $compressionService = $this->objectManager->get(CompressionService::class);

        $files = $compressionService->getUncompressedProcessedFiles(99);

        $this->assertEquals(1, count($files), 'There must be exactly 1 image that is not compressed');

        // Compress the first image.
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression']['compressors'] = [$this->getCompressorMock()];

        $compressionService->compress($files[0]);

        $newFiles = $compressionService->getUncompressedProcessedFiles(99);
        $this->assertEquals(0, count($newFiles), 'There must be noimage that is not compressed');
    }

    /**
     * Test if uncompressed images can be found and get updated after compression try.
     *
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function testUpdateCheckedStatusForFiles()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_metadata.xml');

        /** @var CompressionService $compressionService */
        $compressionService = $this->objectManager->get(CompressionService::class);

        $files = $compressionService->getUncompressedOriginalFiles(99);

        $this->assertEquals(5, count($files), 'There must be exactly 5 images that are not compressed');

        // Compress the first image.
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression']['compressors'] = [$this->getCompressorMock()];

        foreach ($files as $file) {
            $compressionService->compress($file);
        }

        $newFiles = $compressionService->getUncompressedOriginalFiles(99);
        $this->assertEquals(0, count($newFiles), 'There must be no image that is not compressed');
    }

    /**
     * @return \Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCompressorMock()
    {
        $tinifyCompressorMock = $this->getAccessibleMock(TinifyCompressor::class, ['canCompress', 'compress']);
        $tinifyCompressorMock->expects($this->any())->method('canCompress')->willReturn(true);
        $tinifyCompressorMock->expects($this->any())->method('compress')->willReturn(true);

        return $tinifyCompressorMock;
    }
}
