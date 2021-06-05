<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Tests\Functional\Resource;

use Codemonkey1988\ImageCompression\Resource\FileRepository;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FileRepositoryTest extends FunctionalTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var FileRepository
     */
    protected $fileRepository;

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
        $this->fileRepository = $this->objectManager->get(FileRepository::class);
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

        $files = $this->fileRepository->findUncompressedImages(['jpg', 'jpeg', 'png'], 1);

        self::assertEquals(1, count($files), 'There is more or less than one image');
        self::assertEquals(get_class($files[0]), File::class, 'The first entry of $files is not of object type File');
        self::assertEquals($files[0]->getUid(), 1, 'The first image does not have the uid 1');
    }

    /**
     * Test if all uncompressed jpg images can be determined.
     *
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function findUncompressedJpgImages()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_file_metadata.xml');

        $files = $this->fileRepository->findUncompressedImages(['jpg'], 99);

        self::assertEquals(2, count($files), 'There are more or less than two images. Expected 2, actual ' . count($files));
    }

    /**
     * Test if uncompressed images can be determined if there aren't any.
     *
     * @test
     */
    public function findUncompressedImagesNoResult()
    {
        $files = $this->fileRepository->findUncompressedImages(['jpg', 'jpeg', 'png'], 1);

        self::assertTrue(is_array($files) && count($files) === 0, 'One or more images are found');
    }
}
