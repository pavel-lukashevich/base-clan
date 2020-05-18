<?php

function getRoutesList()
{
    return [
        'post' => [
            'clans/create' => 'App\Controllers\ClansController@create', // session_key, title, description
            'clans/destroy' => 'App\Controllers\ClansController@destroy', // session_key
            'clans/index' => 'App\Controllers\ClansController@index', // session_key
            'clans/show' => 'App\Controllers\ClansController@show', // session_key
            'clans/update' => 'App\Controllers\ClansController@update', // session_key, ?title, description

            'clan-members/join' => 'App\Controllers\ClanMembersController@join', // session_key, clan_id
            'clan-members/quit' => 'App\Controllers\ClanMembersController@quit', // session_key
            'clan-members/roleUp' => 'App\Controllers\ClanMembersController@roleUp', // session_key, user_id
            'clan-members/roleDown' => 'App\Controllers\ClanMembersController@roleDown', // session_key, user_id
        ],
        'get' => [
            'seeds/run' => 'App\Services\Seeder@run',
        ]
    ];
}
