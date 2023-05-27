<?php
namespace com\leowascoding\Coinflip;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use onebone\bedrockeconomy\BedrockEconomyAPI;

class CoinFlipPlugin extends PluginBase implements Listener {
    /** @var BedrockEconomyAPI */
    private $economy;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->economy = $this->getServer()->getPluginManager()->getPlugin("BedrockEconomy");
        if ($this->economy === null) {
            $this->getLogger()->error("BedrockEconomy plugin not found. Disabling CoinFlipPlugin.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->getLogger()->info("CoinFlipPlugin enabled!");
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getId() === 266) { // Assuming gold ingot is used for coinflip
            $this->flipCoin($player);
            $event->setCancelled(true);
        }
    }

    private function flipCoin($player) {
        $result = (bool)random_int(0, 1);
        $amount = 100; // Amount to bet, adjust as needed

        if ($this->economy->reduceMoney($player, $amount)) {
            $message = $result ? "You won!" : "You lost!";
            $this->economy->addMoney($player, $result ? $amount * 2 : 0);
        } else {
            $message = "You don't have enough money to bet.";
        }

        $player->sendMessage(TextFormat::GREEN . "[CoinFlip] " . $message);
    }
}

