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

namespace dollmetzer\zzaplib\view;


use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\router\Router;
use dollmetzer\zzaplib\translator\Translator;

/**
 * Class Viewhelper
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Viewhelper
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Viewhelper constructor.
     *
     * @param Config $config
     * @param Router $router
     * @param Translator $translator
     */
    public function __construct(Config $config, Router $router, Translator $translator)
    {
        $this->config = $config;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * Build a URL with a query path considering server settings
     *
     * @param string $path Path part of the URL
     * @param boolean $output Direct output(default) in the template or return value for use in Controller
     * @return string|null
     */
    public function buildURL(string $path, $output = true)
    {
        $url = $this->router->buildURL($path);
        if ($output === true) {
            echo $url;
            return;
        }
        return $url;
    }

    /**
     * Build a URL for media files considering server settings
     *
     * @param string $path Path part of the URL
     * @param boolean $output Direct output(default) in the template or return value for use in Controller
     * @return string|null
     */
    public function buildMediaURL(string $path, $output = true)
    {
        $url = $this->router->buildMediaURL($path);
        if ($output === true) {
            echo $url;
            return;
        }
        return $url;
    }

    /**
     * Get a translated test for a given snippet
     *
     * @param string $snippet
     * @param bool $output
     * @return string|void
     */
    public function translate(string $snippet, $output = true)
    {
        $text = $this->translator->translate($snippet);
        if ($output === true) {
            echo $text;
            return;
        }
        return $text;
    }

}