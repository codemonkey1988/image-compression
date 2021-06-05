<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Task;

use Codemonkey1988\ImageCompression\Service\CompressionService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class CompressTask extends AbstractTask
{
    /**
     * @var int
     */
    public $filesPerRun;

    /**
     * @var bool
     */
    public $compressOriginal;

    /**
     * @var bool
     */
    public $compressProcessed;

    /**
     * @var string
     */
    public $supportedExtensions;

    /**
     * @var int
     */
    protected $remainingFiles;

    /**
     * @var CompressionService
     */
    protected $compressionService;

    /**
     * Executes the task.
     *
     * @return bool
     */
    public function execute(): bool
    {
        $this->remainingFiles = $this->filesPerRun;

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->compressionService = $objectManager->get(CompressionService::class);

        $this->processOriginalFiles();
        $this->processProcessedFiles();

        return true;
    }

    protected function processOriginalFiles()
    {
        $supportedExtension = GeneralUtility::trimExplode(',', $this->supportedExtensions);

        if ($this->remainingFiles <= 0 || !$this->compressOriginal || empty($supportedExtension)) {
            return;
        }

        $files = $this->compressionService->getUncompressedOriginalFiles($supportedExtension, $this->remainingFiles);

        if ($files) {
            /** @var File $file */
            foreach ($files as $file) {
                $this->compressionService->compress($file);
                $this->remainingFiles--;
            }
        }
    }

    protected function processProcessedFiles()
    {
        $supportedExtension = GeneralUtility::trimExplode(',', $this->supportedExtensions);

        if ($this->remainingFiles <= 0 || !$this->compressProcessed || empty($supportedExtension)) {
            return;
        }

        $files = $this->compressionService->getUncompressedProcessedFiles($supportedExtension, $this->remainingFiles);

        if ($files) {
            /** @var File $file */
            foreach ($files as $file) {
                $this->compressionService->compress($file);
                $this->remainingFiles--;
            }
        }
    }
}
