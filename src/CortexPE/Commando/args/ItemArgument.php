<?php

declare(strict_types=1);

namespace CortexPE\Commando\args;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use function array_map;
use function array_values;

final class ItemArgument extends StringEnumArgument {
	public function getTypeName(): string {
		return "item";
	}

	public function canParse(string $testString, CommandSender $sender): bool {
		return $this->getValue($testString) !== null;
	}

	public function parse(string $argument, CommandSender $sender): ?Item {
		return $this->getValue($argument);
	}

	public function getValue(string $string): ?Item {
		return StringToItemParser::getInstance()->parse($string);
	}

	/** @return list<string> */
	public function getEnumValues(): array {
		return array_map(
			static fn(string|int $alias): string => (string) $alias,
			array_values(StringToItemParser::getInstance()->getKnownAliases())
		);
	}
}
