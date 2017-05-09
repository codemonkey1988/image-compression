<?php

$EM_CONF[$_EXTKEY] = array(
    'title'            => 'Image Compression',
    'description'      => 'TYPO3 Extension to compress image',
    'category'         => 'fe',
    'constraints'       => array(
        'depends'   => array(
            'typo3' => '7.6.0-7.6.99',
        ),
        'conflicts' => array(),
    ),
    'state'            => 'beta',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '1.0.0'
);
