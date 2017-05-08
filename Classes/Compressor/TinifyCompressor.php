<?php

namespace Codemonkey1988\ImageCompression\Compressor;

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

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Class CompressionService
 *
 * @package Codemonkey1988\ImageCompression\Service
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class TinifyCompressor implements CompressorInterface
{
    /**
     * @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility
     */
    protected $configurationUtility;
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var \Codemonkey1988\ImageCompression\Service\CompressionLogService
     */
    protected $compressionLogService;

    /**
     * @param ConfigurationUtility $configurationUtility
     * @return void
     */
    public function injectConfigurationUtility(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility)
    {
        $this->configurationUtility = $configurationUtility;
        $this->configuration        = $this->configurationUtility->getCurrentConfiguration('image_compression');
    }

    /**
     * @param \Codemonkey1988\ImageCompression\Service\CompressionLogService $compressionLogService
     * @return void
     */
    public function injectCompressionLogService(\Codemonkey1988\ImageCompression\Service\CompressionLogService $compressionLogService)
    {
        $this->compressionLogService = $compressionLogService;
    }

    /**
     * @return void
     */
    public function initializeObject()
    {
        \Tinify\setKey($this->getApiKey());
        \Tinify\setAppIdentifier('t3_image_compression');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tinify';
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function canCompress(FileInterface $file)
    {
        $from = new \DateTime('first day of this month 00:00:01');
        $to = new \DateTime('last day of this month 23:59:59');
        $compressionCount = $this->compressionLogService->count($from, $to);

        return $this->getApiKey()
            && (in_array(
                $file->getExtension(),
                $this->getSupportedExtensions()
            ))
            && ($compressionCount < $this->getCompressionCount());
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function compress(FileInterface $file)
    {
        try {
            $publicUrl  = PATH_site . $file->getPublicUrl();
            $sourceFile = \Tinify\fromFile($publicUrl);
            $sourceFile->toFile($publicUrl);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    protected function getApiKey()
    {
        if (is_array($this->configuration) && isset($this->configuration['tinifyApiKey']['value'])) {
            return $this->configuration['tinifyApiKey']['value'];
        }

        return '';
    }

    /**
     * @return int
     */
    protected function getCompressionCount()
    {
        if (is_array($this->configuration) && isset($this->configuration['tinifyCompressionCount']['value'])) {
            return (int)$this->configuration['tinifyCompressionCount']['value'];
        }

        return 0;
    }

    /**
     * @return array
     */
    protected function getSupportedExtensions()
    {
        if (is_array($this->configuration) && isset($this->configuration['tinifyExtensions']['value'])) {
            return GeneralUtility::trimExplode(',', $this->configuration['tinifyExtensions']['value']);
        }

        return [];
    }
}