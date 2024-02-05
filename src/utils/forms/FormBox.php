<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\utils\forms;

use ErrorException;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use XackiGiFF\FactionPackAPI\FactionPackAPI;
use XackiGiFF\FactionPackAPI\factions\api\FactionAPI;
use XackiGiFF\FactionPackAPI\factions\faction\Faction;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;
use pocketmine\Server;

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
            var_dump($data);
            self::sendJobPage($sender, $data);
        });
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Список доступных фракций:");

        $factionList = FactionAPI::getFactionList(Faction::TYPE_ARRAY);

        array_pop($factionList); // Удаляем последний элемент из массива. Последний элемент - это "Без фракции\Без ранга"
        foreach ($factionList as $faction) {
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
                $member = FactionAPI::getPlayer($sender->getName());

                if ($data === NULL) {
                    $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                    $member->unregisterPlayer();
                    return;
                }
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
            $skills = "";
            if($member->getRank()->getSkills()->getCanWrite()){
                $skills .= "- Публиковать новости" . PHP_EOL;
            }
            if($member->getRank()->getSkills()->getCanHealth()){
                $skills .= "- Набор аптечки для лечения" . PHP_EOL;
            }
            if($member->getRank()->getSkills()->getCanManage()){
                $faction_ids = $member->getRank()->getSkills()->getManage()->getFactionsIds();
                $skills .= "- Власть управления подчиненными:" . PHP_EOL;
                foreach ($faction_ids as $faction_id){
                    $faction_name = FactionAPI::getFaction($faction_id)->getName();
                    $ranks_names = $member->getRank()->getSkills()->getManage()->getFactionRanks($faction_id);
                    $skills .= $faction_name . ":" . PHP_EOL;
                    foreach ($ranks_names as $ranks_name) {
                        $skills .= '* ' . $ranks_name . PHP_EOL;
                    }
                }
            }
            if($member->getRank()->getSkills()->getCanArrest()){
                $skills .= "- Арестовывать игроков наручниками" . PHP_EOL;
            }

            if($skills !== ''){
                $skills = "§eВам будет доступно: " . PHP_EOL . $skills;
            }

            $form->setContent("§eВы хотите вступить в фракцию на работу {$member->getFaction()->getName()}" . PHP_EOL .
                              "§eВаша будущая роль {$member->getRank()->getName()}" . PHP_EOL .
                              "§eВаша зарплата составит: {$member->getRank()->getPrice()}" . PHP_EOL .
                              "{$skills}");
            $form->setButton1("Подтвердить");
            $form->setButton2("Отказаться");
            $sender->sendForm($form);
        } catch (ErrorException $exception) {
            TerminalLogger::critical("Ошибка! ". $exception->getMessage());
        }
        return true;
    }

    public static function sendJobMainPage($sender) : bool
    {
        $form = new SimpleForm(function (Player $sender, $data) : void {
            if ($data === NULL) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }
            $member = FactionAPI::getPlayer($sender->getName());
            if($data == 'can_write'){
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы можете писать новости.");
                // TODO: Открыть страницу отправки новостей
                self::sendNewsPage($sender);
            }
            if($data == 'can_health'){
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы получили аптечку.");
                // TODO: Выдать игроку аптечку
            }
            if($data == 'can_arrest'){
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы получили наручники.");
                // TODO: Выдать игроку наручники
            }
            if($data == 'can_manage'){
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы можете управлять.");
                // TODO: Открыть страницу управления фракцией\фракциями, которые доступны игроку для управления
                 self::sendManagePage($sender);
            }
            if($data === 'quit'){
                $member->unregisterPlayer();
                $sender->sendMessage(FactionPackAPI::PREFIX . "Вы решили уволиться.");
            }
        });
        $member = FactionAPI::getPlayer($sender->getName());
        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("§eВаша работа: {$member->getRank()->getName()}.\n Ваш ранг:{$member->getFaction()->getName()}.");

        if($member->getRank()->getSkills()->getCanWrite()){
            $form->addButton("Написать новость", -1, "", "can_write");
        }
        if($member->getRank()->getSkills()->getCanHealth()){
            $form->addButton("Получить набор аптечки", -1, "", "can_health");
        }
        if($member->getRank()->getSkills()->getCanArrest()){
            $form->addButton("Получить наручники для ареста", -1, "", "can_arrest");
        }
        if($member->getRank()->getSkills()->getCanManage()){
            $form->addButton("Управлять", -1, "", "can_manage");
        }

        $form->addButton("Уволиться", -1, "", "quit");
        $sender->sendForm($form);
        return true;
    }

    public static function sendNewsPage(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data): void {
            if ($data === null) {
                $player->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }

            $inputText = $data[1];

            Server::getInstance()->broadcastMessage("[News] " . $player->getName() . " > " . $inputText);
        });

        $form->setTitle(FactionPackAPI::PREFIX);
        $form->addLabel("Введите новость, пусть она всех шокирует!");
        $form->addInput("Введите текст:");
        $form->sendToPlayer($player);
    }

    public static function sendManagePage(Player $sender): void
    {
        $member = FactionAPI::getPlayer($sender->getName());

        $form = new SimpleForm(function (Player $sender, $data): void {
            if ($data === null) {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                return;
            }

            $selectedOption = $data;

            switch ($selectedOption) {
                case "rankprom":
                    $sender->sendMessage("Повысить/понизить");
                    self::sendRankPromotionPage($sender);
                    break;

                case "uninv":
                    $sender->sendMessage("Увольнение");
                    break;

                case "interdepartmental_management":
                    $sender->sendMessage("Межвед.упр");
                    break;

                default:
                    $sender->sendMessage(FactionPackAPI::PREFIX . "Неизвестная опция.");
                    break;
            }
        });

        $form->setTitle(FactionPackAPI::PREFIX);
        $form->setContent("Управление фракцией: " . $member->getFaction()->getName());

        $form->addButton("Повысить/понизить", -1, "", "rankprom");
        $form->addButton("Уволить", -1, "", "uninv");

        if ($member->getFaction()->getId() === "f7" && $member->getRank()->getId() >= "r4") {
            $form->addButton("Межведомственное управление", -1, "", "interdepartmental_management");
        }

        $sender->sendForm($form);
    }

    public static function sendRankPromotionPage(Player $sender): void
    {
        $member = FactionAPI::getPlayer($sender->getName());

        // Проверяем, чтобы у игрока была фракция и ранг
        if ($member !== false && $member->getFaction() !== null) {
            $factionId = $member->getFaction()->getId();
            $factionMembers = FactionAPI::getFactionMembers($factionId);

            $form = new SimpleForm(function (Player $sender, $data) use ($factionMembers): void {
                if ($data === null) {
                    $sender->sendMessage(FactionPackAPI::PREFIX . "Форма закрыта.");
                    return;
                }

                // Обработка выбора игрока
                $selectedPlayerIndex = (int)$data;
                $selectedPlayer = $factionMembers[$selectedPlayerIndex];

                // Здесь вы можете реализовать логику повышения/понижения ранга выбранного игрока
                // Например, вызов метода FactionAPI::promotePlayer($selectedPlayer) или что-то подобное

                $sender->sendMessage(FactionPackAPI::PREFIX . "Игрок {$selectedPlayer->getName()} был выбран для повышения/понижения.");
            });

            $form->setTitle(FactionPackAPI::PREFIX);
            $form->setContent("Выберите игрока для повышения/понижения:");

            foreach ($factionMembers as $index => $factionMember) {
                $form->addButton($factionMember->getName());
            }

            $sender->sendForm($form);
        } else {
            $sender->sendMessage(FactionPackAPI::PREFIX . "Вы не состоите в фракции или у вас нет ранга.");
        }
    }


    public static function getAdminBox() : AdminBox
    {
        return new AdminBox();
    }

}