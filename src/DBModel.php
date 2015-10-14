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
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class DBModel
{

    /**
     * @var Application $app Holds the instance of the application 
     */
    public $app;

    /**
     * Connects to DB. Use an already existing connection or open a new one
     * 
     * @param Application $_app
     * @param boolean $_master Use Master DB (default = slave) 
     */
    public function __construct($_app, $_master = false)
    {

        $this->app = $_app;

        if (empty($this->app->dbh)) {
            if ($_master === false) {
                $dsn = $this->app->config['core']['db']['slave']['dsn'];
                $user = $this->app->config['core']['db']['slave']['user'];
                $pass = $this->app->config['core']['db']['slave']['pass'];
            } else {
                $dsn = $this->app->config['core']['db']['master']['dsn'];
                $user = $this->app->config['core']['db']['master']['user'];
                $pass = $this->app->config['core']['db']['master']['pass'];
            }

            $options = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );

            try {
                $this->app->dbh = new \PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die('DB Error: ' . $e->getMessage() . "<br />\n");
            }
            $this->app->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * create a new data set
     * 
     * @param array $_data
     * @return integer
     * @throws Exception
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
        $stmt = $this->app->dbh->prepare($sql);
        $stmt->execute($values);
        return $this->app->dbh->lastInsertId();
    }

    /**
     * read a data set
     * 
     * @param integer $_id
     * @return array
     * @throws Exception
     */
    public function read($_id)
    {

        if (empty($this->tablename) || empty($_id)) {
            throw new \Exception('insufficient data');
        }
        $sql = "SELECT * FROM `" . $this->tablename . "` WHERE id=" . (int) $_id;
        $stmt = $this->app->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * update a data set
     * 
     * @param integer $_id
     * @param array $_data
     * @throws Exception
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
        $stmt = $this->app->dbh->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * Delete a data set
     * 
     * @param integer $_id
     * @throws Exception
     */
    public function delete($_id)
    {

        if (empty($this->tablename) || empty($_id)) {
            throw new \Exception('insufficient data');
        }
        $sql = "DELETE FROM `" . $this->tablename . "` WHERE id=" . (int) $_id;
        $stmt = $this->app->dbh->prepare($sql);
        $stmt->execute();
    }

}

?>
