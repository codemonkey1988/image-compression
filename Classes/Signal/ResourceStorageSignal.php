<?php
namespace Codemonkey1988\ImageCompression\Signal;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Class ResourceStorageSignal
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ResourceStorageSignal
{
    /**
     * @var \Codemonkey1988\ImageCompression\Service\CompressionService
     */
    protected $compressionService;
    /**
     * @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility
     */
    protected $configurationUtility;

    /**
     * @param \Codemonkey1988\ImageCompression\Service\CompressionService $compressionService
     * @return void
     */
    public function injectCompressionService(\Codemonkey1988\ImageCompression\Service\CompressionService $compressionService)
    {
        $this->compressionService = $compressionService;
    }

    /**
     * @param ConfigurationUtility $configurationUtility
     * @return void
     */
    public function injectConfigurationUtility(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility)
    {
        $this->configurationUtility = $configurationUtility;
    }

    /**
     * Signal when a new file has been added.
     *
     * @param File $file
     * @param Folder $folder
     * @return void
     */
    public function postFileAdd(File $file, Folder $folder)
    {
        if ($this->compressOnUploadEnabled()) {
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
     * @return void
     */
    public function postFileReplace(File $file, $tmpName)
    {
        if ($this->compressOnUploadEnabled()) {
            try {
                $this->compressionService->compress($file);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @return bool
     */
    protected function compressOnUploadEnabled()
    {
        $configuration = $this->configurationUtility->getCurrentConfiguration('image_compression');

        if (isset($configuration['enableCompressOnUpload']['value'])) {
            return (int)$configuration['enableCompressOnUpload']['value'] === 1;
        }

        return false;
    }
}
