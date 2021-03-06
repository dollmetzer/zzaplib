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
 * Form class
 *
 * Class for processing user input forms
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Form
{

    /**
     * @var Request $request The Request object
     */
    protected $request;

    /**
     * @var View $view The View object
     */
    protected $view;

    /**
     * @var string $name Name of the form
     */
    public $name;

    /**
     * @var string $title Title of the form
     */
    public $title;

    /**
     * @var string $action optional. If empty, form calls itself
     */
    public $action;

    /**
     * @var boolean $hasErrors Indicates, if an error occured during the processing
     */
    public $hasErrors;

    /**
     * @var array $fields Fields of the form
     */
    public $fields;

    /**
     * Form constructor.
     *
     * Basic setting for the form
     *
     * @param Request $_request
     * @param View $_view
     */
    public function __construct(Request $_request, View $_view)
    {
        $this->request = $_request;
        $this->view = $_view;
        $this->name = '';
        $this->title = '';
        $this->action = '';
        $this->hasErrors = false;
    }

    /**
     * Process the form
     *
     * - set default values
     * - if form was submitted, read and validate all fields
     *
     * @return boolean Success
     */
    public function process()
    {

        // set default values
        $this->hasErrors = false;
        foreach ($this->fields as $name => $field) {
            if (!in_array($field['type'], array('static', 'divider', 'submit'))) {
                if (!isset($field['value'])) {
                    $this->fields[$name]['value'] = '';
                }
            }
        }

        // without input - return without processing
        if (empty($_POST)) {
            return false;
        }

        // read an validate all fields (except special types)
        foreach ($this->fields as $name => $field) {
            if (!in_array($field['type'], array('static', 'divider', 'submit'))) {
                // fetch
                if ($field['type'] == 'checkbox') {
                    if (empty($_POST[$name])) {
                        $value = false;
                    } else {
                        $value = true;
                    }
                } elseif ($field['type'] == 'file') {
                    // check, if file was sent...
                    $value = $_FILES[$name]['name'];

                    // check mimetype
                    // check filesize
                } else {
                    if (isset($_POST[$name])) {
                        $value = $_POST[$name];
                    } else {
                        $value = null;
                    }
                }
                // preprocessing
                $this->fields[$name]['value'] = $value;
                // validating
                $this->validate($name);
            }
        }

        // process didn't succeeded
        if ($this->hasErrors !== false) {
            return false;
        }

        // processed succeeded
        return true;
    }

    /**
     * Returns all data for the rendering of the form template
     * @return array
     */
    public function getViewdata()
    {

        return array(
            'name' => $this->name,
            'title' => $this->title,
            'action' => $this->action,
            'hasErrors' => $this->hasErrors,
            'fields' => $this->fields
        );
    }

    /**
     * Returns an array with name-value pairs of all fields
     *
     * @return array
     */
    public function getValues()
    {

        $result = array();
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

    /**
     * Validate a form fields
     * If an error occurred, the field gets an error entry and the globas $hasError flag is set true
     *
     * @param string $_name Name of the field
     */
    public function validate($_name)
    {

        // required
        if (!empty($this->fields[$_name]['required'])) {
            if (empty($this->fields[$_name]['value'])) {
                $this->fields[$_name]['error'] = $this->view->lang('form_error_required', false);
                $this->hasErrors = true;
                return;
            }
        }

        // minlength check, if value is not empty
        if (!empty($this->fields[$_name]['minlength'])) {
            $length = strlen($this->fields[$_name]['value']);
            if (($length > 0) && ($length < $this->fields[$_name]['minlength'])) {
                $this->fields[$_name]['error'] = sprintf($this->view->lang('form_error_minlength', false),
                    $this->fields[$_name]['minlength']);
                $this->hasErrors = true;
                return;
            }
        }
        // maxlength
        if (!empty($this->fields[$_name]['maxlength'])) {
            if (strlen($this->fields[$_name]['value']) > $this->fields[$_name]['maxlength']) {
                $this->fields[$_name]['error'] = sprintf($this->view->lang('form_error_maxlength', false),
                    $this->fields[$_name]['maxlength']);
                $this->hasErrors = true;
                return;
            }
        }

        // min
        if (!empty($this->fields[$_name]['min'])) {
            if ($this->fields[$_name]['value'] < $this->fields[$_name]['min']) {
                $this->fields[$_name]['error'] = sprintf($this->view->lang('form_error_min', false),
                    $this->fields[$_name]['min']);
                $this->hasErrors = true;
                return;
            }
        }
        // max
        if (!empty($this->fields[$_name]['max'])) {
            if ($this->fields[$_name]['value'] > $this->fields[$_name]['max']) {

                $this->fields[$_name]['error'] = sprintf($this->view->lang('form_error_max', false),
                    $this->fields[$_name]['max']);
                $this->hasErrors = true;
                return;
            }
        }

        // type integer
        if ($this->fields[$_name]['type'] == 'integer') {
            $value = (int)$this->fields[$_name]['value'];
            if ((string)$value != $this->fields[$_name]['value']) {
                $this->fields[$_name]['error'] = sprintf($this->view->lang('form_error_integer', false),
                    $this->fields[$_name]['maxlength']);
                $this->hasErrors = true;
                return;
            }
        }

        if ($this->fields[$_name]['type'] == 'date') {
            $value = $this->fields[$_name]['value'];
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value)) {
                $this->fields[$_name]['error'] = sprintf($this->view->lang('form_error_date', false),
                    $this->fields[$_name]['maxlength']);
                $this->hasErrors = true;
                return;
            }
        }

    }
}
