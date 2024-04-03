<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\utils\forms\admin;

use ErrorException;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use XackiGiFF\FactionPackAPI\FactionPackAPI;
use XackiGiFF\FactionPackAPI\factions\api\FactionAPI;
use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\FactionPackAPI\utils\forms\AdminBox;

class AdminPlayerBox {
    private static AdminPlayerBox $instance;
    private string $player;

    public function __construct()
    {
        self::$instance = $this;
    }

    public function sendRankChange($sender): void
    {
        //var_dump(self::getPlayerName());
        $player = FactionAPI::getPlayer(self::getPlayerName());
        $faction = $player->getFaction();
        $form = new SimpleForm(function (Player $sender, $data): void
        {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            $player = FactionAPI::getPlayer(AdminPlayerBox::getPlayerName());
            $rank_id = "r".($data + 1);
            $player->setRank($rank_id);
            $player->savePlayer();

            AdminBox::sendMainForm($sender);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Выберите ранк для смены:");
        foreach (FactionAPI::getRankList($faction->getId(), Faction::TYPE_ARRAY) as $rank){
            $form->addButton($rank->getName());
        }
        $sender->sendForm($form);
    }
    private function sendFactionChange($sender): void
    {
        $form = new SimpleForm(function (Player $sender, $data): void
        {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            $player = FactionAPI::getPlayer(AdminPlayerBox::getPlayerName());
            $faction_id = "f".($data + 1);
            $rank_id = $player->getFaction()->getDefaultRank()->getId();
            FactionAPI::registerPlayer(AdminPlayerBox::getPlayerName(), $faction_id, $rank_id);
            $player->savePlayer();

            AdminBox::sendMainForm($sender);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Выберите ранк для смены:");
        foreach (FactionAPI::getFactionList(Faction::TYPE_ARRAY) as $faction){
            $form->addButton($faction->getName());
        }
        $sender->sendForm($form);
    }

    public static function sendChange($sender, int $type): void
    {
        switch ($type){
            case AdminBox::FACTION_CHANGE:
                self::getInstance()->sendFactionChange($sender);
                break;
            case AdminBox::RANK_CHANGE:
                self::getInstance()->sendRankChange($sender);
                break;
        }
    }

    private static function getInstance(): self
    {
        return self::$instance;
    }

    public function setPlayer(string $name): void
    {
        $this->player = $name;
    }
    public static function getPlayerName(): string
    {
        return self::getInstance()->player;
    }
}