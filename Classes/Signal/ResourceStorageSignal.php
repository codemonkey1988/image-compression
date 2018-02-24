<?php
namespace Codemonkey1988\ImageCompression\Signal;

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
