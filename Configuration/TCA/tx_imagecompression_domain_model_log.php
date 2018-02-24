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

$ll = 'LLL:EXT:image_compression/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'label' => 'tablename',
        'label_alt' => 'ref',
        'label_alt_force' => true,
        'crdate' => 'crdate',
        'hideTable' => true,
        'default_sortby' => 'ORDER BY crdate DESC',
        'rootLevel' => '1',
        'title' => $ll . 'tx_imagecompression_domain_model_log',
        'typeicon_classes' => [
            'default' => 'mimetypes-x-content-text',
        ],
    ],
    'interface' => [
        'showRecordFieldList' => 'ref,tablename',
    ],
    'columns' => [
        'ref' => [
            'label' => $ll . 'tx_imagecompression_domain_model_log.ref',
            'config' => [
                'type' => 'input',
                'size' => 11,
                'eval' => 'int,required',
            ],
        ],
        'tablename' => [
            'label' => $ll . 'tx_imagecompression_domain_model_log.tablename',
            'config' => [
                'type' => 'input',
                'size' => 25,
                'max' => 25,
                'eval' => 'trim,required',
            ],
        ],
        'compressor' => [
            'label' => $ll . 'tx_imagecompression_domain_model_log.compressor',
            'config' => [
                'type' => 'input',
                'size' => 25,
                'max' => 25,
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
			ref,tablename,compressor
		'],
    ],
];
