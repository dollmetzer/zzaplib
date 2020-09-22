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
 * Class Table
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\data
 */
class Table
{

    /**
     * @var string $title Title of the table (panel head)
     */
    protected $title;

    /**
     * @var string $title Title of the table (panel body)
     */
    protected $description;

    /**
     * @var array Columns Array of Key=>Attribute Arrays
     */
    protected $columns;

    /**
     * @var array Array of data rows as associative array
     */
    protected $rows;

    /**
     * @var Pagination
     */
    protected $pagination;

    /**
     * Basic setting for the table
     *
     */
    public function __construct()
    {
        $this->title = '';
        $this->description = '';
        $this->pagination = new Pagination();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Return Columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set columns
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = [];
        foreach ($columns as $key => $attrib) {
            if (!empty($attrib['type'])) {
                $this->columns[$key]['type'] = $attrib['type'];
            } else {
                $this->columns[$key]['type'] = 'text';
            }

            if (!empty($attrib['width'])) {
                $this->columns[$key]['width'] = $attrib['width'];
            } else {
                $this->columns[$key]['width'] = '';
            }

            if (!empty($attrib['sortable'])) {
                $this->columns[$key]['sortable'] = $attrib['sortable'];
            } else {
                $this->columns[$key]['sortable'] = false;
            }
        }
    }

    /**
     * Return rows
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Insert all cells of a data matrix into the table, if defined in $this->columns
     *
     * @param array $rows
     * @return boolean False, if no columns were defined before this method call
     */
    public function setRows(array $rows)
    {
        if (empty($this->columns)) {
            return false;
        }

        foreach ($rows as $row) {
            $newRow = [];
            foreach (array_keys($this->columns) as $key) {
                if (isset($row[$key])) {
                    $newRow[$key] = $row[$key];
                }
            }
            $this->rows[] = $newRow;
        }
        return true;
    }

    /**
     * @return Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
