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
use dollmetzer\zzaplib\request\RequestInterface;
use dollmetzer\zzaplib\response\ResponseInterface;
use dollmetzer\zzaplib\session\SessionInterface;
use dollmetzer\zzaplib\translator\TranslatorInterface;

/**
 * Class View
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class View implements ViewInterface
{

    /**
     * @var Config
     */
    private $config;

    private $router;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $content;

    /**
     * @var array
     */
    private $cssFiles;

    /**
     * @var array
     */
    private $jsFiles;

    /**
     * View constructor.
     * @param Config $config
     * @param Router $router
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Config $config,
        Router $router,
        RequestInterface $request,
        ResponseInterface $response,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->config = $config;
        $this->router = $router;
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->translator = $translator;
        $this->content = [];
        $this->cssFiles = [];
        $this->jsFiles = [];
    }

    /**
     * @param string $name
     * @param $value
     */
    public function addContent(string $name, $value)
    {
        $this->content[$name] = $value;
    }

    /**
     * @param string $url
     */
    public function addCSSFile(string $url)
    {
        $this->cssFiles[] = $url;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return $this->cssFiles;
    }

    /**
     * @param string $url
     */
    public function addJSFile(string $url)
    {
        $this->jsFiles[] = $url;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return $this->jsFiles;
    }

    /**
     * @param bool $capture
     * @param string $template
     * @throws \Exception
     */
    public function render($capture = false, $template = '')
    {

        $content = &$this->content;
        $request = $this->request;
        $session = $this->session;
        $viewhelper = new Viewhelper($this->config, $this->router, $this->translator);

        if (!empty($this->template)) {
            $filename = PATH_APP . $this->template;
        } else {
            $filename = PATH_APP . 'modules/' . $this->request->getModule();
            $filename .= '/views/' . $this->request->getController() . '/';
            $filename .= $this->request->getAction() . '.php';
        }
        if (!empty($template)) {
            $filename = PATH_APP . $template;
        }
        if (!file_exists($filename)) {
            throw new \Exception("View: Template $filename not found.");
        }

        // direct output...
        if ($capture === false) {
            include $filename;
            return;
        }

        /// ... or capture rendering (later)
        /*
        ob_start();
        include $filename;
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
        */
    }

}