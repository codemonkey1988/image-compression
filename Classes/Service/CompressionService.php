<?php
declare(strict_types=1);
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

use Codemonkey1988\ImageCompression\Compressor\CompressorInterface;
use Codemonkey1988\ImageCompression\Resource\FileRepository;
use Codemonkey1988\ImageCompression\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class CompressionService
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressionService implements SingletonInterface
{
    /**
     * All available compressors.
     *
     * @var array
     */
    protected $compressors = [];

    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * @var ProcessedFileRepository
     */
    protected $processedFileRepository;

    /**
     * CompressionService constructor.
     */
    public function __construct()
    {
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression']['compressors'])) {
            $this->compressors = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression']['compressors'];
        }
    }

    /**
     * @param FileRepository $fileRepository
     */
    public function injectFileRepository(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param ProcessedFileRepository $processedFileRepository
     */
    public function injectProcessedFileRepository(ProcessedFileRepository $processedFileRepository)
    {
        $this->processedFileRepository = $processedFileRepository;
    }

    /**
     * Compress an image file.
     *
     * @param FileInterface $file
     * @return void
     */
    public function compress(FileInterface $file)
    {
        $compressor = $this->getFirstMatchingCompressor($file);

        if ($compressor !== null && $compressor->compress($file) === true) {
            $this->updateCompressionStatus($file);
        }

        // Mark file as "processed"
        $this->updateCheckedStatus($file);
    }

    /**
     * @param array $fileExtensions
     * @param int $limit
     * @return array
     */
    public function getUncompressedOriginalFiles(array $fileExtensions, int $limit): array
    {
        return $this->fileRepository->findUncompressedImages($fileExtensions, $limit);
    }

    /**
     * @param array $fileExtensions
     * @param int $limit
     * @return array
     */
    public function getUncompressedProcessedFiles(array $fileExtensions, int $limit): array
    {
        return $this->processedFileRepository->findUncompressedImages($fileExtensions, $limit);
    }

    /**
     * Find the first compressor that can compress the given file.
     *
     * @param FileInterface $file
     * @return CompressorInterface|null
     */
    protected function getFirstMatchingCompressor(FileInterface $file)
    {
        $imageCompressor = null;
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var CompressorInterface $object */
        foreach ($this->compressors as $compressor) {
            if (is_string($compressor)) {
                $compressor = $objectManager->get($compressor);
            }

            if ($compressor instanceof CompressorInterface && $compressor->canCompress($file)) {
                $imageCompressor = $compressor;
            }
        }

        return $imageCompressor;
    }

    /**
     * Update the compression status.
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
            ->update($table)
            ->where(
                $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($file->getUid(), \PDO::PARAM_INT))
            )
            ->set('image_compression_last_compressed', $now->getTimestamp())
            ->set('tstamp', $now->getTimestamp())
            ->execute();
    }

    /**
     * Update the checked status.
     *
     * @param FileInterface $file
     * @return void
     */
    protected function updateCheckedStatus(FileInterface $file)
    {
        $now = new \DateTime();
        $table = ($file instanceof File) ? 'sys_file_metadata' : 'sys_file_processedfile';
        $field = ($file instanceof File) ? 'file' : 'uid';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($file->getUid(), \PDO::PARAM_INT))
            )
            ->set('image_compression_last_checked', $now->getTimestamp())
            ->execute();
    }
}
