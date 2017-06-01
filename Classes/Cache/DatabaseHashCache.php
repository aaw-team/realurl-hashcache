<?php
namespace AawTeam\RealurlHashcache\Cache;

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

use DmitryDulepov\Realurl\Cache\UrlCacheEntry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DatabaseHashCache
 */
class DatabaseHashCache extends \DmitryDulepov\Realurl\Cache\DatabaseCache
{
    /**
     * Override parent method: only the query itself differs from parent!
     *
     * {@inheritdoc}
     * @see \DmitryDulepov\Realurl\Cache\DatabaseCache::getUrlFromCacheByOriginalUrl()
     */
    public function getUrlFromCacheByOriginalUrl($rootPageId, $originalUrl)
    {
        $cacheEntry = null;

        $row = $this->databaseConnection->exec_SELECTgetSingleRow('d.*', 'tx_realurlhashcache_urldata_hash AS h JOIN tx_realurl_urldata AS d ON h.urldata_uid=d.uid',
                'd.rootpage_id=' . (int)$rootPageId . ' AND h.hash=\'' . sha1($originalUrl) .'\'',
                '', 'expire');
        if (is_array($row)) {
            // 1:1 the same as in parent method
            $cacheEntry = GeneralUtility::makeInstance('DmitryDulepov\\Realurl\\Cache\\UrlCacheEntry');
            /** @var \DmitryDulepov\Realurl\Cache\UrlCacheEntry $cacheEntry */
            $cacheEntry->setCacheId($row['uid']);
            $cacheEntry->setExpiration($row['expire']);
            $cacheEntry->setPageId($row['page_id']);
            $cacheEntry->setRootPageId($row['rootpage_id']);
            $cacheEntry->setOriginalUrl($originalUrl);
            $cacheEntry->setSpeakingUrl($row['speaking_url']);
            $requestVariables = json_decode($row['request_variables'], TRUE);
            // TODO Log a problem here because it must be an array always
            $cacheEntry->setRequestVariables(is_array($requestVariables) ? $requestVariables : array());
        }

        return $cacheEntry;
    }

    /**
     * Extend parent method: add an entry into tx_realurl_urldata_hash that is
     * related to tx_realurl_urldata.
     *
     * {@inheritdoc}
     * @see \DmitryDulepov\Realurl\Cache\DatabaseCache::getUrlFromCacheByOriginalUrl()
     */
    public function putUrlToCache(UrlCacheEntry $cacheEntry)
    {
        // Run parent method (puts data into tx_realurl_urldata)
        parent::putUrlToCache($cacheEntry);

        // Does a tx_realurl_urldata_hash entry exist?
        $count = $this->databaseConnection->exec_SELECTcountRows('uid', 'tx_realurlhashcache_urldata_hash', 'urldata_uid=' . $cacheEntry->getCacheId());

        if ($count < 1) {
            // Add hash to tx_realurl_urldata_hash
            $this->databaseConnection->exec_INSERTquery('tx_realurlhashcache_urldata_hash', [
                'urldata_uid' => $cacheEntry->getCacheId(),
                'hash' => \sha1($cacheEntry->getOriginalUrl())
            ]);
        }
    }
}