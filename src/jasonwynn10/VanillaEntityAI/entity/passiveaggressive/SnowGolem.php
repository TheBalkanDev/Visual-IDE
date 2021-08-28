<?php
declare(strict_types=1);
namespace jasonwynn10\VanillaEntityAI\entity\passiveaggressive;

use jasonwynn10\VanillaEntityAI\entity\Collidable;
use jasonwynn10\VanillaEntityAI\entity\CreatureBase;
use jasonwynn10\VanillaEntityAI\entity\Interactable;
use pocketmine\block\Lava;
use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Shears;

class SnowGolem extends CreatureBase implements Collidable, Interactable {
	public const NETWORK_ID = self::SNOW_GOLEM;
	public $width = 1.281;
	public $height = 1.875;

	public function initEntity() : void {
		if($this->namedtag->getByte("Pumpkin", 1, true) === 1)
			$this->setPumpkin(true);
		else
			$this->setPumpkin(false);
		$this->setTarget(null);
		parent::initEntity(); // TODO: Change the autogenerated stub
	}

	/**
	 * @param int $tickDiff
	 *
	 * @return bool
	 */
	public function entityBaseTick(int $tickDiff = 1) : bool {
		$biome = $this->level->getBiome($this->getFloorX(), $this->getFloorZ());
		if($biome->getTemperature() > 1)
			$this->setOnFire(200);

		if($this->level->getBlockLightAt($this->getFloorX(), $this->getFloorY(), $this->getFloorZ()) >= 14 or
		   $this->level->getBlock($this, true, false) instanceof Water or
		   $this->level->getBlock($this, true, false) instanceof Lava) { // TODO: check weather
			$this->setOnFire(200);
		}
		// TODO: only make snow in biomes with less than 0.8 temp
		return parent::entityBaseTick($tickDiff); // TODO: Change the autogenerated stub
	}

	/**
	 * @return array
	 */
	public function getDrops() : array {
		return [Item::get(Item::SNOWBALL, 0, mt_rand(0, 15))];
	}

	/**
	 * @return string
	 */
	public function getName() : string {
		return "Snow Golem";
	}

	/**
	 * @param Entity $entity
	 */
	public function onCollideWithEntity(Entity $entity) : void {
		// TODO: Implement onCollideWithEntity() method.
	}

	/**
	 * @param Player $player
	 */
	public function onPlayerLook(Player $player) : void {
		if($player->getInventory()->getItemInHand() instanceof Shears) {
			$this->getDataPropertyManager()->setString(Entity::DATA_INTERACTIVE_TAG, "Shear"); // Don't show button anymore
		}
	}

	/**
	 * @param Player $player
	 */
	public function onPlayerInteract(Player $player) : void {
		if($player->getInventory()->getItemInHand() instanceof Shears) {
			$this->setPumpkin(false);
		}
	}

	/**
	 * @param bool $wearing
	 *
	 * @return SnowGolem
	 */
	public function setPumpkin(bool $wearing = true) : self {
		$this->namedtag->setByte("Pumpkin", (int)$wearing);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SHEARED, !$wearing);
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasPumpkin() : bool {
		return (bool) $this->namedtag->getByte("Pumpkin", 1, true);
	}
}