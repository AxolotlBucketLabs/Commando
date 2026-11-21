<?php

declare(strict_types=1);

namespace CortexPE\Commando\args;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\network\mcpe\protocol\types\command\CommandSoftEnum;

final class SoftEnumArgument extends BaseArgument {
	public function __construct(string $name, string $enumName, bool $optional = false) {
		parent::__construct($name, $optional);

		$this->parameterData = CommandParameter::softEnum(
			$name,
			new CommandSoftEnum($enumName, []),
			0,
			$optional
		);
	}

	public function getNetworkType(): int {
		return AvailableCommandsPacket::ARG_TYPE_STRING;
	}

	public function getTypeName(): string {
		return "string";
	}

	public function canParse(string $testString, CommandSender $sender): bool {
		return $testString !== "";
	}

	public function parse(string $argument, CommandSender $sender): string {
		return $argument;
	}
}
