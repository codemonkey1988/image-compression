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
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class CompressorFactory
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressorFactory
{
    /**
     * Get a new instance of a compressor, that can compress the given file.
     *
     * @param FileInterface $file
     * @return CompressorInterface|null
     */
    public static function getCompressor(FileInterface $file)
    {
        $imageCompressor = null;
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression']['compressors'] as $compressor) {
            /** @var CompressorInterface $object */
            $object = $objectManager->get($compressor);

            if ($object instanceof CompressorInterface && $object->canCompress($file)) {
                $imageCompressor = $object;
            }
        }

        return $imageCompressor;
    }
}
