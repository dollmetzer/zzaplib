<?php
/**
 * z z a p l i b   m i n i   f r a m e w o r k
 * ===========================================
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

namespace dollmetzer\zzaplib;

/**
 * DBModel is a base class for all DB related models
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2018 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class DBModel
{

    /**
     * @var \PDO Database handle
     */
    protected $dbh;

    /**
     * @var array Configuration
     */
    protected $config;


    /**
     * Connects to DB
     *
     * @param array $_config
     * @param boolean $_master Use Master DB (default = true)
     */
    public function __construct($_config, $_master = true)
    {

        $this->config = $_config;

        if ($_master === false) {
            $dsn = $_config['db']['slave']['dsn'];
            $user = $_config['db']['slave']['user'];
            $pass = $_config['db']['slave']['pass'];
        } else {
            $dsn = $_config['db']['master']['dsn'];
            $user = $_config['db']['master']['user'];
            $pass = $_config['db']['master']['pass'];
        }

        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        try {
            $this->dbh = new \PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die('DB Error: ' . $e->getMessage() . "<br />\n");
        }
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    }

    /**
     * create a new data set
     *
     * @param array $_data
     * @return integer
     * @throws \Exception
     */
    public function create($_data)
    {

        if (empty($this->tablename) || empty($_data) || !is_array($_data)) {
            throw new \Exception('insufficient data');
        }
        $names = '`' . join('`, `', array_keys($_data)) . '`';
        $questionmarks = join(', ', array_fill(0, sizeof(array_keys($_data)), '?'));
        $values = array_values($_data);
        $sql = "INSERT INTO `" . $this->tablename . '` (' . $names . ') VALUES (' . $questionmarks . ')';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);
        return $this->dbh->lastInsertId();

    }

    /**
     * read a data set
     *
     * @param integer $_id
     * @return array
     * @throws \Exception
     */
    public function read($_id)
    {

        if (empty($this->tablename) || empty($_id)) {
            throw new \Exception('insufficient data');
        }
        $sql = "SELECT * FROM `" . $this->tablename . "` WHERE id=" . (int)$_id;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    /**
     * update a data set
     *
     * @param integer $_id
     * @param array $_data
     * @throws \Exception
     */
    public function update($_id, $_data)
    {

        if (empty($this->tablename) || empty($_id) || empty($_data) || !is_array($_data)) {
            throw new \Exception('insufficient data');
        }
        $names = array();
        foreach (array_keys($_data) as $key) {
            $names[] = $key . '=?';
        }
        $values = array_values($_data);
        $values[] = $_id;
        $sql = "UPDATE `" . $this->tablename . "` SET " . join(', ', $names) . " WHERE id=?";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);

    }

    /**
     * Delete a data set
     *
     * @param integer $_id
     * @throws \Exception
     */
    public function delete($_id)
    {

        if (empty($this->tablename) || empty($_id)) {
            throw new \Exception('insufficient data');
        }
        $sql = "DELETE FROM `" . $this->tablename . "` WHERE id=" . (int)$_id;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

    }

    /**
     * Get a paged list
     *
     * @param integer $_first (optional)
     * @param integer $_length (optional)
     * @param string $_sortColumn (optional)
     * @param string $_sortDirection (optional)
     * @return array
     */
    public function getList($_first = null, $_length = null, $_sortColumn = null, $_sortDirection = 'asc')
    {

        $sql = "SELECT * FROM `".$this->tablename."`";
        if ($_sortColumn) {
            if ($_sortDirection != 'desc') {
                $_sortDirection = 'asc';
            }
            $sql .= ' ORDER BY ' . $_sortColumn . ' ' . strtoupper($_sortDirection);

        }
        if (isset($_first) && isset($_length)) {
            $sql .= ' LIMIT ' . (int)$_first . ',' . (int)$_length;
        }

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $list;
    }

    /**
     * Get the number of entries the list
     *
     * @return integer
     */
    public function getListEntries()
    {

        $sql = "SELECT COUNT(*) as entries FROM `".$this->tablename."`";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['entries'];
    }


    /**
     * Search for a user
     *
     * @param string $_searchterm
     * @param integer $_first
     * @param integer $_length
     * @param string $_sortColumn
     * @param string $_sortDirection
     * @param string $_searchcolumn (default columnname is 'name')
     * @return array users
     */
    public function search($_searchterm, $_first = null, $_length = null, $_sortColumn = null, $_sortDirection = 'asc',$_searchcolumn='name')
    {

        $sql = "SELECT * FROM `".$this->tablename."` WHERE ".$_searchcolumn." LIKE " . $this->dbh->quote($_searchterm);
        if ($_sortColumn) {
            if ($_sortDirection != 'desc') {
                $_sortDirection = 'asc';
            }
            $sql .= ' ORDER BY ' . $_sortColumn . ' ' . strtoupper($_sortDirection);

        }
        if (isset($_first) && isset($_length)) {
            $sql .= ' LIMIT ' . (int)$_first . ',' . (int)$_length;
        }

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $list;

    }

    /**
     * Get the number of results for a search
     *
     * @param string $_searchterm
     * @param string $_searchcolumn (default columnname is 'name')
     * @return array
     */
    public function getSearchEntries($_searchterm, $_searchcolumn='name')
    {

        $sql = "SELECT COUNT(*) as entries FROM `".$this->tablename."` WHERE ".$_searchcolumn." LIKE " . $this->dbh->quote($_searchterm);

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['entries'];

    }

}
