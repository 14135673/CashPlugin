<?php

namespace skh6075\cashplugin\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use skh6075\cashplugin\CashPlugin;

class EventListener implements Listener{

    protected CashPlugin $plugin;


    public function __construct(CashPlugin $plugin) {
        $this->plugin = $plugin;
    }

    /** @priority HIGHEST */
    public function onPlayerJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();

        if (!$this->plugin->isPlayerData($player)) {
            $this->plugin->addPlayerData($player);
        }
    }
}