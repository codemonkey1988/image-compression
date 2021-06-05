<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Resource;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Resource\FileRepository as BaseFileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
                $queryBuilder->expr()->eq(
                    'metadata.image_compression_last_checked',
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
