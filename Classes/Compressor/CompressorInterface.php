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
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Interface CompressorInterface
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
interface CompressorInterface extends SingletonInterface
{
    /**
     * Should return a unique name for this compressor.
     *
     * @return string
     */
    public function getName();

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
