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

namespace dollmetzer\zzaplib\data;

/**
 * Class Form
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\data
 */
class Form
{
    /**
     * @var string $name Name of the form
     */
    protected $name;

    /**
     * @var string $title Title of the form
     */
    protected $title;

    /**
     * @var string $action optional. If empty, form calls itself
     */
    protected $action;

    /**
     * @var string Error message for the whole form
     */
    protected $error;

    /**
     * @var boolean $hasErrors Indicates, if an error occured during the processing
     */
    protected $hasErrors;

    /**
     * @var array $fields Fields of the form
     */
    protected $fields;


    public function __construct()
    {
        $this->name = '';
        $this->title = '';
        $this->action = '';
        $this->error = '';
        $this->hasErrors = false;
        $this->fields = [];
    }

    /**
     * @return bool
     */
    public function process() : bool
    {
        // set default values
        $this->hasErrors = false;

        // without input - return without processing
        if (empty($_POST)) {
            return false;
        }

        $this->fetchInput();

        $success = true;
        if($this->hasErrors === true) {
            $success = false;
        }

        return $success;
    }

    /**
     * Returns all data for the rendering of the form template
     *
     * @return array
     */
    public function getViewdata() : array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'action' => $this->action,
            'error' => $this->error,
            'hasErrors' => $this->hasErrors,
            'fields' => $this->fields
        ];
    }

    /**
     * Returns array of field values
     *
     * @return array
     */
    public function getValues() : array
    {
        $result = [];
        foreach ($this->fields as $name => $field) {
            if (empty($field['value'])) continue;
            if ($field['type'] == 'code') {
                $result[$name] = $field['value'];
            } else {
                $result[$name] = htmlentities($field['value']);
            }
        }
        return $result;
    }

    protected function fetchInput() {
        foreach ($this->fields as $name => $field) {

            // skip special types
            if(in_array($field['type'], ['static', 'divider', 'submit'])) continue;

            if ($field['type'] == 'checkbox') {
                $value = $this->fetchCheckbox($name);
            } elseif ($field['type'] == 'datetime') {
                $value = $this->fetchDatetime($name);
            } elseif ($field['type'] == 'file') {
                $value = $this->fetchFilename($name);
            } else {
                $value = $this->fetchText($name);
            }
            // preprocessing
            $this->fields[$name]['value'] = $value;
            // validating
            $this->validate($name);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function fetchCheckbox(string $name) : bool
    {
        if (empty($_POST[$name])) {
            $value = false;
        } else {
            $value = true;
        }
        return $value;
    }

    /**
     * @param string $name
     * @return string|null
     */
    protected function fetchDatetime(string $name)
    {
        // Assemble field from two separate fields
        $day = $_POST[$name.'_day'];
        $time = $_POST[$name.'_time'];
        if(!empty($day) || !empty($time)) {
            $tmp = explode(':', $time);
            if(sizeof($tmp) == 2) {
                $time = $time.':00';
            }
            $value = $day.' '.$time;
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * @param string $name
     * @return null|string
     */
    protected function fetchFilename(string $name)
    {
        // check, if file was sent...
        $value = $_FILES[$name]['name'];
        // check mimetype
        // check filesize
        return $value;
    }

    /**
     * @param string $name
     * @return null|string
     */
    protected function fetchText(string $name)
    {
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        } else {
            $value = null;
        }
        return $value;
    }

    protected function validate($name)
    {

    }


    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getError() : string
    {
        return $this->error;
    }

    /**
     * @param string $message
     * @param string $field (optional)
     */
    public function setError(string $message, string $field='')
    {
        if(empty($field)) {
            $this->error = $message;
        } else {
            $this->fields[$field] = $message;
        }
        $this->hasErrors = true;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->hasErrors;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

}