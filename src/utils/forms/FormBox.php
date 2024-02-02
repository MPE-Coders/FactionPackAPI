<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\utils\forms;

use ErrorException;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use XackiGiFF\FactionPackAPI\FactionPackAPI;
use XackiGiFF\FactionPackAPI\factions\api\FactionAPI;
use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;

class FormBox {

    public function __construct() {
    }
    public static function sendMainForm($sender) : bool
    {
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            //var_dump($data);
            self::sendJobPage($sender, $data);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Список доступных фракций:");
        foreach (FactionAPI::getFactionList(Faction::TYPE_ARRAY) as $faction){
                $form->addButton($faction->getName(), 0, $faction->getImage());
        }
        $sender->sendForm($form);
        return true;
    }

    /**
     * @throws \JsonException
     */
    public static function sendJobPage($sender, $data) : bool
    {
        $data = "f".($data + 1);
        //var_dump($data);
        try {
            $faction = FactionAPI::getFaction($data);
            $faction_id = $faction->getId();
            $rank_id = $faction->getDefaultRank()->getId();
            $member = FactionAPI::registerPlayer($sender->getName(), $faction_id, $rank_id);
            $form = new ModalForm(function (Player $sender, $data): void {
                if ($data === NULL) {
                    $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                    return;
                }
                $member = FactionAPI::getPlayer($sender->getName());
                if ($data === true) {
                    $sender->sendMessage(FactionPackAPI::PREFIX . "Вы вступили в фракцию: " . $member->getFaction()->getName());
                }
                if ($data === false) {
                    self::sendMainForm($sender);
                    $member->unregisterPlayer();
                    $sender->sendMessage(FactionPackAPI::PREFIX . "Вы решили не вступать");
                }
            });
            $form->setTitle(FactionPackAPI::PREFIX);
            $form->setContent("§eВы хотите вступить в фракцию на работу {$member->getFaction()->getName()}.\n" .
                              "§eВаша будущая роль {$member->getRank()->getName()}.\n" .
                              "§eВаша зарплата составит: " . $member->getRank()->getPrice());
            $form->setButton1("Подтвердить");
            $form->setButton2("Отказаться");
            $sender->sendForm($form);
        } catch (ErrorException $exception) {
            TerminalLogger::critical("Ошибка! ". $exception->getMessage());
        }
        return true;
    }

    public static function sendJobQuitPage($sender) : bool
    {
        $form = new ModalForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            if($data === true){
                $worker = FactionAPI::getPlayer($sender->getName());
                $worker->unregisterPlayer();
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы уволены с работы.");
            }
            if($data === false){
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы решили не увольняться.");
            }
        });
        $player = FactionAPI::getPlayer($sender->getName());
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("§eВаша работа: {$player->getRank()->getName()}.\n Ваш ранг:{$player->getFaction()->getName()}.");
        $form->setButton1("Уволиться");
        $form->setButton2("Остаться");
        $sender->sendForm($form);
        return true;
    }

    public static function getAdminBox() : AdminBox
    {
        return new AdminBox();
    }

}