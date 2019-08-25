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

namespace dollmetzer\zzaplib;

use dollmetzer\zzaplib\request\RequestInterface;
use dollmetzer\zzaplib\response\ResponseInterface;
use dollmetzer\zzaplib\router\RouterInterface;
use dollmetzer\zzaplib\session\SessionInterface;
use dollmetzer\zzaplib\logger\LoggerInterface;
use dollmetzer\zzaplib\translator\TranslatorInterface;
use dollmetzer\zzaplib\view\ViewInterface;

/**
 * Class Application
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Application
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * Application constructor.
     *
     * @param $configFile
     * @throws exception\ApplicationException
     */
    public function __construct($configFile)
    {
        $this->config = new Config($configFile);
        $this->getLogger();
        $this->getSession();
        $this->getRouter();
        $this->getRequest();
        $this->getResponse();
        $this->getTranslator();
        $this->getView();
    }

    /**
     * @return bool
     * @throws exception\ApplicationException
     * @throws exception\BagException
     */
    public function run()
    {

        if (DEBUG_REQUEST) {
            echo "\n<!-- REQUEST\n";
            echo 'Module     : ' . $this->router->getModule() . "\n";
            echo 'Controller : ' . $this->router->getController() . "\n";
            echo 'Action     : ' . $this->router->getAction() . "\n";
            echo "Parameters : \n";
            print_r($this->router->getParams());
            echo "\n-->\n";
        }

        // load core language snippets
        $this->translator->importLanguage('de');

        // load controller
        $controllerName = '\Application\modules\\' . $this->router->getModule() . '\controllers\\';
        $controllerName .= ucfirst(strtolower($this->router->getController()));
        $controllerName .= 'Controller';

        // exists controller class?
        if (class_exists($controllerName)) {
            $controller = new $controllerName($this->config, $this->logger, $this->router, $this->request, $this->response, $this->session, $this->translator, $this->view);
            $this->translator->importLanguage($this->session->get('userLanguage'), $this->router->getModule(), $this->router->getController());
        } else {
            $this->logger->error('Controller class ' . $controllerName . ' not found');
            $this->response->redirect($this->router->buildURL(''), $this->translator->translate('error_core_illegal_parameter'));
        }

        // exists action method?
        $actionName = $this->router->getAction() . 'Action';
        if (method_exists($controller, $actionName) === false) {
            $this->logger->error('Action method ' . $actionName . ' not found in controller ' . $controllerName);
            $this->response->redirect('', $this->translator->translate('error_core_illegal_parameter'));
        }

        // is action allowed?
        // ...

        try {

            if (method_exists($controller, 'before')) {
                $controller->before();
            }

            $controller->$actionName();

            if (method_exists($controller, 'after')) {
                $controller->after();
            }

        } catch (\Exception $e) {
            $message = 'Application error in ';
            $message .= $e->getFile() . ' in Line ';
            $message .= $e->getLine() . ' : ';
            $message .= $e->getMessage();
            $this->logger->error($message);
        }

        $this->view->render();

        $this->session->set('flashMessage', '');
        $this->session->set('flashMessageType', '');

    }

    /**
     * @throws exception\ApplicationException
     */
    private function getSession()
    {
        if ($this->config->isSet('session', 'application')) {
            $className = $this->config->get('session', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\session\Session';
        }
        $this->session = new $className($this->config);
    }

    /**
     * @throws exception\ApplicationException
     */
    private function getRequest()
    {
        if ($this->config->isSet('request', 'application')) {
            $className = $this->config->get('config')->get('request', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\request\Request';
        }
        $this->request = new $className($this->config, $this->router);
    }

    /**
     * @throws exception\ApplicationException
     */
    private function getResponse()
    {
        if ($this->config->isSet('response', 'application')) {
            $className = $this->config->get('response', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\response\Response';
        }
        $this->response = new $className($this->config, $this->session);
    }

    /**
     * @throws exception\ApplicationException
     */
    private function getRouter()
    {
        if ($this->config->isSet('router', 'application')) {
            $className = $this->config->get('router', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\router\Router';
        }
        $this->router = new $className($this->config);
    }

    /**
     * @throws exception\ApplicationException
     */
    private function getTranslator()
    {
        if ($this->config->isSet('translator', 'application')) {
            $className = $this->config->get('translator', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\translator\Translator';
        }
        $this->translator = new $className($this->config, $this->logger);
    }

    /**
     * @throws exception\ApplicationException
     */
    private function getView()
    {
        if ($this->config->isSet('view', 'application')) {
            $className = $this->config->get('view', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\view\View';
        }
        $this->view = new $className($this->config, $this->router, $this->request, $this->response, $this->session, $this->translator);
    }

    /**
     * @throws exception\ApplicationException
     */
    private function getLogger()
    {
        if ($this->config->isSet('logger', 'application')) {
            $className = $this->config->get('logger', 'application');
        } else {
            $className = 'dollmetzer\zzaplib\logger\Logger';
        }
        $this->logger = new $className($this->config);
    }

}