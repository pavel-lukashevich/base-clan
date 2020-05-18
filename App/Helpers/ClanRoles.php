<?php

namespace App\Helpers;

class ClanRoles
{
    private $userRole;
    private $roles;

    public $title;
    public $weight;
    public $permissions;


    /**
     * ClanRoles constructor.
     * @param string|null $userRole
     */
    public function __construct(string $userRole = null)
    {
        $this->roles = RolesPermissions::ROLES;
        $this->userRole = $this->checkRole($userRole);

        $this->title = $this->roles[$this->userRole]["title"];
        $this->weight = $this->roles[$this->userRole]["weight"];
        $this->permissions = $this->roles[$this->userRole]["permissions"];
    }

    /**
     * @return string
     */
    public function getLeader()
    {
        return RolesPermissions::LEADER;
    }

    /**
     * @return string
     */
    public function getSoldier()
    {
        return RolesPermissions::SOLDIER;
    }

    /**
     * @return string
     */
    public function getGuest()
    {
        return RolesPermissions::GUEST;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasPermissionsToChangeRole(string $role)
    {
        return $this->roles[$this->userRole]["weight"] > $this->roles[$role]["weight"] &&
            $this->roles[$role]["weight"] > $this->roles[$this->getGuest()]["weight"];
    }

    public function getNextRole(string $role)
    {
        $nextRole = $this->getLeader();
        foreach ($this->roles as $key => $value) {
            if ($value["weight"] > $this->roles[$role]["weight"] &&
                $this->roles[$nextRole]["weight"] > $value["weight"]
            ){
                $nextRole = $key;
            }
        }
        return $nextRole;
    }

    public function getPreviousRole(string $role)
    {
        $previoustRole = $this->getGuest();
        foreach ($this->roles as $key => $value) {
            if ($value["weight"] < $this->roles[$role]["weight"] &&
                $this->roles[$previoustRole]["weight"] < $value["weight"]
            ){
                $previoustRole = $key;
            }
        }
        return $previoustRole;
    }

    /**
     * @param string|null $userRole
     * @return string
     */
    private function checkRole(string $userRole = null)
    {
        if (!isset($userRole)) {
            $userRole = RolesPermissions::GUEST;
        } elseif (!array_key_exists($userRole, $this->roles)) {
            $userRole = RolesPermissions::SOLDIER;
        }

        return $userRole;
    }
}