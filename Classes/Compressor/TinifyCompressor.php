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

use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * Class CompressionService
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class TinifyCompressor implements CompressorInterface
{
    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @param ConfigurationService $configurationService
     * @return void
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * @return void
     */
    public function initializeObject()
    {
        \Tinify\setKey($this->configurationService->getTinifyApiKey());
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
        $apiKey = $this->configurationService->getTinifyApiKey();
        $supportedExtensions = $this->configurationService->getTinifySupportedExtensions();
        $maxCompressed = $this->configurationService->getTinifyMaxMonthlyCompressionCount();
        $currentCompressed = $this->getCurrentCompressionCount();

        return !empty($apiKey) && (in_array($file->getExtension(), $supportedExtensions)) && $currentCompressed < $maxCompressed;
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
     * @return int
     */
    protected function getCurrentCompressionCount(): int
    {
        return (int)\Tinify\getCompressionCount();
    }
}
