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

use TYPO3\CMS\Core\Resource\FileRepository as BaseFileRepository;

/**
 * Class FileRepository
 *
 * @package Codemonkey1988\ImageCompression\Resource
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class FileRepository extends BaseFileRepository
{
    const IMAGE_COMPRESSION_NOT_PROCESSED = 0;
    const IMAGE_COMPRESSION_PROCESSED     = 1;
    const IMAGE_COMPRESSION_SKIPPED       = 2;

    /**
     * @param int $status
     * @param int $limit
     * @return array
     * @throws \InvalidArgumentException
     */
    public function findByImageCompressionStatus($status = self::IMAGE_COMPRESSION_NOT_PROCESSED, $limit = 0)
    {
        if ($status !== self::IMAGE_COMPRESSION_NOT_PROCESSED && $status !== self::IMAGE_COMPRESSION_PROCESSED && $status !== self::IMAGE_COMPRESSION_SKIPPED) {
            throw new \InvalidArgumentException('Invalid status given.', 1494225066);
        }

        $fileObjecs = [];
        $res        = $this->getDatabaseConnection()->exec_SELECTquery(
            'sys_file.*, sys_file_metadata.*',
            'sys_file, sys_file_metadata',
            'sys_file.uid=sys_file_metadata.file AND sys_file_metadata.image_compression_status=' . $status . $this->getWhereClauseForEnabledFields(
            ),
            '',
            ((int)$limit > 0) ? (int)$limit : ''
        );

        while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            $fileObjecs[] = $this->createDomainObject($row);
        }

        return $fileObjecs;
    }
}