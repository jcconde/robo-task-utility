# Robo Task Utility

A small command-line utility built with Robo (Consolidation\Robo) that demonstrates how to organize and run tasks/commands in a PHP project.

This repository currently includes a simple Hello World command, a sample configuration file, and Composer-based autoloading.

## Requirements
- PHP 8.3+
- Composer
- ext-json (bundled by default with most PHP builds)

The project depends on:
- consolidation/robo ^5.1

See composer.json for the full list of dev tools (PHPUnit, PHPStan, PHPMD, PHP CS Fixer, PHPCS).

## Installation
Clone the repository and install dependencies via Composer:

```bash
git clone https://github.com/jcconde/robo-task-utility.git
cd robo-task-utility
composer install
```

If you want to include this as a library in another project, require it with Composer (assuming it's published to Packagist under the same name):

```bash
composer require jcconde/robo-task-utility
```

## Project layout
- src/Commands/HelloWorldCommand.php — Example command that prints “Hello world”.
- src/etc/config.yml — Example configuration file (name, version, env vars).
- VERSION — Project version file.

## Usage
Robo provides a single executable. After `composer install`, you can list available commands and run them.

On Linux/macOS:
```bash
vendor/bin/robo list
vendor/bin/robo hello
```

Expected output of the example command:
```
Hello world
```

### How it works
- Robo automatically discovers command classes under the PSR-4 base namespace defined in composer.json (Juanca\\RoboTaskUtility) and the relative sub-namespace `Commands`.
- Any public method in a class that extends `Robo\Tasks` becomes an executable command. For example, the method `hello(ConsoleIO $io): void` from `HelloWorldCommand` is available as `hello`.

## Creating new commands
1. Create a new PHP class under `src/Commands` that extends `Robo\Tasks`.
2. Every command class must have Command suffix.
3. Add public methods for each command you want to expose. Use type-hinted `Robo\Symfony\ConsoleIO $io` for input/output convenience.

Example:
```php
<?php

namespace Juanca\RoboTaskUtility\Commands;

use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

class GreetCommand extends Tasks
{
    /**
     * Greet someone by name.
     *
     * @command greet
     */
    public function greet(ConsoleIO $io, string $name = "World"): void
    {
        $io->say("Hello, {$name}!");
    }
}
```

Run it:
```bash
vendor/bin/robo greet Alice
```

Notes:
- You can optionally use the `@command` annotation to map method names to specific command names. Without it, Robo infers the command name from the method name.
- Use `--help` on any command to see available options/arguments.

## Configuration
A sample configuration file is provided at `src/etc/config.yml`:
```yaml
project:
  name: "Juanca Tools"
  version: "0.1.0"
  author: "Juanca"

paths:
  src: "src"

env:
  mode: "development"
  debug: true
```
You can load and use configuration values within your commands as needed. Consider using Consolidation\Config if you plan to expand configuration support.

## Development
- Lint/format: Use PHP CS Fixer / PHPCS per your local setup.
- Static analysis: PHPStan
- Mess detector: PHPMD
- Tests: PHPUnit

Typical commands (adjust to your environment):
```bash
# Coding standards
vendor/bin/php-cs-fixer fix --dry-run
vendor/bin/phpcs

# Static analysis
vendor/bin/phpstan analyse src

# Mess detection
vendor/bin/phpmd src text phpmd.xml.dist
```

## Troubleshooting
- If `vendor/bin/robo` is not found, ensure `composer install` completed successfully.
- If commands do not appear in `robo list`, verify:
  - Your classes are under the `Juanca\\RoboTaskUtility\\Commands` namespace.
  - Composer autoload is up to date: `composer dump-autoload -o`.
  - Method visibility is `public` and the class extends `Robo\\Tasks`.
