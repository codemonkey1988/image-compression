<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Compressor;

use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use TYPO3\CMS\Core\Resource\FileInterface;

class TinifyCompressor implements CompressorInterface
{
    /**
     * @var array
     */
    protected $supportedExtensions = ['png', 'jpg', 'jpeg'];

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @param ConfigurationService $configurationService
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public function initializeObject()
    {
        \Tinify\setKey($this->configurationService->getTinifyApiKey());
        \Tinify\setAppIdentifier('t3_image_compression');
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function canCompress(FileInterface $file)
    {
        $apiKey = $this->configurationService->getTinifyApiKey();
        $maxCompressed = $this->configurationService->getTinifyMaxMonthlyCompressionCount();
        $currentCompressed = $this->getCurrentCompressionCount();
        $storage = $file->getStorage();

        return !empty($apiKey)
            && (in_array($file->getExtension(), $this->supportedExtensions))
            && $currentCompressed < $maxCompressed
            && $storage->getDriverType() === 'Local';
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function compress(FileInterface $file)
    {
        try {
            $publicUrl = $file->getForLocalProcessing(false);
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
