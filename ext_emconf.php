<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Image Compression',
    'description'      => 'TYPO3 Extension to compress image',
    'category'         => 'fe',
    'constraints'      => [
        'depends'   => [
            'typo3' => '8.7.0-8.7.99'
        ],
        'conflicts' => [],
        'suggests'  => []
    ],
    'state'            => 'beta',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '2.0.0-dev'
];
