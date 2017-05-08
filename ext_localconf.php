<?php

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

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['image_compression'] = [
    'compressors' => [
        \Codemonkey1988\ImageCompression\Compressor\TinifyCompressor::class
    ]
];

// Add scheduler task for compressing images.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Codemonkey1988\ImageCompression\Task\CompressTask::class] = [
    'extension' => 'codemonkey1988.image_compressor',
    'title' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress',
    'description' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress.description',
    'additionalFields' => \Codemonkey1988\ImageCompression\Task\CompressTaskFieldProvider::class
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