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
     * @return bool
     */
    public function compress(FileInterface $file)
    {
        $uid = $file->getProperty('uid');
        $table = ($file instanceof File) ? 'sys_file_metadata' : 'sys_file_processedfile';
        $compressor = CompressorFactory::getCompressor($file);

        if ($compressor !== null) {
            $success = $compressor->compress($file);

            $this->updateCompressionStatus($uid, $table, ($success === true) ? 1 : 2);

            return $success;
        }

        return true;
    }

    /**
     * @param int $fileUid
     * @param string $table
     * @param int $status
     * @return void
     */
    protected function updateCompressionStatus($fileUid, $table, $status)
    {
        $where = 'uid=' . $fileUid;

        if ($table === 'sys_file_metadata') {
            $where = 'file=' . $fileUid;
        }

        // Update compression status for this file.
        $this->getDatabaseConnection()->exec_UPDATEquery(
            $table,
            $where,
            [
                'image_compression_status' => $status,
            ]
        );
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
