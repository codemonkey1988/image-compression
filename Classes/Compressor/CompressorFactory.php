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
