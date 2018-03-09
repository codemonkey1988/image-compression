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
     * @param array $fileExtensions
     * @param int $limit
     * @throws \InvalidArgumentException
     * @return array
     */
    public function findUncompressedImages(array $fileExtensions, $limit = 0): array
    {
        $fileObjecs = [];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
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
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->in(
                    'sys_file.extension',
                    $queryBuilder->createNamedParameter($fileExtensions, Connection::PARAM_STR_ARRAY)
                )
            )
            ->orderBy('sys_file.uid', 'ASC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $statement = $qb->execute();

        while ($row = $statement->fetch()) {
            $fileObjecs[] = $this->createDomainObject($row);
        }

        return $fileObjecs;
    }
}
