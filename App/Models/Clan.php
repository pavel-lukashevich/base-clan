<?php

namespace App\Models;

use App\Helpers\ClanRoles;

class Clan extends BaseModel
{
    protected static $table = 'clans';

    /** @var ClanRoles $roleHelper */
    protected $roleHelper = null;

    /** @var int */
    public $id;

    /** @var string */
    public $title;

    /** @var string */
    public $description;

    /** @var int */
    public $user_id;

    /**
     * @return ClanRoles
     */
    public function getRoleHelper()
    {
        return $this->roleHelper;
    }

    /**
     * @param ClanRoles $roleHelper
     */
    public function setRoleHelper(ClanRoles $roleHelper)
    {
        $this->roleHelper = $roleHelper;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        $stmt = $this->db->prepare('SELECT ' . static::$table .'.*, ' .
            ClanMember::getTableName() . '.role as role ' .
            'FROM ' . static::$table .
            ' LEFT JOIN ' . ClanMember::getTableName() .
            ' ON ' . static::$table . '.id = ' . ClanMember::getTableName() . '.clan_id ' .
            'WHERE ' . ClanMember::getTableName() . '.user_id = :userId');
        $stmt->execute(["userId" => $userId]);
        $result = $stmt->fetchObject(static::class);

        return $result;
    }

    public function getClanInfo()
    {
        $query = 'SELECT ' . static::$table . '.id, ' .
            static::$table . '.title, ' .
            static::$table . '.description, ' .
            'users.id as leader_id, ' .
            'users.username as leader, ' .
            'COUNT(clan_members.id) as count ' .
            'FROM ' . static::$table . ' ' .
            'LEFT JOIN users ON ' . static::$table . '.user_id = users.id ' .
            'LEFT JOIN clan_members ON ' . static::$table . '.id = clan_members.clan_id ' .
            'GROUP BY ' . static::$table . '.id';
        $stmt = $this->db->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return null;
    }
}