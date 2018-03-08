<?php
namespace Codemonkey1988\ImageCompression\Service;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Class ConfigurationService
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ConfigurationService implements SingletonInterface
{
    /**
     * @var ConfigurationUtility
     */
    protected $configurationUtility;

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * @param ConfigurationUtility $configurationUtility
     */
    public function injectConfigurationUtility(ConfigurationUtility $configurationUtility)
    {
        $this->configurationUtility = $configurationUtility;
        $this->extensionConfiguration = $this->configurationUtility->getCurrentConfiguration('image_compression');
    }

    /**
     * @return bool
     */
    public function isCompressOnUploadEnabled(): bool
    {
        if (is_array($this->extensionConfiguration) && !empty($this->extensionConfiguration['enableCompressOnUpload']['value'])) {
            return (int)$this->extensionConfiguration['enableCompressOnUpload']['value'] === 1;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTinifySupportedExtensions(): array
    {
        if (is_array($this->extensionConfiguration) && !empty($this->extensionConfiguration['tinifyExtensions']['value'])) {
            return GeneralUtility::trimExplode(',', $this->extensionConfiguration['tinifyExtensions']['value']);
        }

        return [];
    }

    /**
     * @return int
     */
    public function getTinifyMaxMonthlyCompressionCount(): int
    {
        if (is_array($this->extensionConfiguration) && !empty($this->extensionConfiguration['tinifyCompressionCount']['value'])) {
            return (int)$this->extensionConfiguration['tinifyCompressionCount']['value'];
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getTinifyApiKey(): string
    {
        if (is_array($this->extensionConfiguration) && !empty($this->extensionConfiguration['tinifyApiKey']['value'])) {
            return trim($this->extensionConfiguration['tinifyApiKey']['value']);
        }

        return '';
    }
}
