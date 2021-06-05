<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Service;

use TYPO3\CMS\Core\SingletonInterface;

class ConfigurationService implements SingletonInterface
{
    /**
     * @var array
     */
    protected $extensionConfiguration;

    public function __construct()
    {
        $this->extensionConfiguration = [];

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['image_compression'])) {
            $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['image_compression'];
        } elseif (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['image_compression'])) {
            $this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['image_compression']);
        }
    }

    /**
     * @return bool
     */
    public function isCompressOnUploadEnabled(): bool
    {
        if (!empty($this->extensionConfiguration['enableCompressOnUpload'])) {
            return (int)$this->extensionConfiguration['enableCompressOnUpload'] === 1;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTinifyMaxMonthlyCompressionCount(): int
    {
        if (!empty($this->extensionConfiguration['tinifyCompressionCount'])) {
            return (int)$this->extensionConfiguration['tinifyCompressionCount'];
        }

        return 500;
    }

    /**
     * @return string
     */
    public function getTinifyApiKey(): string
    {
        if (!empty($this->extensionConfiguration['tinifyApiKey'])) {
            return trim($this->extensionConfiguration['tinifyApiKey']);
        }

        return '';
    }
}
