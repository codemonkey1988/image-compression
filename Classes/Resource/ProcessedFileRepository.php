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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository as BaseProcessedFileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProcessedFileRepository
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ProcessedFileRepository extends BaseProcessedFileRepository
{
    /**
     * @param int $status
     * @param int $limit
     * @throws \InvalidArgumentException
     * @return array
     */
    public function findByImageCompressionStatus($status = FileRepository::IMAGE_COMPRESSION_NOT_PROCESSED, $limit = 0)
    {
        if ($status !== FileRepository::IMAGE_COMPRESSION_NOT_PROCESSED && $status !== FileRepository::IMAGE_COMPRESSION_PROCESSED && $status !== FileRepository::IMAGE_COMPRESSION_SKIPPED) {
            throw new \InvalidArgumentException('Invalid status given.', 1494225066);
        }

        $fileObjecs = [];

        if (GeneralUtility::compat_version('8.6.0')) {
            $rows = $this->getRecords($status, $limit);
        } else {
            $rows = $this->getRecordsCompat($status, $limit);
        }

        foreach ($rows as $row) {
            $fileObjecs[] = $this->createDomainObject($row);
        }

        return $fileObjecs;
    }

    /**
     * Find records for TYPO3 v8 and higher using docrtine.
     *
     * @param int $status
     * @param int $limit
     *
     * @return array
     */
    protected function getRecords($status, $limit)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file_processedfile');

        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(DefaultRestrictionContainer::class));

        $qb = $queryBuilder
            ->select('*')
            ->from('sys_file_processedfile')
            ->where(
                $queryBuilder->expr()->eq(
                    'image_compression_status',
                    $queryBuilder->createNamedParameter($status, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'task_type',
                    $queryBuilder->createNamedParameter('Image.CropScaleMask', \PDO::PARAM_STR)
                )
            )
            ->orderBy('uid', 'ASC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $res = $qb->execute();

        return $res->fetchAll();
    }

    /**
     * Find records for TYPO3 v7
     *
     * @param int $status
     * @param int $limit
     *
     * @return array
     */
    protected function getRecordsCompat($status, $limit)
    {
        return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'sys_file_processedfile',
            'image_compression_status = ' . $status . ' AND task_type="Image.CropScaleMask"',
            '',
            'uid ASC',
            ((int)$limit > 0) ? (int)$limit : ''
        );
    }
}
