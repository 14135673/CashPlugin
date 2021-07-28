<?php

namespace skh6075\cashplugin\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use skh6075\cashplugin\CashPlugin;

class SetCashCommand extends Command{

    protected CashPlugin $plugin;


    public function __construct(CashPlugin $plugin) {
        parent::__construct("캐쉬설정", "캐쉬설정 명령어 입니다.");
        $this->setPermission("setcash.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if (!$player instanceof Player) {
            $player->sendMessage(CashPlugin::$prefix . "인게임에서만 사용할 수 있습니다.");
            return false;
        }
        $name = array_shift($args) ?? "";
        $amount = array_shift($args) ?? "";
        if (trim($name) !== "" and trim($amount) and is_numeric($amount)) {
            $this->plugin->setCash($name, $amount);
            $player->sendMessage(CashPlugin::$prefix . "캐쉬를 차감하였습니다.");
        } else {
            $player->sendMessage(CashPlugin::$prefix . "/캐쉬설정 [닉네임] [캐쉬]");
        }
        return true;
    }
}