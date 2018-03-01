<?php

/**
 * Copyright notice
 *
 *  (c) 2018 Tim Schreiner <schreiner.tim@gmail.com>
 *  All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

$boot = function() {
    $ll = 'LLL:EXT:image_compression/Resources/Private/Language/locallang_db.xlf:';

    if (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('8.6.0')) {
        $config = [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'dbType' => 'datetime',
            'eval' => 'datetime',
        ];
    } else {
        $config = [
            'type' => 'input',
            'size' => '13',
            'eval' => 'datetime',
            'default' => '0'
        ];
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_processedfile', [
        'image_compression_last_compressed' => [
            'label' => $ll . 'sys_file_processedfile.image_compression_last_compressed',
            'exclude' => 1,
            'config' => $config
        ]
    ]);
};

$boot();
unset($boot);
