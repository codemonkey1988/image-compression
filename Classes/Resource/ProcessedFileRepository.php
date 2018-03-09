<?php
declare(strict_types=1);
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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProcessedFileRepository
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ProcessedFileRepository extends \TYPO3\CMS\Core\Resource\ProcessedFileRepository
{
    /**
     * @param array $fileExtensions
     * @param int $limit
     * @throws \InvalidArgumentException
     * @return array
     */
    public function findUnCompressedImages(array $fileExtensions, $limit = 0): array
    {
        $fileObjecs = [];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_processedfile');
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(DefaultRestrictionContainer::class));

        $qb = $queryBuilder
            ->select('sys_file_processedfile.*')
            ->from('sys_file_processedfile')
            ->join(
                'sys_file_processedfile',
                'sys_file',
                'sys_file',
                $queryBuilder->expr()->eq('sys_file_processedfile.original', $queryBuilder->quoteIdentifier('sys_file.uid'))
            )
            ->join(
                'sys_file_processedfile',
                'sys_file_storage',
                'storage',
                $queryBuilder->expr()->eq('sys_file_processedfile.storage', $queryBuilder->quoteIdentifier('storage.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'sys_file_processedfile.image_compression_last_compressed',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'sys_file_processedfile.task_type',
                    $queryBuilder->createNamedParameter('Image.CropScaleMask', Connection::PARAM_STR)
                ),
                $queryBuilder->expr()->in(
                    'sys_file.extension',
                    $queryBuilder->createNamedParameter($fileExtensions, Connection::PARAM_STR_ARRAY)
                ),
                $queryBuilder->expr()->eq(
                    'storage.driver',
                    $queryBuilder->createNamedParameter('Local', Connection::PARAM_STR)
                )
            )
            ->orderBy('uid', 'ASC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $res = $qb->execute();

        while ($row = $res->fetch()) {
            $fileObjecs[] = $this->createDomainObject($row);
        }

        return $fileObjecs;
    }
}
