<?php

/**
 * All regions with the API routing for League Of Legends
 * @link https://github.com/TheDrone7/shieldbow
 */
abstract class Regions
{
    /**
     * Base URL for the API routing of Riot Games
     * @var array baseUniformResourceLocators
     */
    public const baseUniformResourceLocators = array(
        "br" => "https://br1.api.riotgames.com",
        "eune" => "https://eun1.api.riotgames.com",
        "euw" => "https://euw1.api.riotgames.com",
        "jp" => "https://jp1.api.riotgames.com",
        "kr" => "https://kr.api.riotgames.com",
        "lan" => "https://la1.api.riotgames.com",
        "las" => "https://la2.api.riotgames.com",
        "na" => "https://na1.api.riotgames.com",
        "oce" => "https://oc1.api.riotgames.com",
        "tr" => "https://tr1.api.riotgames.com",
        "ru" => "https://ru.api.riotgames.com",
        "ph" => "https://ph2.api.riotgames.com",
        "sg" => "https://sg2.api.riotgames.com",
        "th" => "https://th2.api.riotgames.com",
        "tw" => "https://tw2.api.riotgames.com",
        "vn" => "https://vn2.api.riotgames.com",
        "pbe" => "https://na1.api.riotgames.com"
    );
    /**
     * Regional URL or the API routing of Riot Games
     * @var array regionalUniformResourceLocators
     */
    public const regionalUniformResourceLocators = array(
        "america" => 'https://americas.api.riotgames.com',
        "south_east_asia" => 'https://sea.api.riotgames.com',
        "asia" => 'https://asia.api.riotgames.com',
        "europe" => 'https://europe.api.riotgames.com'
    );
}
