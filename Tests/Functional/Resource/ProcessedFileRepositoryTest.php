<?php
declare(strict_types=1);
namespace Codemonkey1988\ImageCompression\Tests\Functional\Resource;

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

use Codemonkey1988\ImageCompression\Resource\ProcessedFileRepository;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Test class for \Codemonkey1988\ImageCompression\Resource\ProcessedFileRepository
 */
class ProcessedFileRepositoryTest extends FunctionalTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ProcessedFileRepository
     */
    protected $processedFileRepository;

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
        $this->processedFileRepository = $this->objectManager->get(ProcessedFileRepository::class);
    }

    /**
     * Test if one uncompressed image can be determined.
     *
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function findUncompressedImage()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_metadata.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_processedfile.xml');

        $files = $this->processedFileRepository->findUncompressedImages(1);

        $this->assertTrue(is_array($files) && count($files) === 1, 'There is more or less than one image');
        $this->assertEquals(get_class($files[0]), ProcessedFile::class, 'The first entry of $files is not of object type ProcessedFile');
        $this->assertEquals($files[0]->getUid(), 10, 'The first image does not have the uid 1');
    }

    /**
     * Test if uncompressed images can be determined.
     *
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function findUncompressedImages()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_metadata.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_processedfile.xml');

        $files = $this->processedFileRepository->findUncompressedImages(99);

        $this->assertEquals(1, count($files));
    }

    /**
     * Test if uncompressed images can be determined if there aren't any.
     *
     * @test
     */
    public function findUncompressedImagesNoResult()
    {
        $files = $this->processedFileRepository->findUncompressedImages(1);

        $this->assertTrue(is_array($files) && count($files) === 0, 'One or more images are found');
    }
}
