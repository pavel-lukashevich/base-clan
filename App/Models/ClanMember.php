<?php

namespace App\Models;

class ClanMember extends BaseModel
{
    protected static $table = 'clan_members';

    /** @var int */
    public $id;

    /** @var int */
    public $user_id;

    /** @var int */
    public $clan_id;

    /** @var string */
    public $role;

    public function usersList(int $clanId)
    {
        if (empty($clanId)) {
            return null;
        }

        $query = 'SELECT ' . static::$table . '.*, users.username  FROM ' . static::$table .
            ' LEFT JOIN users ON ' . static::$table . '.user_id = users.id ' .
            'WHERE ' . static::$table . '.clan_id = :clanId';
        $stmt = $this->db->prepare($query);

        if ($stmt->execute(["clanId" => $clanId])) {
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }
        return null;
    }
}