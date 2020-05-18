<?php

namespace App\Controllers;

use App\Helpers\ClanRoles;
use App\Models\Clan;
use App\Models\User;

abstract class BaseController
{
    /** @var User|null $user */
    public $user = null;

    /** @var Clan|null $clan */
    public $clan = null;

    public function __construct()
    {
        if (!empty($_REQUEST['session_key'])) {
            $this->user = (new User())->getByConditions(['session_key' => $_REQUEST['session_key']]);
        }
        if (empty($this->user)) {
            return $this->viewJson(["messages" => ["User not found"]], 404);
        }

        $this->clan = (new Clan())->getByUserId($this->user->id);
        if (empty($this->clan)) {
            $this->clan = new Clan();
        }
        $roleHelper = new ClanRoles($this->clan->role);
        $this->clan->setRoleHelper($roleHelper);

        return $this;
    }

    /**
     * @param array $data
     * @param int $response_code
     * @return string
     */
    public function viewJson(array $data, int $response_code = 200)
    {
        header('Content-type:application/json');
        http_response_code($response_code);
        echo json_encode($data);
        die;
    }
}