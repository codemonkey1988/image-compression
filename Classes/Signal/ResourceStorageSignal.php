<?php
declare(strict_types=1);
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

use Codemonkey1988\ImageCompression\Service\CompressionService;
use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;

/**
 * Class ResourceStorageSignal
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
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
     * @return void
     */
    public function injectCompressionService(CompressionService $compressionService)
    {
        $this->compressionService = $compressionService;
    }

    /**
     * @param ConfigurationService $configurationService
     * @return void
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
     * @return void
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
     * @return void
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
