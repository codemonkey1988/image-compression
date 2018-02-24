<?php

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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression'] = [
    'compressors' => [
        \Codemonkey1988\ImageCompression\Compressor\TinifyCompressor::class,
    ],
];

// Add scheduler task for compressing images.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Codemonkey1988\ImageCompression\Task\CompressTask::class] = [
    'extension' => 'codemonkey1988.image_compressor',
    'title' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress',
    'description' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress.description',
    'additionalFields' => \Codemonkey1988\ImageCompression\Task\CompressTaskFieldProvider::class,
];

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorage::SIGNAL_PostFileAdd,
    \Codemonkey1988\ImageCompression\Signal\ResourceStorageSignal::class,
    'postFileAdd'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorage::SIGNAL_PostFileReplace,
    \Codemonkey1988\ImageCompression\Signal\ResourceStorageSignal::class,
    'postFileReplace'
);
