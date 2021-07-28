<?php

namespace skh6075\cashplugin;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use skh6075\cashplugin\command\AddCashCommand;
use skh6075\cashplugin\command\ReduceCashCommand;
use skh6075\cashplugin\command\SeeCashCommand;
use skh6075\cashplugin\command\SetCashCommand;
use skh6075\cashplugin\listener\EventListener;
use skh6075\economyplus\EconomyPlus;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_encode;
use function json_decode;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;
function convertName($player): string{
    return strtolower($player instanceof Player ? $player->getLowerCaseName() : $player);
}

class CashPlugin extends PluginBase{
    use SingletonTrait;

    public static string $prefix = "§l§f[§e 캐쉬 §f]§r§7 ";

    protected array $config = [];

    public const UNIT_FORMAT = "§r§l§eC§r§7";


    public function onLoad(): void{
        self::setInstance($this);
    }

    public function onEnable(): void{
        $this->config = json_decode(file_exists($this->getDataFolder() . "config.json") ? file_get_contents($this->getDataFolder() . "config.json") : "{}", true);
        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()), [
            new SeeCashCommand($this),
            new AddCashCommand($this),
            new ReduceCashCommand($this),
            new SetCashCommand($this)
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onDisable(): void{
        file_put_contents($this->getDataFolder() . "config.json", json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function isPlayerData($player): bool{
        return isset($this->config[convertName($player)]);
    }

    public function addPlayerData(Player $player): void{
        $this->config[$player->getLowerCaseName()] = 0;
    }

    public function getCash($player): int{
        return $this->config[convertName($player)] ?? 0;
    }

    public function addCash($player, int $amount): void{
        if (!$this->isPlayerData($player)) {
            return;
        }
        $this->config[convertName($player)] += $amount;
        if (($player = $this->getServer()->getPlayer(convertName($player))) instanceof Player)
            $player->sendMessage(self::$prefix . "§f" . EconomyPlus::getInstance()->wonFormat($amount, self::UNIT_FORMAT) . "§r§7 만큼 캐쉬를 획득하셨습니다.");
    }

    public function reduceCash($player, int $amount): void{
        if (!$this->isPlayerData($player)) {
            return;
        }
        $this->config[convertName($player)] -= $amount;
        if ($this->config[convertName($player)] < 0)
            $this->config[convertName($player)] = 0;
    }

    public function setCash($player, int $amount): void{
        if (!$this->isPlayerData($player)) {
            return;
        }
        $this->config[convertName($player)] = ($amount < 0 ? 0 : $amount);
    }
}