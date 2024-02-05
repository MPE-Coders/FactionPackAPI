<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\factions\api;

use JsonException;
use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\FactionPackAPI\factions\faction\Rank;
use XackiGiFF\FactionPackAPI\factions\Manager;
use XackiGiFF\FactionPackAPI\factions\player\CustomPlayer;

class FactionAPI
{
    public function __construct()
    {
    }

    /**
     * @throws JsonException
     */
    public static function registerPlayer($name, $faction_id, $rank_id) : CustomPlayer
    {
        $player = new CustomPlayer($name);
        $player->setFaction($faction_id);
        $player->setRank($rank_id);
        $player->savePlayer();
        return $player;
    }

    public static function getPlayer($name) : CustomPlayer|false
    {
        return Manager::getPlayer($name) ?? false;
    }

    public static function getPlayers() : array
    {
        return Manager::$users;
    }

    public static function getFaction($faction_id) : Faction
    {
        return Manager::getFaction($faction_id);
    }

    public static function getRank($faction_id,$rank_id) : Rank
    {
        return Manager::getRank($faction_id, $rank_id);
    }

    public static function getFactionList($type) : array|string
    {
        return Manager::getFactionList($type);
    }

    public static function getRankList($faction_id, $type) : array|string
    {
        return Manager::getFaction($faction_id)->getRankList($type);
    }

    public static function setFaction($player, $faction_id) : void {
        self::getPlayer($player)->setFaction($faction_id);
    }

    public static function setRank($name, $rank_id) : void {
        self::getPlayer($name)->setRank($rank_id);
    }

    /**
     * @throws JsonException
     */
    public static function savePlayer($name) : void {
        self::getPlayer($name)->savePlayer();
    }

    /**
     * Получить список участников фракции по ид фракции.
     *
     * @param string $factionId Идентификатор фракции.
     * @return CustomPlayer[]|array Список участников фракции или пустой массив, если фракция не найдена.
     */
    public static function getFactionMembers($factionId): array
    {
        $faction = Manager::getFaction($factionId);

        if ($faction !== null) {
            $members = [];
            foreach (self::getPlayers() as $player) {
                if ($player->getFaction()->getId() === $factionId) {
                    $members[] = $player;
                }
            }
            return $members;
        }

        return [];
    }


}