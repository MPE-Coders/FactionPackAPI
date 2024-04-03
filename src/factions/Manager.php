<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\factions;

use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\FactionPackAPI\factions\faction\Rank;
use XackiGiFF\FactionPackAPI\factions\player\CustomPlayer;

class Manager
{
    const MODEL_FACTIONS = "factions";
    const MODEL_USERS = "users";
    const NO_FACTION = "Без фракции";
    const NO_RANK = "Без ранга";
    const NULL_FACTION = "null";
    const NULL_RANK = "null";
    const DEFAULT_RANK = "r1";

    public static array $factions;
    public static array $users;
    public static string $dataFolderPath;

    public function __construct(){

    }

    public static function addToManager($model, $slot, $object): void
    {
        self::$$model[$slot] =  $object;
    }

    public static function delFromManager($model, $slot): void
    {
        unset(self::$$model[$slot]);
    }

    public static function addDataFolderPath($path): void
    {
        self::$dataFolderPath = $path;
    }

    public static function getDataFolderPath(): string
    {
        return self::$dataFolderPath;
    }

    public static function getFaction($faction_id): Faction
    {
        return self::$factions[$faction_id] ?? Manager::getNullFaction();
    }

    public static function getPlayer($name): CustomPlayer {
        if(!isset(self::$users[$name])) {
            self::$users[$name] = new CustomPlayer($name);
        }
        return self::$users[$name];
    }
    public static function getRank($faction, mixed $rank_id): Rank
    {
        return $faction->getRank($rank_id) ?? $faction->getRank(self::DEFAULT_RANK);
    }

    public static function nullFaction(): Faction
    {
        $faction = new Faction();
        $faction->setId(self::NULL_FACTION);
        $faction->setName(self::NO_FACTION);
        $faction->setType("NO_FACTION");
        $options = self::nullRank();
        $faction->initRanks($options);
        Manager::addToManager(Manager::MODEL_FACTIONS, $faction->getId(), $faction);
        return $faction;
    }

    public static function getNullFaction(): Faction
    {
        return self::$factions[self::NULL_FACTION];
    }

    public static function nullRank(): array
    {
        return ["ranks" =>
                    [ Manager::NULL_RANK =>
                        ["name" => self::NO_RANK,
                        "id" => self::NULL_RANK,
                        "price" => 0,
                        "pay" => 0,
                        "time" => 0,
                        "can_write" => false,
                        "default" => true,
                        ]
                    ]
        ];
    }

    public static function isCorrectRank($faction, mixed $rank_id): bool
    {
        return $faction->checkRank($rank_id);
    }

    public static function isCorrectFaction(mixed $faction_id): bool
    {
        return isset(self::$factions[$faction_id]);
    }

    public static function getFactionList($type): array|string
    {
        switch ($type) {
            default:
            case Faction::TYPE_STRING:
                $faction_list = "";
                foreach (self::$factions as $faction){
                    $faction_list .= " - ".$faction->getName() . PHP_EOL;
                }
                break;
            case Faction::TYPE_ARRAY:
                $faction_list = [];
                foreach (self::$factions as $faction){
                    $faction_list[] = $faction;
                }
                break;
        }
        return $faction_list;
    }

}