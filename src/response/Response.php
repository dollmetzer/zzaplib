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

namespace dollmetzer\zzaplib\response;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\session\Session;

/**
 * Class Response
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\response
 */
class Response implements ResponseInterface
{

    const MESSAGE_TYPE_ERROR = 'error';

    const MESSAGE_TYPE_NOTIFICATION = 'notification';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $messageTypes;

    /**
     * Response constructor.
     *
     * @param Config $config
     * @param Session $session
     */
    public function __construct(Config $config, Session $session)
    {

        $this->config = $config;
        $this->session = $session;
        $this->messageTypes = [
            self::MESSAGE_TYPE_ERROR,
            self::MESSAGE_TYPE_NOTIFICATION
        ];
    }

    /**
     * Redirect to another URL and optionally leave a flash message
     *
     * @param string $url
     * @param string $message     optinal flash message
     * @param string $messageType optional flash message type. Valid values are 'error' and 'notification'
     */
    public function redirect(string $url, string $message='', string $messageType='error')
    {

        if(!in_array($messageType, $this->messageTypes)) {
            $messageType = self::MESSAGE_TYPE_ERROR;
        }

        if(!empty($message)) {
            $this->session->set('flashMessage', $message);
            $this->session->set('flashMessageType', $messageType);
        }

        if(!defined('UNIT_TEST')) {
            header('Location: ' . $url);
            exit;
        }

    }

}