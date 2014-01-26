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

/**
 * Description of DBModel
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2014 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class DBModel {

    /**
     * Connects to DB. Use an already existing connection or open a new one
     * 
     * @param Application $_app
     * @param boolean $_master Use Master DB (default = slave) 
     */
    public function __construct($_app, $_master = false) {

        $this->app = $_app;
        if ($this->app->dbh === NULL) {
            if ($_master === false) {
                $dsn = $this->app->config['db']['slave']['dsn'];
                $user = $this->app->config['db']['slave']['user'];
                $pass = $this->app->config['db']['slave']['pass'];
            } else {
                $dsn = $this->app->config['db']['master']['dsn'];
                $user = $this->app->config['db']['master']['user'];
                $pass = $this->app->config['db']['master']['pass'];
            }

            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );

            try {
                $this->app->dbh = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die('DB Error: ' . $e->getMessage() . "<br />\n");
            }
            $this->app->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

}

?>
