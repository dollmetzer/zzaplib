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
 * Mail class
 *
 * Mail wrapper for template based e-mails
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Mail
{

    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var Session $session
     */
    protected $session;

    /**
     * @var Request $request
     */
    protected $request;


    /**
     * Mail constructor.
     *
     * @param array $_config
     * @param Session $_session
     * @param Request $request
     */
    public function __construct($_config, Session $_session, Request $request)
    {

        $this->config = $_config;
        $this->session = $_session;
        $this->request = $request;

    }

    /**
     * Send an e-mail
     *
     * @param string $_template
     * @param array $_data
     * @param string $_subject
     * @param string $_to
     * @param string $_cc
     * @param string $_from
     * @param string $_replyto
     * @return bool success
     */
    public function send($_template, $_data, $_subject, $_to, $_cc = '', $_from = '', $_replyto = '')
    {

        // check parameters
        if (empty($_from)) {
            $_from = $this->config['mail']['from'];
        }

        // process message text
        $mailView = new View($this->session, $this->request);
        $mailView->content = $_data;
        $message = $mailView->render(true, $_template);

        /* for later... if multipart mail
        $boundary = uniqid('np');
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        */

        // send mail direct or by spooler
        if (!empty($this->config['mail']['spool'])) {
            if ($this->config['mail']['spool'] === true) {
                $this->send2Spooler($message, $_subject, $_to, $_cc, $_from, $_replyto);
                return true;
            }
        }

        return $this->sendDirect($message, $_subject, $_to, $_cc, $_from, $_replyto);

    }

    /**
     * Send the mail direct via your local MTA
     *
     * @param string $_template Path to the mail template
     * @param array $_data Data for the template processing
     * @param string $_subject mail subject
     * @param string $_to recipient
     * @param string $_cc additional recipients
     * @param string $_from sender
     * @param string $_replyto separate reply-to address
     * @return bool success
     * @throws \Exception
     */
    protected function sendDirect($_message, $_subject, $_to, $_cc, $_from, $_replyto)
    {

        $subject = $_subject;

        $to = mb_encode_mimeheader($_to);
        $sender = mb_encode_mimeheader($_from);
        $replyto = mb_encode_mimeheader($_replyto);
        $_subject = "=?utf-8?b?" . base64_encode($_subject) . "?=";

        $headers = "Mime-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $sender";
        if (!empty($replyto)) {
            $headers .= "\r\nReply-To: $replyto";
        }

        $success = mb_send_mail($to, $subject, $_message, $headers);
        if ($success !== true) {
            $this->request->log('Sending Mail to ' . $to . ' failed');
        }
        return $success;

    }

    /**
     * Not implemented yet
     *
     * @param string $_template Path to the mail template
     * @param array $_data Data for the template processing
     * @param string $_subject mail subject
     * @param string $_to recipient
     * @param string $_cc additional recipients
     * @param string $_from sender
     * @param string $_replyto separate reply-to address
     * @return bool success
     */
    protected function send2Spooler($_template, $_data, $_subject, $_to, $_cc, $_from, $_replyto)
    {

        return false;

    }

}