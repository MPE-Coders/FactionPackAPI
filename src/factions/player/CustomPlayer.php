<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\factions\player;

use JsonException;
use pocketmine\utils\Config;
use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\FactionPackAPI\factions\faction\Rank;
use XackiGiFF\FactionPackAPI\factions\Manager;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;

class CustomPlayer
{
    public string $name;
    public Faction|bool $faction;
    public Rank $rank;

    public int $limit;

    public Config $config;

    public function __construct($playerName){
        $this->name = $playerName;

        $this->config = new Config(Manager::getDataFolderPath() . 'players/' . $playerName . '_data.yml',Config::YAML);
    }

    public function getFaction() : Faction
    {
        if(isset($this->faction)){
            if($this->faction instanceof Faction) {
                return $this->faction;
            }
        } else {
            $faction_id = self::getConfig()->getNested("faction");
            if(isset($faction_id)){
                if(Manager::isCorrectFaction($faction_id)){
                    $this->faction = Manager::getFaction($faction_id);
                } else {
                    TerminalLogger::critical("Конфиг ссылается на несуществующую фракцию!");
                    TerminalLogger::warning("Фракции ".$faction_id." не существует!");
                    TerminalLogger::warning("Доступные фракции:");
                    TerminalLogger::notice(PHP_EOL.Manager::getFactionList(Faction::TYPE_STRING));
                    $this->faction = Manager::getNullFaction();
                }
            } else {
                TerminalLogger::critical("Конфиг не содержит данных о фракции!");
                TerminalLogger::warning("Доступные фракции:");
                TerminalLogger::notice(PHP_EOL.Manager::getFactionList(Faction::TYPE_STRING));
                $this->faction = Manager::getNullFaction();
            }
        }
        return $this->faction;
    }

    public function setFaction($name): void
    {
        if(isset(Manager::$factions[$name])){
                $this->faction = Manager::getFaction($name);
        }  else {
            TerminalLogger::warning("Фракции ".$name." не существует!");
            TerminalLogger::warning("Доступные фракции:");
            TerminalLogger::notice(PHP_EOL.Manager::getFactionList(Faction::TYPE_STRING));
            $this->faction = Manager::getNullFaction();
        }
    }

    public function getRank() : Rank
    {
        $faction = self::getFaction();
        $faction_name = $faction->getName();

        if(isset($this->rank)){
            return $this->rank;
        } else {
            TerminalLogger::critical("Ранг не был задан!");
            $rank_id = self::getConfig()->getNested("rank");
            if(isset($rank_id)){
                if(Manager::isCorrectRank($faction, $rank_id)){
                    $this->rank = $faction->getRank($rank_id);
                } else {
                    TerminalLogger::critical("Конфиг ссылкается на несуществующий ранг!");
                    TerminalLogger::warning("Ранга ".$rank_id." не существует!");
                    TerminalLogger::warning("Доступные ранги у фракции \"".$faction_name."\":");
                    TerminalLogger::notice(PHP_EOL.$faction->getRankList(Faction::TYPE_STRING));
                    $this->rank = Manager::getNullFaction()->getRank(Manager::NULL_RANK);
                }
            } else {
                TerminalLogger::critical("Конфиг не содержит данных о ранге!");
                TerminalLogger::warning("Доступные ранги у фракции \"".$faction_name."\":");
                TerminalLogger::notice($faction->getRankList(Faction::TYPE_STRING));
                $this->rank = Manager::getNullFaction()->getRank(Manager::NULL_RANK);
            }
        }
        return $this->rank;
    }

    public function setRank($rank_id) : void
    {   if(isset($rank_id)) {
            if(Manager::isCorrectRank(self::getFaction(), $rank_id)){
                $this->rank = self::getFaction()->getRank($rank_id);
            } else {
                TerminalLogger::warning("Ранга ".$rank_id." не существует!");
                TerminalLogger::warning("Доступные ранги у фракции ".self::getFaction()->getName().":");
                TerminalLogger::notice(PHP_EOL.self::getFaction()->getRankList(Faction::TYPE_STRING));
                $this->rank = Manager::getFaction(Manager::NULL_FACTION)->getRank(Manager::NULL_RANK);
            }
        }
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
    public function getLimit() : int
    {
        return self::getConfig()->getNested("limit");
    }

    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @throws JsonException
     */
    public function savePlayer() : void
    {

        $serializedContents = [ "faction" => self::getFaction()->getId(),
                                "rank"     => self::getRank()->getId()];
        self::getConfig()->setAll($serializedContents);
        self::getConfig()->save();
        Manager::addToManager(Manager::MODEL_USERS, self::getName(), $this);
    }

    public function unregisterPlayer() : void
    {
        Manager::delFromManager(Manager::MODEL_USERS, self::getName());
        unlink(self::getConfig()->getPath());
        unset($this->name);
        unset($this->faction);
        unset($this->rank);
        unset($this->limit);
        unset($this->config);
    }
}