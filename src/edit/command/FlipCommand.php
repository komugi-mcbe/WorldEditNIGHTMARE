<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\functions\operation\Operations;
use edit\command\util\FlagChecker;
use edit\command\util\HelpChecker;
use edit\command\util\DefinedChecker;
use edit\math\transform\AffineTransform;
use edit\command\util\SpaceChecker;

class FlipCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"クリップボードをひっくり返します",
			"//flip [<方向>]"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		if(!Main::$canUseNotOp && !$sender->isOp()){
			return false;
		}

		if(HelpChecker::check($args) || SpaceChecker::check($args)){
			$sender->sendMessage("§c効果: §aクリップボードをひっくり返します\n".
					     "§c使い方: §a//flip [<方向>]");
			return false;
		}

		if(DefinedChecker::checkClipboard($sender)) {
			return false;
		}

		if(count($args) < 1){
			$direction = Main::getCardinalDirection($sender);
		}else{
			$direction = Main::getFlipDirection($sender, $args[0]);
		}

		$session = Main::getInstance()->getEditSession($sender);

		$holder = $session->getClipboard();
		$transform = new AffineTransform();
		$transform = $transform->scale($direction->positive()->multiply(-2)->add(1, 1, 1));
		$holder->setTransform($holder->getTransform()->combine($transform));

		$sender->sendMessage(Main::LOGO."クリップボードをひっくり返しました");
		Main::getInstance()->getServer()->broadcastMessage("§7".Main::LOGO.$sender->getName()." が /".$this->getName()." を利用");
		return true;
	}
}