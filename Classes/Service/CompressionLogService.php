<?php
namespace Codemonkey1988\ImageCompression\Service;

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

/**
 * Class CompressionLogService
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressionLogService
{
    /**
     * Add a log entry.
     *
     * @param int $refUid
     * @param string $refTablename
     * @param string $compressor
     * @return void
     */
    public function add($refUid, $refTablename, $compressor = '')
    {
        $this->getDatabaseConnection()->exec_INSERTquery(
            'tx_imagecompression_domain_model_log',
            [
                'pid' => 0,
                'crdate' => time(),
                'ref' => (int)$refUid,
                'tablename' => $refTablename,
                'compressor' => $compressor,
            ]
        );
    }

    /**
     * Count all log entries in a given time period.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function count(\DateTime $from, \DateTime $to)
    {
        $res = $this->getDatabaseConnection()->exec_SELECTquery(
            'uid',
            'tx_imagecompression_domain_model_log',
            'crdate BETWEEN ' . $from->getTimestamp() . ' AND ' . $to->getTimestamp()
        );

        $count = $this->getDatabaseConnection()->sql_num_rows($res);

        return ($count === false) ? 0 : $count;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
