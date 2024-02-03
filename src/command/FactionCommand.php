<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI\command;

use XackiGiFF\FactionPackAPI\FactionPackAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use XackiGiFF\FactionPackAPI\factions\api\FactionAPI;
use XackiGiFF\FactionPackAPI\utils\forms\FormBox;

class FactionCommand extends Command
{

    function __construct($cmd, $description)
    {
        parent::__construct($cmd, $description);

        $this->setPermission("faction.job.use");
    }
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @return void
     */
    function execute(CommandSender $sender, string $commandLabel, array $args) : void
    {
        if(!$sender instanceof Player) {
            $sender->sendMessage(FactionPackAPI::PREFIX . "Только в игре!");
            return;
        }
        if(!$sender->hasPermission("faction.job.use")) {
            $sender->sendTitle("§cНедостаточно прав", "§f для выполнения этой команды!");
            return;
        }
        $member = FactionAPI::getPlayer($sender->getName());
        if(count($args) > 0) {
            if($args[0] == "info" && $member) {
                $sender->sendMessage("Ваша работа: " . $member->getFaction()->getName());
                $sender->sendMessage("Ваша роль: " . $member->getRank()->getName());
                return;
            } else {
                $sender->sendMessage(FactionPackAPI::PREFIX . "Используйте: /faction");
                $sender->sendMessage("§c>§a Статистика: /faction info");
            }
        }
        if($member) {
            if($sender->hasPermission("faction.job.admin")){
                FormBox::getAdminBox()->sendMainForm($sender);
            } else {
                FormBox::sendJobMainPage($sender);
            }
        } else {
            if(!empty($args[0]) && $args[0] == 'info'){
                $sender->sendMessage(FactionPackAPI::PREFIX . "Сначала устройтесь на работу!");
                return;
            }
            FormBox::sendMainForm($sender);
        }
    }
}