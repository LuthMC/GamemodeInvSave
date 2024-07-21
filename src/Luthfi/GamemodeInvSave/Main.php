<?php

namespace Luthfi\GamemodeInvSave;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\inventory\PlayerInventory;

class Main extends PluginBase implements Listener {

    private $inventories = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable(): void {
    }

    /**
     * Handles player gamemode change events.
     *
     * @param PlayerGameModeChangeEvent $event
     */
    public function onGamemodeChange(PlayerGameModeChangeEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $newGamemode = $event->getNewGamemode();

        $this->inventories[$name][$player->getGamemode()] = [
            'inventory' => $player->getInventory()->getContents(),
            'armor' => $player->getArmorInventory()->getContents()
        ];

        if (isset($this->inventories[$name][$newGamemode])) {
            $player->getInventory()->setContents($this->inventories[$name][$newGamemode]['inventory']);
            $player->getArmorInventory()->setContents($this->inventories[$name][$newGamemode]['armor']);
        } else {
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
        }
    }
}
