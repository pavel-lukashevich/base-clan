<?php

namespace App\Helpers;

class RolesPermissions
{
    public const LEADER = 'leader';
    public const SOLDIER = 'soldier';
    public const GUEST = 'guest';
    public const ROLES = [
        self::LEADER => [
            "title" => "Leader",
            "weight" => 100,
            "permissions" => [
                "clans.destroy",
                "clans.index",
                "clans.show",
                "clans.update",
                "clans.update-title",
                "clans.update-description",
                "clan-members.roleUp",
                "clan-members.roleDown"
            ]
        ],
        "vice" => [
            "title" => 'Vice',
            "weight" => 60,
            "permissions" => [
                "clans.index",
                "clans.show",
                "clans.update",
                "clans.update-description",
                "clan-members.roleUp",
                "clan-members.roleDown",
                "clan-members.quit"
            ]
        ],
        self::SOLDIER => [
            "title" => 'Soldier',
            "weight" => 10,
            "permissions" => [
                "clans.index",
                "clans.show",
                "clan-members.quit"
            ]
        ],
        self::GUEST => [
            "title" => 'Guest',
            "weight" => 0,
            "permissions" => [
                "clans.create",
                "clans.index",
                "clan-members.join"
            ]
        ]
    ];
}