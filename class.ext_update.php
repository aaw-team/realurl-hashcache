<?php
namespace AawTeam\RealurlHashcache;

/*
 * Copyright 2017 Agentur am Wasser | Maeder & Partner AG
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Extension update class
 */
class ext_update
{
    /**
     * @return boolean
     */
    public function access()
    {
        return true;
    }

    /**
     * @return void
     */
    public function main()
    {
        $this->addForeignKeyConstraint();
        $this->synchronizeTables();
        return 'Finished all updates';
    }

	/**
	 * Returns true, when the foreign key constraint
	 * tx_realurlhashcache_urldata_hash.fk_parent exists.
	 *
	 * @return boolean
	 */
	protected function hasForeignKeyConstraint()
	{
	    static $constraintExists = null;
	    if ($constraintExists === null) {
    	    $foundConstraint = $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE',
    	            'TABLE_SCHEMA=DATABASE()
    	             AND CONSTRAINT_NAME=\'fk_urldata\'
    	             AND TABLE_NAME=\'tx_realurlhashcache_urldata_hash\'
    	             AND COLUMN_NAME=\'urldata_uid\'
    	             AND REFERENCED_TABLE_SCHEMA=DATABASE()
    	             AND REFERENCED_TABLE_NAME=\'tx_realurl_urldata\'
    	             AND REFERENCED_COLUMN_NAME=\'uid\'');
    	    $constraintExists = ($foundConstraint === 1);
	    }
	    return $constraintExists;
	}

	/**
	 * @return void
	 */
	protected function addForeignKeyConstraint()
	{
	    if (!$this->hasForeignKeyConstraint()) {
    	    // Add the constraint
    	    $this->getDatabaseConnection()->admin_query('ALTER TABLE `tx_realurlhashcache_urldata_hash` ADD CONSTRAINT `fk_urldata` FOREIGN KEY (`urldata_uid`) REFERENCES `tx_realurl_urldata`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;');
	    }
	}

	/**
	 * @return void
	 */
	protected function synchronizeTables()
	{
	    $this->getDatabaseConnection()->admin_query('TRUNCATE `tx_realurlhashcache_urldata_hash`');
	    $this->getDatabaseConnection()->admin_query('INSERT INTO `tx_realurlhashcache_urldata_hash` SELECT NULL, d.uid, SHA1(d.original_url) FROM tx_realurl_urldata d');
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection()
	{
	    return $GLOBALS['TYPO3_DB'];
	}
}
