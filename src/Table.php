<?php
/**
 * z z a p l i b    m i n i   f r a m e w o r k
 * ============================================
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
 * Class for table (datagrid) handling
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Table
{

    /**
     * @var string $title Title of the table (panel head)
     */
    public $title;

    /**
     * @var string $title Title of the table (panel body)
     */
    public $description;

    /**
     * @var array Columns Array of Key=>Attribute Arrays
     */
    protected $columns;

    /**
     * @var array Array of data rows as associative array
     */
    protected $rows;

    /**
     * @var integer Pagination: Number of pages left and right of actual page (eg. 3 means 3 left + current + 3 reight = 7 pagelinks)
     */
    public $paginationWidth;

    /**
     * @var integer Pagination: Page number
     */
    public $page;

    /**
     * @var integer Pagination: Highest page number
     */
    public $maxPage;

    /**
     * @var integer Pagination: Max number of entries per Page
     */
    public $entriesPerPage;

    /**
     * @var string URL for pagination link
     */
    public $urlPage;

    /**
     * @var string URL for new item link
     */
    public $urlNew;

    /**
     * @var string URL for item details link
     */
    public $urlDetail;

    /**
     * @var string URL for item edit link
     */
    public $urlEdit;

    /**
     * @var string URL for item delete link
     */
    public $urlDelete;

    /**
     * @var string URL for item active-switch
     */
    public $urlSwitch;

    /**
     * @var string URL for item sorting switch
     */
    public $urlSort;

    /**
     * @var string Name of the sorting column
     */
    public $sortColumn;

    /**
     * @var string Direction of the sorting ('asc' or 'desc')
     */
    public $sortDirection;

    /**
     * Basic setting for the table
     *
     * @param Application $_app The application object
     */
    public function __construct()
    {

        $this->title = '';
        $this->description = '';

        $this->page = 0;
        $this->maxPage = 0;
        $this->entriesPerPage = 10;
        $this->urlPage = '';
        $this->paginationWidth = 3;

        $this->urlNew = '';
        $this->urlDetail = '';
        $this->urlEdit = '';
        $this->urlDelete = '';
        $this->urlSwitch = '';
        $this->urlSort = '';

        $this->sortColumn = '';
        $this->sortDirection = 'asc';

    }

    public function getColumns()
    {

        return $this->columns;

    }

    public function setColumns(array $_columns)
    {

        $this->columns = array();
        foreach ($_columns as $key => $attrib) {

            if (!empty($attrib['type'])) {
                $this->columns[$key]['type'] = $attrib['type'];
            } else {
                $this->columns[$key]['type'] = 'text';
            }

            if (!empty($attrib['width'])) {
                $this->columns[$key]['width'] = $attrib['width'];
            } else {
                $this->columns[$key]['width'] = 'width';
            }

            if (!empty($attrib['sortable'])) {
                $this->columns[$key]['sortable'] = $attrib['sortable'];
            } else {
                $this->columns[$key]['sortable'] = false;
            }

        }

    }

    public function getRows()
    {

        return $this->rows;

    }

    /**
     * Insert all cells of a data matrix into the table, if defined in $this->columns
     *
     * @param array $_rows
     * @return boolean False, if no columns were defined before this method call
     */
    public function setRows(array $_rows)
    {

        if (empty($this->columns)) {
            return false;
        }

        foreach ($_rows as $row) {
            $newRow = array();
            foreach (array_keys($this->columns) as $key) {
                if (isset($row[$key])) {
                    $newRow[$key] = $row[$key];
                }
            }
            $this->rows[] = $newRow;
        }
        return true;

    }


    public function calculateMaxPage($_maxNumRows)
    {

        $this->maxPage = ceil($_maxNumRows / $this->entriesPerPage);

    }

}
