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

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($rank_id) : void
    {
        $this->id = $rank_id;
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

    public function getPrice() : int
    {
        return $this->price;
    }

    public function setPrice($price) : void
    {
        $this->price = $price;
    }
    public function getPay() : int
    {
        return $this->pay;
    }

    public function setPay($pay) : void
    {
        $this->pay = $pay;
    }

    public function getTime() : int
    {
        return $this->time;
    }

    public function setTime($time) : void
    {
        $this->time = $time;
    }
    public function isDefault() : bool
    {
        return $this->default;
    }

    public function setDefault($default) : void
    {
        $this->default = $default;
    }

    public function canWrite() : bool
    {
        return $this->can_write;
    }

    public function setCanWrite($can_write) : void
    {
        $this->can_write = $can_write;
    }
}