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
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Interface CompressorInterface
 *
 * @package Codemonkey1988\ImageCompression\Compressor
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