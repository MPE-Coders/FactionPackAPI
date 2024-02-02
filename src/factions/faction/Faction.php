<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\factions\faction;

use XackiGiFF\FactionPackAPI\factions\Manager;

final class Faction
{
    const TYPE_STRING = 0x01;
    const TYPE_ARRAY = 0x02;
    private string $id;
    private string $name;
    private mixed $image;
    private string $type;
    private array $ranks;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($faction_id) : void
    {
        $this->id = $faction_id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName($name) : void
    {
        $this->name = $name;
    }


    public function getImage() : string
    {
        return $this->image ?? 'textures/items/diamond';
    }

    public function setImage($image) : void
    {
        $this->image = $image;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setType($type) : void
    {
        $this->type = $type;
    }

    public function initRanks($options) : array
    {
        $this->ranks = array();
        if(!is_array($options)){
            $options =  Manager::nullRank();
        }
        foreach ($options['ranks'] as $rank_id => $option){
            $this->ranks[$rank_id] = new Rank();
            $this->ranks[$rank_id]->setId($rank_id);
            $this->ranks[$rank_id]->setName($option['name']);
            $this->ranks[$rank_id]->setPrice($option['price']);
            $this->ranks[$rank_id]->setPay($option['pay']);
            $this->ranks[$rank_id]->setTime($option['time']);

            if(isset($option['can_write'])){
                $this->ranks[$rank_id]->setCanWrite($option['can_write']);
            } else {
                $this->ranks[$rank_id]->setCanWrite(false);
            }

            if(isset($option['default'])){
                $this->ranks[$rank_id]->setDefault($option['default']);
            } else {
                $this->ranks[$rank_id]->setDefault(false);
            }
        }
        return $this->ranks;
    }

    public function getRank($rank_id): Rank
    {
        return $this->ranks[$rank_id];
    }

    public function getDefaultRank(): Rank
    {
//        sort($this->ranks, SORT_NATURAL | SORT_FLAG_CASE);
        $first = array_key_first($this->ranks);
        //var_dump($this->ranks[$first]);
        return $this->ranks[$first];
    }
    public function checkRank($rank_id): bool
    {
        return isset($this->ranks[$rank_id]);
    }
    public function getRankList($type) : array|string
    {
        switch ($type) {
            default:
            case self::TYPE_STRING:
                $rank_list = "";
                foreach ($this->ranks as $rank){
                    $rank_list .= " - ".$rank->getName() . PHP_EOL;
                }
                break;
            case self::TYPE_ARRAY:
                $rank_list = [];
                foreach ($this->ranks as $rank){
                    $rank_list[] = $rank;
                }
                break;
        }
        return $rank_list;
    }

}