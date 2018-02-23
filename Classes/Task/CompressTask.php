<?php
namespace Codemonkey1988\ImageCompression\Task;

/***************************************************************
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Codemonkey1988\ImageCompression\Resource\FileRepository;
use Codemonkey1988\ImageCompression\Resource\ProcessedFileRepository;
use Codemonkey1988\ImageCompression\Service\CompressionService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class CompressTask
 * Compresses all not compressed images in the system.
 *
 * @package Codemonkey1988\ImageCompression\Task
 * @author Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressTask extends AbstractTask
{
    /**
     * @var int
     */
    protected $filesPerRun;

    /**
     * @var CompressionService
     */
    protected $compressionService;

    /**
     * CompressTask constructor.
     */
    public function __construct()
    {
        parent::__construct();

        /** @vatschreinerr ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->compressionService = $objectManager->get(CompressionService::class);
    }

    /**
     * Executes the task.
     *
     * @return boolean
     */
    public function execute()
    {
        $this->filesPerRun = (int)$this->files_per_run;

        $this->processOriginalFiles();
        $this->processProcessedFiles();

        return true;
    }

    /**
     * @return void
     */
    protected function processOriginalFiles()
    {
        if ($this->filesPerRun <= 0 || empty($this->compress_original)) {
            return;
        }

        /** @var FileRepository $fileRepository */
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $files = $fileRepository->findByImageCompressionStatus(FileRepository::IMAGE_COMPRESSION_NOT_PROCESSED, (int)$this->filesPerRun);

        if ($files) {
            /** @var File $file */
            foreach ($files as $file) {
                $this->compressionService->compress($file);

                $this->filesPerRun--;
            }
        }
    }

    /**
     * @return void
     */
    protected function processProcessedFiles()
    {
        if ($this->filesPerRun <= 0 || empty($this->compress_processed)) {
            return;
        }

        /** @var ProcessedFileRepository $processedFileRepository */
        $processedFileRepository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
        $files = $processedFileRepository->findByImageCompressionStatus(FileRepository::IMAGE_COMPRESSION_NOT_PROCESSED, (int)$this->filesPerRun);

        if ($files) {
            /** @var File $file */
            foreach ($files as $file) {
                $this->compressionService->compress($file);

                $this->filesPerRun--;
            }
        }
    }
}