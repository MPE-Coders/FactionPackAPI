<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\factions\Loader;

use pocketmine\utils\Config;
use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\FactionPackAPI\factions\Manager;

class LoaderFaction
{
    private static Config $config;
    public function __construct(protected $main)
    {
        self::$config = new Config(Manager::getDataFolderPath() . "factions.yml", Config::YAML);

        self::initFactions();
    }

    private static function initFactions(): void
    {
        $factions = self::$config->getAll();
        foreach ($factions as $faction => $options){
            $name = self::$config->getNested("$faction.name");
            $faction = self::registerFaction($faction, $name);
            $faction->initRanks($options);
        }
        self::registerNullFaction();
    }

    private static function registerFaction(int|string $faction_id, $name): Faction
    {
        $faction = new Faction();
        $faction->setId($faction_id);
        $faction->setName($name);

        Manager::addToManager(Manager::MODEL_FACTIONS, $faction_id, $faction);

        return $faction;
    }

    public static function registerNullFaction() : Faction
    {

        return Manager::nullFaction();
    }
}