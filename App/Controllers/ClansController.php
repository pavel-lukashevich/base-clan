<?php

namespace App\Controllers;

use App\Helpers\ClanRoles;
use App\Models\ClanMember;
use App\Services\Db;

class ClansController extends BaseController
{
    private $messages = [];

    /**
     * @param string $action
     * @return bool
     */
    public function checkPermissions(string $action)
    {
        if (in_array('clans.' . $action, $this->clan->getRoleHelper()->permissions)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function create()
    {
        $request = $_REQUEST;
        if ($this->validate($request)) {
            $db = Db::getPdo();
            try {
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $db->beginTransaction();

                $clanId = $this->clan->insert([
                    'title' => $request['title'],
                    'description' => $request['description'],
                    'user_id' => $this->user->id
                ]);
                if (empty($clanId)) {
                    return $this->viewJson(["messages" => ["Clan not created"]],422);
                }

                $role = (new ClanRoles())->getLeader();
                $clanMembersId = (new ClanMember())->insert([
                    'user_id' => $this->user->id,
                    'clan_id' => $clanId,
                    'role' => $role
                ]);

                if (empty($clanMembersId)) {
                    return $this->viewJson(["messages" => ["Clan member not created"]],422);
                }

                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                $this->messages[] = $e->getMessage();
            }
        } else {
            return $this->viewJson(["messages" => $this->messages],422);
        }
        return $this->viewJson(["messages" => ["Successfully"]],201);
    }

    /**
     * @return string
     */
    public function destroy()
    {
        try {
            $clan = $this->clan;
            $clan->getByConditions(['user_id' => $this->user->id]);
            if (empty($clan->getModel())) {
                return $this->viewJson(["messages" => ["Clan not found"]],422);
            }
            if (!$clan->destroy()) {
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
    public function index()
    {
        $data = $this->clan->getClanInfo();
        if (!empty($data)) {
            return $this->viewJson($data, 200);
        }
        return $this->viewJson(['messages' => 'Clan list is empty'], 204);
    }

    /**
     * @return string
     */
    public function show()
    {
        $data = (new ClanMember())->usersList($this->clan->id);
        if (empty($data)) {
            return $this->viewJson(['messages' => 'Clan list is empty'], 422);
        }
        return $this->viewJson($data, 200);
    }

    /**
     * @return string
     */
    public function update()
    {
        $request = $_REQUEST;
        $conditions = [];

        if ($this->checkPermissions('update-title')) {
            if (!empty($request['title'])) {
                $this->validateTitle($request['title']);
                $this->validateUniqueTitle($request['title'], $this->clan->id);
                $conditions['title'] = $request['title'];
            } else {
                $this->messages[] = 'Title required';
            }
        }

        if ($this->checkPermissions('update-description')) {
            if (!empty($request['description'])) {
                $this->validateDescription($request['description']);
                $conditions['description'] = $request['description'];
            } else {
                $this->messages[] = 'description required';
            }
        }

        if (!empty($this->messages)) {
            return $this->viewJson($this->messages, 422);
        }

        try {
            $clan = $this->clan;
            $status = $clan->update($conditions, ['id' => $this->clan->id]);
            if (empty($status)) {
                return $this->viewJson(["messages" => ["Clan member not updated"]],422);
            }
        } catch (\Exception $e) {
            return $this->viewJson(["messages" => [$e->getMessage()]],422);
        }

        return $this->viewJson(["messages" => ["Successfully"]],201);
    }

    /**
     * @param array $request
     * @return bool
     */
    private function validate(array &$request)
    {
        $member = (new ClanMember())->getByConditions(['user_id' => $this->user->id]);
        if (!empty($member)) {
            $this->messages[] = 'You are in a clan';
            return false;
        }
        if (!empty($request['title'])) {
            $this->validateTitle($request['title']);
            $this->validateUniqueTitle($request['title']);
        } else {
            $this->messages[] = 'Title required';
        }
        if (!empty($request['description'])) {
            $this->validateDescription($request['description']);
        } else {
            $this->messages[] = 'Description required';
        }
        if (empty($this->messages)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $title
     */
    private function validateTitle(string &$title)
    {
        $title = trim($title);
        if (mb_strlen($title) > 12) {
            $this->messages[] = 'Title - max length 12 letters';
        }
        if (!preg_match('~^\w+$~', $title)) {
            $this->messages[] = 'Title should only consist of letters and numbers';
        }
    }

    /**
     * @param string $title
     * @param int|null $clanId
     */
    private function validateUniqueTitle(string $title, int $clanId = null)
    {
        if (empty($this->messages)) {
            $clan = $this->clan->getByConditions(['title' => $title]);
            if (!empty($clan) && empty($clanId) && $clan->id != $clanId) {
                $this->messages[] = 'Title must be unique';
            }
        }
    }

    /**
     * @param string $description
     */
    private function validateDescription(string &$description)
    {
        $description = trim($description);
        if (empty($description)) {
            $this->messages[] = 'Description required';
        }
        if (mb_strlen($description) > 30) {
            $this->messages[] = 'Description - max length 30 letters';
        }
    }
}