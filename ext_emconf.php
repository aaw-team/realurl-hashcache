<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "realurl_hashcache".
 *
 * Auto generated 01-06-2017 14:39
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => '"Hash-cache" for realurl',
    'description' => 'A hash-based cache for TYPO3 extension realurl',
    'category' => 'plugin',
    'author' => 'Agentur am Wasser | Maeder & Partner AG',
    'author_email' => 'development@agenturamwasser.ch',
    'state' => 'experimental',
    'clearCacheOnLoad' => 0,
    'version' => '0.1.0-dev',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-8.7.999',
            'realurl' => '2.2.1-2.2.1'
        ),
        'conflicts' => array(),
        'suggests' => array()
    )
);
