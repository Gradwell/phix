<?php

namespace Gradwell\ComponentMaker\PhixCommands;

use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Phix_Project\PhixExtensions\CommandBase;
use Phix_Project\CommandLineLib\DefinedSwitches;
use Phix_Project\CommandLineLib\DefinedSwitch;

use Gradwell\ComponentMaker\Entities\LibraryComponentFolder;

if (!class_exists('\Gradwell\ComponentMaker\PhixCommands\ComponentCommandBase'))
{
class ComponentCommandBase extends CommandBase
{
        protected function validateFolder($args, $argsIndex, Context $context)
        {
                $se = $context->stderr;

                // $args[$argsIndex] should point at the folder where we
                // want to create the initial structure

                if (!isset($args[$argsIndex]))
                {
                        $se->output($context->errorStyle, $context->errorPrefix);
                        $se->outputLine(null, 'missing argument <folder>');

                        return 1;
                }

                // is the folder a real directory?

                if (!\is_dir($args[$argsIndex]))
                {
                        $se->output($context->errorStyle, $context->errorPrefix);
                        $se->outputLine(null, 'folder ' . $args[$argsIndex] . ' not found');

                        return 1;
                }

                // can we write to the folder?

                if (!\is_writeable($args[$argsIndex]))
                {
                        $se->output($context->errorStyle, $context->errorPrefix);
                        $se->outputLine(null, 'cannot write to folder ' . $args[$argsIndex]);

                        return 1;
                }

                // if we get here, we have run out of things that we can
                // check for right now

                return null;
        }
}
}