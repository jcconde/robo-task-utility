<?php

/**
 * @copyright 2025 Onetree. All rights reserved.
 * @author    Juanca <juancarlosc@onetree.com>
 */

declare(strict_types=1);

namespace Juanca\RoboTaskUtility\Commands;

use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

/**
 * @SuppressWarnings("PHPMD.ShortVariable")
 */
class HelloWorldCommand extends Tasks
{
    /**
     * @param ConsoleIO $io
     * @return void
     * @command hello
     */
    public function hello(ConsoleIO $io): void
    {
        $io->say("Hello world");
    }
}
