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

use dollmetzer\zzaplib\exception\ValidationException;

/**
 * Class Permission
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Permission
{
    const CREATE = 1;
    const READ = 2;
    const UPDATE = 4;
    const DELETE = 8;
    const EXECUTE = 16;
    const MULTIPLY_GROUP = 32;
    const MULTIPLY_ALL = 1024;

    /**
     * @var int
     */
    protected $ownerId;

    /**
     * @var int
     */
    protected $groupId;

    /**
     * @var int
     */
    protected $permissions;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var array
     */
    protected $groupIds;


    /**
     * Permission constructor.
     *
     * @param int $ownerId
     * @param int $groupId
     * @param int $permissions
     * @throws ValidationException
     */
    public function __construct(int $ownerId=0, int $groupId=0, int $permissions=31)
    {
        $this->setObject($ownerId, $groupId, $permissions);
        $this->userId = 0;
        $this->groupIds = [];
    }

    /**
     * @param int $ownerId
     * @param int $groupId
     * @param int $permissions
     * @throws ValidationException
     */
    public function setObject(int $ownerId, int $groupId, int $permissions)
    {
        if($ownerId < 0) {
            throw new ValidationException('ownerId', 'value is less than 0');
        }
        $this->ownerId = $ownerId;

        if($groupId < 0) {
            throw new ValidationException('groupId', 'value is less than 0');
        }
        $this->groupId = $groupId;

        if(($permissions < 0) or ($permissions >32767))
        {
            throw new ValidationException('permissions', 'value is less than 0 or greater than 32767');
        }
        $this->permissions = $permissions; // muss < 2 ^ 15 32768
    }

    /**
     * @param int $userId
     * @param array $groupIds
     * @throws ValidationException
     */
    public function setUser(int $userId, array $groupIds)
    {
        if($userId < 0) {
            throw new ValidationException('userId', 'value is less than 0');
        }
        $this->userId = $userId;

        if(!is_array($groupIds)) {
            throw new ValidationException('groupIds', 'value is no array of int');
        }
        $this->groupIds = $groupIds;
    }

    /**
     * @return bool
     */
    public function isCreateAllowed()
    {
        return $this->testPermissionBit(self::CREATE);
    }

    /**
     * @return bool
     */
    public function isReadAllowed()
    {
        return $this->testPermissionBit(self::READ);
    }

    /**
     * @return bool
     */
    public function isUpdateAllowed()
    {
        return $this->testPermissionBit(self::UPDATE);
    }

    /**
     * @return bool
     */
    public function isDeleteAllowed()
    {
        return $this->testPermissionBit(self::DELETE);
    }

    /**
     * @return bool
     */
    public function isExecuteAllowed()
    {
        return $this->testPermissionBit(self::EXECUTE);
    }

    /**
     * @param $bitValue
     * @return bool
     */
    private function testPermissionBit($bitValue)
    {
        $result = false;
        if($this->userId == $this->ownerId) {
            if($this->permissions & $bitValue) {
                $result = true;
            }
        }
        if(in_array($this->groupId, $this->groupIds)) {
            if($this->permissions & ($bitValue * self::MULTIPLY_GROUP)) {
                $result = true;
            }
        }
        if($this->permissions & ($bitValue * self::MULTIPLY_ALL)) {
            $result = true;
        }
        return $result;
    }

}
