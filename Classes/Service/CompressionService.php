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

use Codemonkey1988\ImageCompression\Compressor\CompressorFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Class CompressionService
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressionService
{
    /**
     * Compress an image file.
     *
     * @param FileInterface $file
     * @return void
     */
    public function compress(FileInterface $file)
    {
        $compressor = CompressorFactory::getCompressor($file);

        if ($compressor !== null && $compressor->compress($file) === true) {
            if (GeneralUtility::compat_version('8.6.0')) {
                $this->updateCompressionStatus($file);
            } else {
                $this->updateCompressionStatusCompat($file);
            }
        }
    }

    /**
     * Update the compression status for TYPO3 v8 and higher.
     *
     * @param FileInterface $file
     * @return void
     */
    protected function updateCompressionStatus(FileInterface $file)
    {
        $now = new \DateTime();
        $table = ($file instanceof File) ? 'sys_file_metadata' : 'sys_file_processedfile';
        $field = ($file instanceof File) ? 'file' : 'uid';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder
            ->update($table, 't')
            ->where(
                $queryBuilder->expr()->eq('t' . $field, $queryBuilder->createNamedParameter($file->getUid()))
            )
            ->set('t.image_compression_last_compressed', $now->getTimestamp())
            ->execute();
    }

    /**
     * Update the compression status for TYPO3 v7.
     *
     * @param FileInterface $file
     * @return void
     */
    protected function updateCompressionStatusCompat(FileInterface $file)
    {
        $now = new \DateTime();
        $table = ($file instanceof File) ? 'sys_file_metadata' : 'sys_file_processedfile';
        $field = ($file instanceof File) ? 'file' : 'uid';

        // Update compression status for this file.
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
            $table,
            $field . '=' . (int) $file->getUid(),
            [
                'image_compression_last_compressed' => $now->getTimestamp(),
            ]
        );
    }
}
