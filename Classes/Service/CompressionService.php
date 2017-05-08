<?php
namespace Codemonkey1988\ImageCompression\Service;

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

use Codemonkey1988\ImageCompression\Compressor\CompressorFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * Class CompressionService
 *
 * @package Codemonkey1988\ImageCompression\Service
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressionService
{
    /**
     * @var \Codemonkey1988\ImageCompression\Service\CompressionLogService
     */
    protected $compressionLogService;

    /**
     * @param \Codemonkey1988\ImageCompression\Service\CompressionLogService $compressionLogService
     * @return void
     */
    public function injectCompressionLogService(\Codemonkey1988\ImageCompression\Service\CompressionLogService $compressionLogService)
    {
        $this->compressionLogService = $compressionLogService;
    }

    /**
     * Compress an image file.
     *
     * @param FileInterface $file
     * @return bool
     */
    public function compress(FileInterface $file)
    {
        $uid     = $file->getProperty('uid');
        $table   = ($file instanceof File) ? 'sys_file_metadata' : 'sys_file_processedfile';
        $compressor = CompressorFactory::getCompressor($file);

        if ($compressor !== null) {
            $success = $compressor->compress($file);

            $this->updateCompressionStatus($uid, $table, ($success === true) ? 1 : 2);

            // When the image could be successfully compressed, add it to the log.
            if ($success) {
                $this->compressionLogService->add($uid, $table, $compressor->getName());

                return true;
            }
        } else {
            // No matching compressor can be found.
            $this->updateCompressionStatus($uid, $table, 2);

            return true;
        }

        return false;
    }

    /**
     * @param int    $fileUid
     * @param string $table
     * @param int    $status
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
                'image_compression_status' => $status
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