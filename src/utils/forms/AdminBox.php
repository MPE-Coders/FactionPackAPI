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
use XackiGiFF\FactionPackAPI\factions\Manager;
use XackiGiFF\FactionPackAPI\utils\forms\admin\AdminPlayerBox;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;

class AdminBox {
    const FACTION_CHANGE = 0x01;
    const RANK_CHANGE = 0x02;
    private static AdminBox $instance;
    private array $players;
    private string $tmp;
    private $adminBoxPlayerBox;

    public function __construct() {
        $this->players = [];
        $this->adminBoxPlayerBox = new AdminPlayerBox();
        $players = FactionAPI::getPlayers();
        foreach ($players as $player){
            $this->players[] = $player->getName();
        }
        self::$instance = $this;
    }

    /**
     * Главная страница управления
     * @param $sender
     * @return bool
     */
    public static function sendMainForm($sender) : bool
    {
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            
            if ($data == 0)
                self::sendAdminFactionsPage($sender, $data);
            if ($data == 1)
                self::sendAdminCreateFactionPage($sender, $data);
            if ($data == 2)
                self::sendAdminPlayersPage($sender, $data);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Список доступных функций:");
        $form->addButton("Управление существующими фракциями");
        $form->addButton("Создать новую фракцию");
        $form->addButton("Управление игроками");
        $sender->sendForm($form);
        return true;
    }

    private static function sendAdminFactionsPage(Player $sender, $data): void
    {
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            
            self::sendAdminFactionPage($sender, $data);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Список доступных фракций:");
        $factionList = FactionAPI::getFactionList(Faction::TYPE_ARRAY);

        foreach ($factionList as $faction){
            $form->addButton($faction->getName(), 0, $faction->getImage());
        }
        $sender->sendForm($form);
    }

    private static function sendAdminFactionPage(Player $sender, $data): void
    {
        $data = "f".($data + 1);
        $faction = FactionAPI::getFaction($data);
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            if ($data == 0) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Управление фракцией:" . PHP_EOL .
                          "Имя фракции: ".$faction->getName() . PHP_EOL .
                          "Ид фракции: ".$faction->getId() . PHP_EOL .
                          "Ранг фракции по умолчанию: ".$faction->getDefaultRank()->getName() . PHP_EOL .
                          "Ранги фракции: ".$faction->getRankList(Faction::TYPE_STRING));
        $form->addButton("Закрыть");
        $sender->sendForm($form);
    }

    private static function sendAdminPlayersPage(Player $sender, $data): void
    {
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            
            self::sendAdminPlayerPage($sender, $data);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Список доступных игроков для модерации:");
        foreach (FactionAPI::getPlayers() as $player){
            $form->addButton($player->getName());
        }
        $sender->sendForm($form);
    }

    private static function sendAdminPlayerPage(Player $sender, $data): void
    {
        $players = self::getInstance()->getPlayers();
        $player = FactionAPI::getPlayer($players[$data]);
        AdminBox::getAdminPlayerBox()->setPlayer($player->getName());
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            if ($data == 0) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Меняем фракцию");
                AdminBox::getAdminPlayerBox()->sendChange($sender, self::FACTION_CHANGE);
                return;
            }
            if ($data == 1) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Меняем ранг");
                AdminBox::getAdminPlayerBox()->sendChange($sender, self::RANK_CHANGE);
                return;
            }
            
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Управление игроком:" . PHP_EOL .
                          "Имя игрока: ".$player->getName() . PHP_EOL .
                          "Фракция игрока: ".$player->getFaction()->getName() . PHP_EOL .
                          "Ранг игрока: ".$player->getRank()->getName() . PHP_EOL .
                          "Заработок на этом ранге: ".$player->getRank()->getPay() . PHP_EOL .
                          "Зарплата выдается каждые: ".$player->getRank()->getTime(). " минут.");
        $form->addButton("Изменить фракцию");
        $form->addButton("Изменить ранг");
        $sender->sendForm($form);
    }

    public function getPlayers() : array
    {
        return $this->players;
    }

    private static function getInstance(): self
    {
        return self::$instance;
    }

    public static function getAdminPlayerBox() : AdminPlayerBox
    {
        return self::getInstance()->adminBoxPlayerBox;
    }

    private static function sendAdminCreateFactionPage(Player $sender, $data)
    {
    }
}