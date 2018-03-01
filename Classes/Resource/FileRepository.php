<?php
namespace Codemonkey1988\ImageCompression\Resource;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Resource\FileRepository as BaseFileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileRepository
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class FileRepository extends BaseFileRepository
{
    /**
     * @param int $limit
     * @throws \InvalidArgumentException
     * @return array
     */
    public function findNoncompressedImages($limit = 0)
    {
        $fileObjecs = [];

        if (GeneralUtility::compat_version('8.6.0')) {
            $rows = $this->getRecords($limit);
        } else {
            $rows = $this->getRecordsCompat($limit);
        }

        foreach ($rows as $row) {
            $fileObjecs[] = $this->createDomainObject($row);
        }

        return $fileObjecs;
    }

    /**
     * Find records for TYPO3 v8 and higher using docrtine.
     *
     * @param int $limit
     *
     * @return array
     */
    protected function getRecords($limit)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');

        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(DefaultRestrictionContainer::class));

        $qb = $queryBuilder
            ->select('sys_file.*', 'metadata.*')
            ->from('sys_file')
            ->join(
                'sys_file',
                'sys_file_metadata',
                'metadata',
                $queryBuilder->expr()->eq('metadata.file', $queryBuilder->quoteIdentifier('sys_file.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'metadata.image_compression_last_compressed',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->orderBy('sys_file.uid', 'ASC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $res = $qb->execute();

        return $res->fetchAll();
    }

    /**
     * Find records for TYPO3 v7
     *
     * @param int $limit
     *
     * @return array
     */
    protected function getRecordsCompat($limit)
    {
        $enabledFieldsWhereClause = BackendUtility::BEenableFields('sys_file');
        $enabledFieldsWhereClause .= BackendUtility::deleteClause('sys_file');

        return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'sys_file.*, sys_file_metadata.*',
            'sys_file, sys_file_metadata',
            'sys_file.uid=sys_file_metadata.file AND sys_file_metadata.image_compression_last_compressed=0' . $enabledFieldsWhereClause,
            '',
            'sys_file.uid ASC',
            ((int)$limit > 0) ? (int)$limit : ''
        );
    }
}
