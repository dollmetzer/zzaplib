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
 * For a Unix like Permission system
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Permission
{
    const READ = 1;
    const WRITE = 2;
    const EXECUTE = 4;
    const MULTIPLY_ALL = 1;
    const MULTIPLY_GROUP = 8;
    const MULTIPLY_OWNER = 64;

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
     * Permission constructor.
     *
     * @param int $ownerId
     * @param int $groupId
     * @param int $permissions
     * @throws ValidationException
     */
    public function __construct(int $ownerId = 0, int $groupId = 0, int $permissions = 0)
    {
        $this->setObject($ownerId, $groupId, $permissions);
    }

    /**
     * @param int $ownerId
     * @param int $groupId
     * @param int $permissions
     * @throws ValidationException
     */
    public function setObject(int $ownerId, int $groupId, int $permissions)
    {
        $this->setOwnerId($ownerId);
        $this->setGroupId($groupId);
        $this->setPermissions($permissions);
    }

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * @param int $ownerId
     * @throws ValidationException
     */
    public function setOwnerId(int $ownerId)
    {
        if ($ownerId < 0) {
            throw new ValidationException('ownerId', 'value is less than 0');
        }
        $this->ownerId = $ownerId;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     * @throws ValidationException
     */
    public function setGroupId(int $groupId)
    {
        if ($groupId < 0) {
            throw new ValidationException('groupId', 'value is less than 0');
        }
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getPermissions(): int
    {
        return $this->permissions;
    }

    /**
     * @param int $permissions
     * @throws ValidationException
     */
    public function setPermissions(int $permissions)
    {
        if (($permissions < 0) or ($permissions > 511)) {
            throw new ValidationException('permissions', 'value is less than 0 or greater than 511');
        }
        $this->permissions = $permissions;
    }

    /**
     * @param int $ownerId
     * @param array $groupIds
     * @return bool
     */
    public function canRead(int $ownerId = 0, array $groupIds = [])
    {
        return $this->testPermissionBit(self::READ, $ownerId, $groupIds);
    }

    /**
     * @param int $ownerId
     * @param array $groupIds
     * @return bool
     */
    public function canWrite(int $ownerId = 0, array $groupIds = [])
    {
        return $this->testPermissionBit(self::WRITE, $ownerId, $groupIds);
    }

    /**
     * @param int $ownerId
     * @param array $groupIds
     * @return bool
     */
    public function canExecute(int $ownerId = 0, array $groupIds = [])
    {
        return $this->testPermissionBit(self::EXECUTE, $ownerId, $groupIds);
    }

    /**
     * @param int $bitValue
     * @param int $ownerId
     * @param array $groupIds
     * @return bool
     */
    private function testPermissionBit(int $bitValue, int $ownerId, array $groupIds): bool
    {
        $result = false;
        if ($ownerId == $this->ownerId) {
            if ($this->permissions & ($bitValue * self::MULTIPLY_OWNER)) {
                $result = true;
            }
        }
        if (in_array($this->groupId, $groupIds)) {
            if ($this->permissions & ($bitValue * self::MULTIPLY_GROUP)) {
                $result = true;
            }
        }
        if ($this->permissions & ($bitValue * self::MULTIPLY_ALL)) {
            $result = true;
        }
        return $result;
    }

}
