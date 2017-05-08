<?php

/**
 * Copyright notice
 *
 *  (c) 2017 arndtteunissen <dev@arndtteunissen.de>
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

$ll = 'LLL:EXT:image_compression/Resources/Private/Language/locallang_db.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_processedfile', [
    'image_compression_status' => [
        'label' => $ll . 'sys_file_processedfile.image_compression_status',
        'exclude' => 1,
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [$ll . 'sys_file_processedfile.image_compression_status.0', 0],
                [$ll . 'sys_file_processedfile.image_compression_status.1', 1],
                [$ll . 'sys_file_processedfile.image_compression_status.2', 2]
            ]
        ]
    ]
]);