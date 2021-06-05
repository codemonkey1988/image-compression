<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Signal;

use Codemonkey1988\ImageCompression\Service\CompressionService;
use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;

class ResourceStorageSignal
{
    /**
     * @var CompressionService
     */
    protected $compressionService;
    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @param CompressionService $compressionService
     */
    public function injectCompressionService(CompressionService $compressionService)
    {
        $this->compressionService = $compressionService;
    }

    /**
     * @param ConfigurationService $configurationService
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * Signal when a new file has been added.
     *
     * @param File $file
     * @param Folder $folder
     */
    public function postFileAdd(File $file, Folder $folder)
    {
        if ($this->configurationService->isCompressOnUploadEnabled()) {
            try {
                $this->compressionService->compress($file);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Signal when a new file has been added.
     *
     * @param File $file
     * @param string $tmpName
     */
    public function postFileReplace(File $file, string $tmpName)
    {
        if ($this->configurationService->isCompressOnUploadEnabled()) {
            try {
                $this->compressionService->compress($file);
            } catch (\Exception $e) {
            }
        }
    }
}
