<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Service;

use Codemonkey1988\ImageCompression\Compressor\CompressorInterface;
use Codemonkey1988\ImageCompression\Resource\FileRepository;
use Codemonkey1988\ImageCompression\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
     * @param AbstractFile $file
     */
    public function compress(AbstractFile $file)
    {
        $compressor = $this->getFirstMatchingCompressor($file);

        if ($compressor !== null && $compressor->compress($file) === true) {
            $this->updateCompressionStatus($file);
            $this->updatePostProcess($file);
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
     * @param AbstractFile $file
     * @return CompressorInterface|null
     */
    protected function getFirstMatchingCompressor(AbstractFile $file)
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
     * @param AbstractFile $file
     */
    protected function updateCompressionStatus(AbstractFile $file)
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
     * @param AbstractFile $file
     */
    protected function updateCheckedStatus(AbstractFile $file)
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

    /**
     * Updates necessary database fields after successful image compression.
     *
     * @param AbstractFile $file
     */
    protected function updatePostProcess(AbstractFile $file)
    {
        // There are only database updates, when the original file is compressed.
        if (($file instanceof File) === false) {
            return;
        }

        $size = filesize($file->getForLocalProcessing(false));

        // Update sys_file table. Fields: size
        // Do not update modification time, because it makes all processed images from this image invalid. (See checksum generation)
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');

        $queryBuilder
            ->update('sys_file')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($file->getUid(), \PDO::PARAM_INT))
            )
            ->set('size', $size)
            ->execute();
    }
}
