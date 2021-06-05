<?php

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Image Compression',
    'description'      => 'TYPO3 Extension to compress image',
    'category'         => 'fe',
    'constraints'      => [
        'depends'   => [
            'typo3' => '8.7.0-10.4.99'
        ],
        'conflicts' => [],
        'suggests'  => []
    ],
    'state'            => 'stable',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '2.1.0-dev'
];
