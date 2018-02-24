<?php
namespace Codemonkey1988\ImageCompression\Compressor;

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

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Class CompressionService
 *
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
     * @param \Codemonkey1988\ImageCompression\Service\CompressionLogService $compressionLogService
     * @return void
     */
    public function injectCompressionLogService(\Codemonkey1988\ImageCompression\Service\CompressionLogService $compressionLogService)
    {
        $this->compressionLogService = $compressionLogService;
    }

    /**
     * @param ConfigurationUtility $configurationUtility
     * @return void
     */
    public function injectConfigurationUtility(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility)
    {
        $this->configurationUtility = $configurationUtility;
        $this->configuration = $this->configurationUtility->getCurrentConfiguration('image_compression');
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
        $limitReached = $this->getCompressionCount() > 0 && $compressionCount < $this->getCompressionCount();

        return $this->getApiKey() && (in_array($file->getExtension(), $this->getSupportedExtensions())) && $limitReached;
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function compress(FileInterface $file)
    {
        try {
            $publicUrl = PATH_site . $file->getPublicUrl();
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
