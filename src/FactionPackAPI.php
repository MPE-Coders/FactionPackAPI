<?php

declare(strict_types=1);

namespace XackiGiFF\FactionPackAPI;

use JsonException;
use pocketmine\plugin\PluginBase;
use XackiGiFF\FactionPackAPI\factions\api\FactionAPI;
use XackiGiFF\FactionPackAPI\factions\Loader\LoaderFaction;
use XackiGiFF\FactionPackAPI\factions\Manager;
use XackiGiFF\FactionPackAPI\command\FactionCommand;
use XackiGiFF\TerminalLogger\utils\TerminalLogger;

class FactionPackAPI extends PluginBase{
    const PREFIX = "§eФракции§e |§r ";
    private static FactionPackAPI $instance;

    public string $dataFolderPath;

    /**
     * @throws JsonException
     */
    public function onEnable(): void
    {
        $this->saveResource("factions.yml");

        self::$instance = $this;
        /* Инициализация дебагера */
        new TerminalLogger("MSK");
        TerminalLogger::startLogger();
        /* Конец блока */

        Manager::addDataFolderPath($this->getDataFolder());
        if(!is_dir(Manager::getDataFolderPath())) {
            mkdir(Manager::getDataFolderPath(), 0777, true);
        }

        new Manager();
        new LoaderFaction($this);
        $this->getServer()->getCommandMap()->register("FactionAPIPack", new FactionCommand("faction", "Фракции"));

        /* DEBUG BLOCK */

//        FactionAPI::registerPlayer("name", "f3", "r1");
        $p = FactionAPI::getPlayer("namename");
        //var_dump($p);
//        $p->setRank("r5");
//        $p->savePlayer();
//
//        $member = FactionAPI::getPlayer($p->getName());
//        $member->unregisterPlayer();


        /* END BLOCK */

    }
    public static function getInstance() : FactionPackAPI
    {
        return self::$instance;
    }

}
