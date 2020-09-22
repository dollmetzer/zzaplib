<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * This library is a mini framework from php web applications
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 3 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 */

namespace dollmetzer\zzaplib\model;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\logger\LoggerInterface;
use dollmetzer\zzaplib\exception\ApplicationException;
use PDO;

/**
 * Class DbModel
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\model
 */
class DbModel
{
    const ERROR_CONFIG_MISSING_DSN = 'Found no DSN for DB connection in the config';

    const ERROR_CONFIG_MISSING_USER = 'Found no user for DB connection in the config';
    const ERROR_CONFIG_MISSING_PASSWORD = 'Found no password for DB connection in the config';
    const ERROR_CONFIG_MISSING_TABLENAME = 'Missing tablename for DB connection in the model';
    const ERROR_CONFIG_EMPTY_DATA = 'Data array is empty';

    /**
     * @var PDO Database handle
     */
    protected $dbh;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * DbModel constructor.
     *
     * @param Config $config
     * @param LoggerInterface $logger
     * @throws ApplicationException
     */
    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->init();
    }

    /**
     * Init DB Connection
     *
     * @throws ApplicationException
     */
    public function init()
    {
        if (!$this->config->isSet('dsn', 'database')) {
            $this->logger->critical(self::ERROR_CONFIG_MISSING_DSN);
            throw new ApplicationException(self::ERROR_CONFIG_MISSING_DSN);
        }
        $dsn = $this->config->get('dsn', 'database');

        if (!$this->config->isSet('user', 'database')) {
            $this->logger->critical(self::ERROR_CONFIG_MISSING_USER);
            throw new ApplicationException(self::ERROR_CONFIG_MISSING_USER);
        }
        $user = $this->config->get('user', 'database');

        if (!$this->config->isSet('password', 'database')) {
            $this->logger->critical(self::ERROR_CONFIG_MISSING_PASSWORD);
            throw new ApplicationException(self::ERROR_CONFIG_MISSING_PASSWORD);
        }
        $pass = $this->config->get('password', 'database');

        if (empty($this->tableName)) {
            $this->logger->critical(self::ERROR_CONFIG_MISSING_TABLENAME);
            throw new ApplicationException(self::ERROR_CONFIG_MISSING_TABLENAME);
        }

        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        try {
            $this->dbh = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            $message = 'DB Error: ' . $e->getMessage();
            $this->logger->critical($message);
            throw new ApplicationException($message);
        }
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * create a new data set
     *
     * @param array $data
     * @return int
     * @throws ApplicationException
     */
    public function create(array $data): int
    {
        if (empty($data)) {
            throw new ApplicationException(self::ERROR_CONFIG_EMPTY_DATA);
        }

        $names = '`' . join('`, `', array_keys($data)) . '`';
        $questionmarks = join(', ', array_fill(0, sizeof(array_keys($data)), '?'));
        $values = array_values($data);
        $sql = "INSERT INTO `" . $this->tableName . '` (' . $names . ') VALUES (' . $questionmarks . ')';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);
        return $this->dbh->lastInsertId();
    }

    /**
     * read a data set
     *
     * @param int $id
     * @return null | array
     */
    public function read(int $id)
    {
        $sql = "SELECT * FROM `" . $this->tableName . "` WHERE id=" . $id;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * update a data set
     *
     * @param int $id
     * @param array $data
     * @throws ApplicationException
     */
    public function update(int $id, array $data)
    {
        if (empty($data)) {
            throw new ApplicationException(self::ERROR_CONFIG_EMPTY_DATA);
        }

        $names = array();
        foreach (array_keys($data) as $key) {
            $names[] = $key . '=?';
        }
        $values = array_values($data);
        $values[] = $id;
        $sql = "UPDATE `" . $this->tableName . "` SET " . join(', ', $names) . " WHERE id=?";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * Delete a data set
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $sql = "DELETE FROM `" . $this->tableName . "` WHERE id=" . $id;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    /**
     * Get a paged list
     *
     * @param null $first
     * @param null $length
     * @param null $sortColumn
     * @param string $sortDirection
     * @return array
     */
    public function getList($first = null, $length = null, $sortColumn = null, $sortDirection = 'asc')
    {
        $sql = "SELECT * FROM `" . $this->tableName . "`";
        if ($sortColumn) {
            if ($sortDirection != 'desc') {
                $sortDirection = 'asc';
            }
            $sql .= ' ORDER BY ' . $sortColumn . ' ' . strtoupper($sortDirection);
        }
        if (isset($first) && isset($length)) {
            $sql .= ' LIMIT ' . (int)$first . ',' . (int)$length;
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $list;
    }

    /**
     * Get the number of entries the list
     *
     * @return integer
     */
    public function getListEntries()
    {
        $sql = "SELECT COUNT(*) as entries FROM `" . $this->tableName . "`";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['entries'];
    }

    /**
     * Search for an entry
     *
     * @param string $searchTerm
     * @param int $first
     * @param int $length
     * @param string $sortColumn
     * @param string $sortDirection (default is 'asc')
     * @param string $searchColumn (default is 'name')
     * @return array
     */
    public function search(
        $searchTerm,
        $first = null,
        $length = null,
        $sortColumn = null,
        $sortDirection = 'asc',
        $searchColumn = 'name'
    ) {
        $sql = "SELECT * FROM `" . $this->tableName . "` WHERE " . $searchColumn . " LIKE " . $this->dbh->quote($searchTerm);
        if ($sortColumn) {
            if ($sortDirection != 'desc') {
                $sortDirection = 'asc';
            }
            $sql .= ' ORDER BY ' . $sortColumn . ' ' . strtoupper($sortDirection);
        }
        if (isset($first) && isset($length)) {
            $sql .= ' LIMIT ' . (int)$first . ',' . (int)$length;
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $list;
    }

    /**
     * Get the number of results for a search
     *
     * @param string $searchTerm
     * @param string $searchColumn
     * @return array
     */
    public function getSearchEntries($searchTerm, $searchColumn = 'name')
    {
        $sql = "SELECT COUNT(*) as entries FROM `" . $this->tableName . "` WHERE " . $searchColumn . " LIKE " . $this->dbh->quote($searchTerm);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['entries'];
    }

}