<?php
declare(strict_types=1);
namespace Codemonkey1988\ImageCompression\Task;

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

use Codemonkey1988\ImageCompression\Service\CompressionService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class CompressTask
 * Compresses all not compressed images in the system.
 *
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
     * @return bool
     */
    public function execute(): bool
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

        $files = $this->compressionService->getUncompressedOriginalFiles($this->filesPerRun);

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

        $files = $this->compressionService->getUncompressedProcessedFiles($this->filesPerRun);

        if ($files) {
            /** @var File $file */
            foreach ($files as $file) {
                $this->compressionService->compress($file);
                $this->filesPerRun--;
            }
        }
    }
}
