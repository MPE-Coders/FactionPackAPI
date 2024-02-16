<?php

namespace XackiGiFF\FactionPackAPI\factions\faction;

class SpecialSkils
{

    private bool $can_write;
    private bool $can_manage;
    private Manage $manage;
    private bool $can_health;
    private bool $can_arrest;

    public function __construct()
    {
        $this->can_write = false;
        $this->can_manage = false;
        $this->can_health = false;
        $this->can_arrest = false;
    }

    public function setCanWrite(bool $status): void
    {
        $this->can_write = $status;
    }

    public function setCanManage(bool $status): void
    {
        $this->can_manage = $status;
    }

    public function setManage(Manage $manage): void
    {
        $this->manage = $manage;
    }

    public function setCanHealth(bool $status): void
    {
        $this->can_health = $status;
    }

    public function setCanArrest(bool $status): void
    {
        $this->can_arrest = $status;
    }

    public function getCanWrite() : bool
    {
        return $this->can_write;
    }

    public function getCanManage() : bool
    {
        return $this->can_manage;
    }

    public function getManage(): Manage
    {
        return $this->manage;
    }

    public function getCanHealth(): bool
    {
        return $this->can_health;
    }

    public function getCanArrest(): bool
    {
        return $this->can_arrest;
    }

}