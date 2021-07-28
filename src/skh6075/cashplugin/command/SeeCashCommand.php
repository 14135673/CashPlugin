<?php

namespace skh6075\cashplugin\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use skh6075\cashplugin\CashPlugin;
use skh6075\economyplus\EconomyPlus;

class SeeCashCommand extends Command{

    protected CashPlugin $plugin;


    public function __construct(CashPlugin $plugin) {
        parent::__construct("캐쉬보기", "캐쉬보기 명령어 입니다.");
        $this->setPermission("seecash.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if (!$player instanceof Player) {
            $player->sendMessage(CashPlugin::$prefix . "인게임에서만 사용할 수 있습니다.");
            return false;
        }
        $name = array_shift($args) ?? "";
        if (trim($name) !== "") {
            if (($target = Server::getInstance()->getPlayer($name)) instanceof Player) {
                $name = $target->getLowerCaseName();
            }
            if ($this->plugin->isPlayerData($name)) {
                $player->sendMessage(CashPlugin::$prefix . "§f" . strtolower($name) . "님§7의 캐쉬: §f" . EconomyPlus::getInstance()->wonFormat($this->plugin->getCash($name), CashPlugin::UNIT_FORMAT));
            } else {
                $player->sendMessage(CashPlugin::$prefix . "해당 플레이어는 서버에 접속한 적이 없습니다.");
            }
        } else {
            $player->sendMessage(CashPlugin::$prefix . "§f" . $player->getLowerCaseName() . "님§7의 캐쉬: §f" . EconomyPlus::getInstance()->wonFormat($this->plugin->getCash($player), CashPlugin::UNIT_FORMAT));
        }
        return true;
    }
}