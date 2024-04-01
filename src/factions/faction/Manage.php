<?php

namespace XackiGiFF\FactionPackAPI\factions\faction;

use XackiGiFF\FactionPackAPI\factions\api\FactionAPI;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;

class Manage
{
    private array $types;
    private array $factions;

    public function __construct()
    {
    }

    public function setTypes(array $manage_type): void
    {
        $this->types = $manage_type;
    }
    public function getTypes() : array {
        return $this->types;
    }

    public function getCanKick(): bool {
        return in_array('kick', $this->types);
    }

    public function getCanDown(): bool {
        return in_array('down', $this->types);
    }

    public function getCanUp(): bool {
        return in_array('up', $this->types);
    }


    public function setFactions(mixed $factions): void
    {
        foreach ($factions as $faction_id => $ranks_id) {
            $faction = FactionAPI::getFaction($faction_id);

            $rank_ids = [];

            foreach ($ranks_id as $rank_id){
                $rank_ids[] = $faction->getRank($rank_id)->getId();
            }

            $factions[$faction_id]['faction_id'] = $faction_id;
            $factions[$faction_id]['ranks'] = $rank_ids;
            // TODO: Set factions
        }
        $this->factions = $factions;
    }

    /**
     * Вернет M2M связь 'faction' к 'rank', доступные для управления текущему рангу во фракции
     * Пример связи: f1 => ['r1', 'r2', 'r3', 'r4', 'r5']
     * Содержимое:
     * $factions[$faction_id] = [
     * 'faction_id' => (string) 'f3',
     * 'ranks' => [ 'r1', 'r2', 'r3'],
     * ];
     * @return array
     */
    public function getFactions() : array
    {
        return $this->factions;
    }

    /**
     * Вернет только массив из имен фракций, которыми можно управлять
     * @return array
     */

    public function getFactionsNames() : array {
        $factions = [];
        foreach ($this->factions as $faction_manage){
            $factions[] = FactionAPI::getFaction($faction_manage['faction_id'])->getName();
        }
        return $factions;
    }

    /**
     * Вернет только массив из имен рангов, которыми можно управлять в запрашиваемой фракции
     * @param $faction_id
     * @return array
     */

    public function getFactionRanks($faction_id) : array {
        $ranks = [];
        if(isset($this->factions[$faction_id])){
            foreach ($this->factions[$faction_id]['ranks'] as $rank_id){
                $faction = FactionAPI::getFaction($faction_id);
                $ranks[] = FactionAPI::getRank($faction, $rank_id)->getName();
            }
            return $ranks;
        } else {
            TerminalLogger::critical("Эта фракция {$faction_id} недоступна для управления из вашего текущего ранга фракции!");
            return [];
        }
    }

    public function getFactionsIds() : array {
        $factions = [];
        foreach ($this->factions as $faction_manage){
            $factions[] = FactionAPI::getFaction($faction_manage['faction_id'])->getId();
        }
        return $factions;
    }
}