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

namespace dollmetzer\zzaplib\controller;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\logger\LoggerInterface;
use dollmetzer\zzaplib\router\RouterInterface;
use dollmetzer\zzaplib\request\RequestInterface;
use dollmetzer\zzaplib\response\ResponseInterface;
use dollmetzer\zzaplib\session\SessionInterface;
use dollmetzer\zzaplib\translator\TranslatorInterface;
use dollmetzer\zzaplib\view\View;

/**
 * Class WebController
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\controller
 */
class WebController
{

    protected $config;

    protected $logger;

    protected $router;

    protected $request;

    protected $response;

    protected $session;

    protected $translator;

    protected $view;

    public function __construct(
        Config $config,
        LoggerInterface $logger,
        RouterInterface $router,
        RequestInterface $request,
        ResponseInterface $response,
        SessionInterface $session,
        TranslatorInterface $translator,
        View $view
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->router = $router;
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->translator = $translator;
        $this->view = $view;
    }
}
