<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\factions\faction;

final class Rank
{
    private string $id;
    private string $name;
    private int $price;
    private int $pay;
    private int $time;
    private bool $default;
    private bool $can_write;
    private mixed $image;
    private SpecialSkils $skills;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($rank_id): void
    {
        $this->id = $rank_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getImage(): string
    {
        return $this->image ?? 'textures/items/diamond';
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }
    public function getPay(): int
    {
        return $this->pay;
    }

    public function setPay($pay): void
    {
        $this->pay = $pay;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime($time): void
    {
        $this->time = $time;
    }
    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault($default): void
    {
        $this->default = $default;
    }

    public function canWrite(): bool
    {
        return $this->can_write;
    }

    public function setCanWrite($can_write): void
    {
        $this->can_write = $can_write;
    }

    public function setSpecialSkills(mixed $special_skills): void
    {
        $skills = new SpecialSkils();
        foreach ($special_skills as $skill => $options) {
            switch ($skill) {
                case SpecialSkils::CAN_WRITE:
                    if($options){
                        $skills->setCanWrite(true);
                    } else {
                        $skills->setCanWrite(false);
                    }
                    break;
                case SpecialSkils::CAN_MANAGE:
                    $manage = new Manage();
                    if($options){
                        $manage->setTypes($options['manage_type']);
                        $manage->setFactions($options['factions']);
                        $skills->setCanManage(true);
                    } else {
                        $skills->setCanManage(false);
                    }
                    $skills->setManage($manage);
                    break;
                case SpecialSkils::CAN_HEALTH:
                    if($options) {
                        $skills->setCanHealth(true);
                    } else {
                        $skills->setCanHealth(false);
                    }
                    break;
                case SpecialSkils::CAN_ARREST:
                    if($options){
                        $skills->setCanArrest(true);
                    } else {
                        $skills->setCanArrest(false);
                    }
                    break;
                case SpecialSkils::CAN_AMMUNITION:
                    if($options){
                        $skills->setCanAmmun(true);
                    } else {
                        $skills->setCanAmmun(false);
                    }
                    break;
            }

        }
        $this->skills = $skills;
    }

    public function getSkills(): SpecialSkils
    {
        return $this->skills ?? new SpecialSkils();
    }
}