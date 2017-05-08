<?php
namespace Codemonkey1988\ImageCompression\Resource;

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

use TYPO3\CMS\Core\Resource\ProcessedFileRepository as BaseProcessedFileRepository;

/**
 * Class ProcessedFileRepository
 *
 * @package Codemonkey1988\ImageCompression\Resource
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ProcessedFileRepository extends BaseProcessedFileRepository
{
    /**
     * @param int $status
     * @param int $limit
     * @return array
     * @throws \InvalidArgumentException
     */
    public function findByImageCompressionStatus($status = FileRepository::IMAGE_COMPRESSION_NOT_PROCESSED, $limit = 0)
    {
        if ($status !== FileRepository::IMAGE_COMPRESSION_NOT_PROCESSED && $status !== FileRepository::IMAGE_COMPRESSION_PROCESSED && $status !== FileRepository::IMAGE_COMPRESSION_SKIPPED) {
            throw new \InvalidArgumentException('Invalid status given.', 1494225066);
        }

        $fileObjecs = [];

        $res = $this->databaseConnection->exec_SELECTQuery(
            '*',
            $this->table,
            'image_compression_status = ' . $status . ' AND task_type="Image.CropScaleMask"',
            '',
            'tstamp ASC',
            ((int)$limit > 0) ? (int)$limit : ''
        );

        if ($this->databaseConnection->sql_num_rows($res)) {
            while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
                $fileObjecs[] = $this->createDomainObject($row);
            }
        }

        return $fileObjecs;
    }
}