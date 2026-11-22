# Commando

Commando is a command framework virion for Axolotl-PM. It parses named arguments, validates input, supports subcommands and constraints, and adds argument data to the commands sent to Bedrock clients.

[![Build](https://github.com/AxolotlBucketLabs/Commando/actions/workflows/build.yml/badge.svg?branch=main)](https://github.com/AxolotlBucketLabs/Commando/actions/workflows/build.yml)
[![License](https://img.shields.io/github/license/AxolotlBucketLabs/Commando?label=License)](LICENSE)
[![Discord](https://img.shields.io/discord/1568295845374206052?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/Pk4FrNvHnM)

## Requirements

| Component | Requirement |
| --- | --- |
| PHP | 8.4 or newer |
| Axolotl-PM | API 5.51.2 |
| PacketHandler | Virion `PacketHandler` version `0.1.10` |

Commando is a library, not a standalone plugin. Include it and its declared `PacketHandler` dependency in the consumer plugin's build. The library manifest declares that dependency in `virion.yml`.

## Install with DevTools

Place the Commando source folder under the server's `virions/` directory. Add the matching `PacketHandler` source folder under `virions/PacketHandler/`. Declare Commando in the consumer plugin's `devtools.yml`:

```yaml
virions:
  - name: Commando
    version: "3.3.0"
```

DevTools checks the dependency declared by Commando and loads the compatible virion set. See the DevTools [shared virion guide](https://github.com/AxolotlBucketLabs/DevTools/blob/v4.1.1/docs/shared-virions.md) for Composer based builds and dependency preparation.

## Register the packet hook

Register `PacketHooker` once during plugin startup. The hook adds Commando's argument metadata to the `AvailableCommandsPacket` sent to clients.

```php
use CortexPE\Commando\PacketHooker;

if (!PacketHooker::isRegistered()) {
    PacketHooker::register($this);
}
```

## Define and register a command

Extend `BaseCommand`, register arguments in `prepare()`, and handle the parsed values in `onRun()`.

```php
<?php

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

final class GreetCommand extends BaseCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$sender->sendMessage("Hello, " . $args["name"] . "!");
	}
}
```

Register the command from your plugin's `onEnable()` method:

```php
$this->getServer()->getCommandMap()->register(
	"example",
	new GreetCommand($this, "greet", "Greet a player.")
);
```

Arguments are passed to `onRun()` as a map from argument name to parsed value. Optional arguments are omitted when the sender does not provide them. `TextArgument` consumes the rest of the input. Commando also provides integer, float, boolean, vector, block position, string enum, and item arguments. `ItemArgument` parses aliases registered with Axolotl-PM's `StringToItemParser` into `Item` instances. A `StringEnumArgument` subclass can pass a plugin-scoped enum name as the third constructor argument to control its client enum name. Use `SoftEnumArgument` for values that should appear as client suggestions while still allowing other input.

Register a soft enum with a unique, plugin-scoped name before registering commands that use it. Update the stored values when the suggestions change:

```php
use CortexPE\Commando\args\SoftEnumArgument;
use CortexPE\Commando\store\SoftEnumStore;
use pocketmine\network\mcpe\protocol\types\command\CommandSoftEnum;

SoftEnumStore::addEnum(new CommandSoftEnum("example.warps", ["spawn", "shop"]));

// In the command's prepare() method:
$this->registerArgument(0, new SoftEnumArgument("warp", "example.warps"));

// Refresh the suggestions when they change:
SoftEnumStore::updateEnum("example.warps", ["spawn", "shop", "arena"]);
```

Use `BaseSubCommand` for subcommands. Add `BaseConstraint` implementations to restrict who can run a command or see it in client command suggestions. Use `sendUsage()` and `setErrorFormat()` to customize feedback.

## Build

The GitHub Actions workflow uses AxolotlBucketLabs DevTools `v4.1.1` to build and inspect a virion PHAR. It runs PHPStan at `max` against the locked Axolotl-PM `5.51.2` source and uploads the PHAR as a workflow artifact. The workflow does not publish a GitHub release.

To build from an Axolotl-PM server with DevTools installed, run:

```text
/devtools build virion Commando
```

The server writes the artifact to `build/Commando.phar`.

## License

Commando is distributed under the GNU Lesser General Public License v3.0 only. See [LICENSE](LICENSE).
