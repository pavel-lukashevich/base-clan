<?php

namespace App\Controllers;

use App\Helpers\ClanRoles;
use App\Models\ClanMember;

class ClanMembersController extends BaseController
{
    private $messages = [];

    /**
     * @param string $action
     * @return bool
     */
    public function checkPermissions(string $action)
    {
        if (in_array('clan-members.' . $action, $this->clan->getRoleHelper()->permissions)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function join()
    {
        $clanId = $_REQUEST["clan_id"];
        $this->clan->getByConditions(["id" => $clanId]);
        if (empty($clanId)) {
            return $this->viewJson(["messages" => ["Clan not found"]],422);
        }

        $role = (new ClanRoles())->getSoldier();
        $clanMembersId = (new ClanMember())->insert([
            'user_id' => $this->user->id,
            'clan_id' => $clanId,
            'role' => $role
        ]);

        if (empty($clanMembersId)) {
            return $this->viewJson(["messages" => ["Clan member not created"]],422);
        }

        return $this->viewJson(["messages" => ["Successfully"]],201);
    }

    /**
     * @return string
     */
    public function quit()
    {
        try {
            $clanMember = new ClanMember();
            $clanMember->getByConditions(["user_id" => $this->user->id]);
            if (empty($clanMember->getModel())) {
                return $this->viewJson(["messages" => ["Clan member not found"]],422);
            }
            if (!$clanMember->destroy()) {
                $this->messages[] = "Failed to delete";
            }
        } catch (\Exception $e) {
            $this->messages[] = $e->getMessage();
        }
        if (!empty($this->messages)) {
            return $this->viewJson(["messages" => $this->messages],422);
        }
        return $this->viewJson(["messages" => ["Successfully"]],201);
    }

    /**
     * @return string
     */
    public function roleUp()
    {
        $mutableUserId = $_REQUEST["user_id"];
        $mutableUser = new ClanMember();
        $mutableUser->getByConditions(["user_id" => $mutableUserId, "clan_id" => $this->clan->id]);

        if (empty($mutableUser->getModel())) {
            return $this->viewJson(["messages" => ["User not found"]],422);
        }

        $roleHelper = $this->clan->getRoleHelper();
        if ($roleHelper->hasPermissionsToChangeRole($mutableUser->getModel()->role)) {
            $nextRole = $roleHelper->getNextRole($mutableUser->getModel()->role);
            if ($nextRole != $roleHelper->getLeader()) {
                $status = $mutableUser->getModel()->update(["role" => $nextRole], ["user_id" => $mutableUserId]);
                if (!empty($status)) {
                    return $this->viewJson(["messages" => ["Successfully"]],201);
                }
            }
        }

        return $this->viewJson(["messages" => ["Failed to enhance the role"]],422);
    }

    /**
     * @return string
     */
    public function roleDown()
    {
        $mutableUserId = $_REQUEST["user_id"];
        $mutableUser = new ClanMember();
        $mutableUser->getByConditions(["user_id" => $mutableUserId, "clan_id" => $this->clan->id]);

        if (empty($mutableUser->getModel())) {
            return $this->viewJson(["messages" => ["User not found"]],422);
        }

        $roleHelper = $this->clan->getRoleHelper();
        if ($roleHelper->hasPermissionsToChangeRole($mutableUser->getModel()->role)) {
            $previousRole = $roleHelper->getPreviousRole($mutableUser->getModel()->role);
            if ($previousRole != $roleHelper->getGuest()) {
                $status = $mutableUser->getModel()->update(["role" => $previousRole], ["user_id" => $mutableUserId]);
                if (!empty($status)) {
                    return $this->viewJson(["messages" => ["Successfully"]],201);
                }
            } else {
                try {
                    if (!$mutableUser->destroy()) {
                        $this->messages[] = "Failed to delete";
                    }
                } catch (\Exception $e) {
                    $this->messages[] = $e->getMessage();
                }
                if (!empty($this->messages)) {
                    return $this->viewJson(["messages" => $this->messages],422);
                }
                return $this->viewJson(["messages" => ["User expelled from clan"]],201);
            }
        }

        return $this->viewJson(["messages" => ["Failed to enhance the role"]],422);
    }
}
