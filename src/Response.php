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
 * API response class
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2018 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Response
{

    /**
     * @var array Valid HTTP statis codes with the corrosponding messages
     */
    protected $HTTP_STATUS;

    /**
     * @var int HTTP status code of the response
     */
    protected $statusCode;

    /**
     * @var mixed HTTP status message of the response
     */
    protected $statusMessage;

    /**
     * @var string Additional status information
     */
    protected $statusInfo;

    /**
     * @var array Payload of the response
     */
    protected $data;


    /**
     * Response constructor.
     *
     * Sets default values
     */
    public function __construct()
    {

        $this->HTTP_STATUS = array(
            // 1xx - Informations not implemented
            // 2xx - Successful operations
            200 => 'OK', // success
            201 => 'Created', // ressource was created. "Location“-header-field may contain Address of the ressource
            202 => 'Accepted', // request was queued and maybe later executed
            // 3xx - Redirections not implemented
            // 4xx - Client errors
            400 => 'Bad Request', // syntax errors. 422 is semantic errors
            401 => 'Unauthorized', // no authentication sent
            403 => 'Forbidden', //
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            422 => 'Unprocessable Entity', // semantic errors
            423 => 'Locked',
            429 => 'Too Many Requests',
            451 => 'Unavailable For Legal Reasons',
            // 5xx - Server errors
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            503 => 'Service Unavailable',
            507 => 'Insufficient Storage'
        );

        $this->statusCode = 200;
        $this->statusMessage = $this->HTTP_STATUS[200];
        $this->statusInfo = '';
        $this->data = array();

    }

    /**
     * Returns response as an array
     *
     * @return array
     */
    public function getAsArray()
    {

        $response = array();
        $response['statusCode'] = $this->statusCode;
        $response['statusMessage'] = $this->statusMessage;
        if (!empty($this->statusInfo)) {
            $response['statusInfo'] = $this->statusInfo;
        }
        $response['data'] = $this->data;

        return $response;

    }

    /**
     * Returns the HTTP status code
     *
     * @return int HTTP status code
     */
    public function getStatusCode()
    {

        return $this->statusCode;

    }

    /**
     * Set HTTP status code and message
     *
     * @param integer $_code Valid HTTP status code
     * @return bool Success. False if unsupported status code
     */
    public function setStatusCode($_code)
    {

        if (!in_array($_code, array_keys($this->HTTP_STATUS)) ) return false;

        $this->statusCode = $_code;
        $this->statusMessage = $this->HTTP_STATUS[$_code];

        return true;

    }

    /**
     * Returns the status message corrosponding to the status code
     * (No setter for the value!)
     *
     * @return string
     */
    public function getStatusMessage()
    {

        return $this->statusMessage;

    }

    /**
     * Returns the additional status info
     *
     * @return string
     */
    public function getStatusInfo()
    {

        return $this->statusInfo;

    }

    /**
     * Setter for the additional status info
     *
     * @param string $_info
     * @return bool Success. False, if parameter was no string
     */
    public function setStatusInfo($_info)
    {

        if (!is_string($_info)) return false;

        $this->statusInfo = $_info;

        return true;

    }

    /**
     * Returns the payload data
     *
     * @return array
     */
    public function getData()
    {

        return $this->data;

    }

    /**
     * Set data (payload)
     *
     * @param array $_data
     * @return bool Success. False if data wasn't an array
     */
    public function setData($_data)
    {

        if (!is_array($_data)) return false;

        $this->data = $_data;

        return true;

    }

}