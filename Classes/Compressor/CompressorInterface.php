<?php

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Compressor;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\SingletonInterface;

interface CompressorInterface extends SingletonInterface
{
    /**
     * This method should return true, if this processor can compress the given file.
     *
     * @param FileInterface $file
     * @return bool
     */
    public function canCompress(FileInterface $file);

    /**
     * If the compression was successful, this method should return true.
     *
     * @param FileInterface $file
     * @return bool
     */
    public function compress(FileInterface $file);
}
