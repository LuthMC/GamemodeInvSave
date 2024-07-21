<?php

namespace Luthfi\GamemodeInvSave;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private $inventoryData;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->inventoryData = new Config($this->getDataFolder() . "inventories.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable(): void {
        $this->inventoryData->save();
    }

    public function onGamemodeChange(PlayerGameModeChangeEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $newGamemode = $event->getNewGamemode();
        $currentGamemode = $player->getGamemode();

        $this->saveInventory($player, $currentGamemode);

        $this->loadInventory($player, $newGamemode);
    }

    private function saveInventory(Player $player, int $gamemode): void {
        $name = $player->getName();
        $inventoryContents = [];
        foreach ($player->getInventory()->getContents() as $slot => $item) {
            $inventoryContents[$slot] = $item->jsonSerialize();
        }
        $armorContents = [];
        foreach ($player->getArmorInventory()->getContents() as $slot => $item) {
            $armorContents[$slot] = $item->jsonSerialize();
        }

        $this->inventoryData->setNested("$name.$gamemode.inventory", $inventoryContents);
        $this->inventoryData->setNested("$name.$gamemode.armor", $armorContents);
    }

    private function loadInventory(Player $player, int $gamemode): void {
        $name = $player->getName();

        $inventoryContents = $this->inventoryData->getNested("$name.$gamemode.inventory", []);
        $armorContents = $this->inventoryData->getNested("$name.$gamemode.armor", []);

        $playerInventory = $player->getInventory();
        $armorInventory = $player->getArmorInventory();

        $playerInventory->clearAll();
        $armorInventory->clearAll();

        foreach ($inventoryContents as $slot => $itemData) {
            $playerInventory->setItem($slot, Item::jsonDeserialize($itemData));
        }
        foreach ($armorContents as $slot => $itemData) {
            $armorInventory->setItem($slot, Item::jsonDeserialize($itemData));
        }
    }
}
