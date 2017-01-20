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
 * Base class for permission handling
 *
 * NOT FOR PRODUCTION READY YET!
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Permission
{
    /**
     * @var integer User Id - Owner of the item
     */
    protected $userId;

    /**
     * @var integer Group Id - Group of the item
     */
    protected $groupId;

    /**
     * @var integer 15 bit encoded access permissions of the item
     */
    protected $permissions;

    /**
     * @var boolean Right to create new item
     */
    protected $isCreate;

    /**
     * @var boolean Right to read item
     */
    protected $isRead;

    /**
     * @var boolean Right to update item
     */
    protected $isUpdate;

    /**
     * @var boolean Right to delete item
     */
    protected $isDelete;

    /**
     * @var boolean Right to execute item
     */
    protected $isExecute;

    /**
     * Create new Permission object
     *
     * @param integer $_userId
     * @param integer $_groupId
     * @param integer $_permissions Permissions are bit based (15 bit)
     */
    public function __construct($_userId = 0, $_groupId = 0, $_permissions = 0)
    {

        $this->userId      = (int) $_userId;
        $this->groupId     = (int) $_groupId;
        $this->permissions = (int) $_permissions;
        $this->calculateRights();
    }

    /**
     * Has right to create?
     *
     * @return type
     */
    public function isCreate()
    {
        return $this->isCreate;
    }

    /**
     * Has right to read?
     *
     * @return type
     */
    public function isRead()
    {
        return $this->isRead;
    }

    /**
     * Has right to update?
     *
     * @return type
     */
    public function isUpdate()
    {
        return $this->isUpdate;
    }

    /**
     * Has right to delete?
     *
     * @return type
     */
    public function isDelete()
    {
        return $this->isDelete;
    }

    /**
     * Has right to execute?
     *
     * @return boolean
     */
    public function isExecute()
    {
        return $this->isExecute;
    }

    public function setCreate(bool $_status=true, $_context='all') {

        if((int)$_status === false) {
            // delete bits
            if($_context == 'user') {
                $this->permissions = $this->permissions & (~16384);
            } else if($_context == 'group') {
                $this->permissions = $this->permissions & (~512);
            } else {
                $this->permissions = $this->permissions & (~16);
            }
        } else {
            // set bits
            if($_context == 'user') {
                $this->permissions = $this->permissions | 16384;
            } else if($_context == 'group') {
                $this->permissions = $this->permissions | 512;
            } else {
                $this->permissions = $this->permissions | 16;
            }
        }

    }

    public function setRead(bool $_status=true, $_context='all') {

        if((int)$_status === false) {
            // delete bits
            if($_context == 'user') {
                $this->permissions = $this->permissions & (~8192);
            } else if($_context == 'group') {
                $this->permissions = $this->permissions & (~256);
            } else {
                $this->permissions = $this->permissions & (~8);
            }
        } else {
            // set bits
            if($_context == 'user') {
                $this->permissions = $this->permissions | 8192;
            } else if($_context == 'group') {
                $this->permissions = $this->permissions | 256;
            } else {
                $this->permissions = $this->permissions | 8;
            }
        }

    }

    public function setUpdate(bool $_status=true, $_context='all') {

        if((int)$_status === false) {
            // delete bits
            if($_context == 'user') {
                $this->permissions = $this->permissions & (~4096);
            } else if($_context == 'group') {
                $this->permissions = $this->permissions & (~128);
            } else {
                $this->permissions = $this->permissions & (~4);
            }
        } else {
            // set bits
            if($_context == 'user') {
                $this->permissions = $this->permissions | 4096;
            } else if($_context == 'group') {
                $this->permissions = $this->permissions | 128;
            } else {
                $this->permissions = $this->permissions | 4;
            }
        }

    }

    public function setDelete(bool $_status=true, $_context='all') {

        if((int)$_status === false) {
            // delete bits
            if($_context == 'user') {
                $this->permissions = $this->permissions & (~2048);
            } else if($_context == 'group') {
                $this->permissions = $this->permissions & (~64);
            } else {
                $this->permissions = $this->permissions & (~2);
            }
        } else {
            // set bits
            if($_context == 'user') {
                $this->permissions = $this->permissions | 2048;
            } else if($_context == 'group') {
                $this->permissions = $this->permissions | 64;
            } else {
                $this->permissions = $this->permissions | 2;
            }
        }

    }

    /**
     * Set Execute Flag in Permission
     *
     * @param integer $_status
     * @param string $_context Set flag for 'user', 'group' or 'all'
     */
    public function setExecute(bool $_status=true, $_context='all') {

        if((int)$_status === false) {
            // delete bits
            if($_context == 'user') {
                $this->permissions = $this->permissions & (~1024);
            } else if($_context == 'group') {
                $this->permissions = $this->permissions & (~32);
            } else {
                $this->permissions = $this->permissions & (~1);
            }
        } else {
            // set bits
            if($_context == 'user') {
                $this->permissions = $this->permissions | 1024;
            } else if($_context == 'group') {
                $this->permissions = $this->permissions | 32;
            } else {
                $this->permissions = $this->permissions | 1;
            }
        }

    }

    /**
     * Calculate rights from userid, groupid and permission
     */
    protected function calculateRights()
    {

        // start without any permission
        $this->isCreate  = false;
        $this->isRead    = false;
        $this->isUpdate  = false;
        $this->isDelete  = false;
        $this->isExecute = false;

        // calculate rights for all (bits 1-5)
        if ($this->permissions & 16) $this->isCreate  = true;
        if ($this->permissions & 8) $this->isRead    = true;
        if ($this->permissions & 4) $this->isUpdate  = true;
        if ($this->permissions & 2) $this->isDelete  = true;
        if ($this->permissions & 1) $this->isExecute = true;

        // calculate rights for group (bits 6-10)
        if (isset($_SESSION['groups'])) {
            if (in_array($this->groupId, array_keys($_SESSION['groups']))) {
                if ($this->permissions & 512) $this->isCreate  = true;
                if ($this->permissions & 256) $this->isRead    = true;
                if ($this->permissions & 128) $this->isUpdate  = true;
                if ($this->permissions & 64) $this->isDelete  = true;
                if ($this->permissions & 32) $this->isExecute = true;
            }
        }

        // calculate rights for user (bits 11-15)
        if (isset($_SESSION['user_id'])) {
            if ($this->userId == $_SESSION['user_id']) {
                if ($this->permissions & 16384) $this->isCreate  = true;
                if ($this->permissions & 8192) $this->isRead    = true;
                if ($this->permissions & 4096) $this->isUpdate  = true;
                if ($this->permissions & 2048) $this->isDelete  = true;
                if ($this->permissions & 1024) $this->isExecute = true;
            }
        }
    }

}