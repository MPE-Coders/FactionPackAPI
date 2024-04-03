<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI;

use pocketmine\plugin\PluginBase;
use XackiGiFF\FactionPackAPI\factions\Loader\LoaderFaction;
use XackiGiFF\FactionPackAPI\factions\Manager;
use XackiGiFF\FactionPackAPI\command\FactionCommand;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;

class FactionPackAPI extends PluginBase{
    const PREFIX = "§eФракции§e |§r ";
    private static FactionPackAPI $instance;

    public function onLoad(): void
    {
        self::$instance = $this;
        $this->saveResource("factions.yml");

        /* Инициализация дебагера */
        new TerminalLogger("MSK");
        TerminalLogger::startLogger();
        /* Конец блока */

    }

    public function onEnable(): void
    {
        TerminalLogger::warning("Внимание! Версия плагина DEV не предназначена для повседневного использования!");

        Manager::addDataFolderPath($this->getDataFolder());
        if(!is_dir(Manager::getDataFolderPath())) {
            mkdir(Manager::getDataFolderPath(), 0777, true);
        }

        new Manager();
        new LoaderFaction($this);

        $this->getServer()->getCommandMap()->register("FactionAPIPack", new FactionCommand("faction", "Фракции"));
    }

    public static function getInstance(): FactionPackAPI
    {
        return self::$instance;
    }
}
