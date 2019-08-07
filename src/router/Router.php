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

namespace dollmetzer\zzaplib\router;

/**
 * Class PathRouter
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\router
 */
class Router implements RouterInterface
{

    private $queryString = '';

    private $module = 'index';

    private $controller = 'index';

    private $action = 'index';

    private $params = [];


    public function __construct()
    {

    }

    public function resolve()
    {

        if (empty($_GET['q'])) {
            return;
        }

        $this->queryString = $_GET['q'];
        $query = $this->cleanQueryPath($this->queryString);

        if (sizeof($query) > 2) {
            $this->module = array_shift($query);
        }

        if (sizeof($query) > 1) {
            $this->controller = array_shift($query);
        }

        if (sizeof($query) > 0) {
            $this->action = array_shift($query);
        }

        if (sizeof($query) > 0) {
            $this->params = $query;
        }

    }

    /**
     * @return string
     */
    public function getModule() : string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getController() : string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams() : array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }


    /**
     * @param string $path
     * @return array
     */
    protected function cleanQueryPath(string $path) : array
    {

        $raw = explode('/', $path);
        $query = [];
        for ($i = 0; $i < sizeof($raw); $i++) {
            if ($raw[$i] != '') {
                array_push($query, $raw[$i]);
            }
        }
        return $query;

    }

    /**
     * Build a complete URL from a query string
     *
     * @param string $path Query string like controller/action/param_1/param_n
     * @param array $attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL(string $path, array $attributes = [])
    {
        if (URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $url = $protocol . URL_BASE;
        if (URL_REWRITE) {
            $url .= '/' . $path;
        } else {
            if (!empty($path)) {
                $url .= '/index.php?q=' . $path;
            }
        }
        if (!empty($attributes)) {
            $addition = [];
            foreach ($attributes as $key => $val) {
                $addition[] = $key . '=' . urlencode($val);
            }
            $url .= '&' . join('&', $addition);
        }
        return $url;
    }

    /**
     * Build a complete URL for media files
     *
     * @param string $path Path to media file
     * @return string
     */
    public function buildMediaURL(string $path)
    {
        if (URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $url = $protocol . URL_MEDIA . '/' . $path;
        return $url;
    }

}