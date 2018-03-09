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

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_processedfile', [
    'image_compression_last_compressed' => [
        'label' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_db.xlf:sys_file_processedfile.image_compression_last_compressed',
        'exclude' => 1,
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'dbType' => 'datetime',
            'eval' => 'datetime',
        ],
    ],
]);
