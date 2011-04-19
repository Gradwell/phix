<?php

namespace Phix_Project\Phix;

use Phix_Project\PhixExtensions\CommandsBase;
use Phix_Project\PhixExtensions\CommandsInterface;

class CommandsFinderDummy extends CommandsBase implements CommandsInterface
{
        public function getCommandName()
        {
                return 'commandsFinderDummy';
        }

        public function getCommandDesc()
        {
                return 'a dummy command to be found by the CommandsFinder tests';
        }
}
?>
