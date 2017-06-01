--
-- Table structure for table 'tx_realurlhashcache_urldata_hash'
--
CREATE TABLE tx_realurlhashcache_urldata_hash (
    uid int(11) NOT NULL auto_increment,
    urldata_uid int(11) DEFAULT '0' NOT NULL,
    hash char(40) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY hash_lookup (hash(10)),
    KEY urldata_uid (urldata_uid)
) ENGINE=InnoDB;
