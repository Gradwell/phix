<?php

namespace Gradwell\ComponentMaker\PhinCommands;

use Phin_Project\Phin\CommandsList;
use Phin_Project\Phin\Context;
use Phin_Project\PhinExtensions\CommandInterface;
use Phin_Project\CommandLineLib\DefinedSwitches;
use Phin_Project\CommandLineLib\DefinedSwitch;

use Gradwell\ComponentMaker\Entities\LibraryComponentFolder;

if (!class_exists('Gradwell\ComponentMaker\PhinCommands\PhpLibraryStatus'))
{
class PhpLibraryStatus extends PhpLibraryBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'php-library:status';
        }

        public function getCommandDesc()
        {
                return 'check the status of a PEAR-compatible library component';
        }

        public function  getCommandArgs()
        {
                return array
                (
                        '<folder>'      => '<folder> is a path to an existing folder, which you must have permission to write to.',
                );
        }

        public function validateAndExecute($args, $argsIndex, Context $context)
        {
                $so = $context->stdout;
                $se = $context->stderr;

                // do we have a folder to init?
                $errorCode = $this->validateFolder($args, $argsIndex, $context);
                if ($errorCode !== null)
                {
                        return $errorCode;
                }
                $folder = $args[$argsIndex];

                // has the folder already been initialised?
                $lib = new LibraryComponentFolder($folder);

                // what do we need to tell the user to do?
                switch ($lib->state)
                {
                        case LibraryComponentFolder::STATE_UPTODATE:
                                $so->outputLine(null, "folder has already been initialised");
                                break;

                        case LibraryComponentFolder::STATE_NEEDSUPGRADE:
                                $so->outputLine(null, "folder has been initialised; needs upgrade");
                                $so->output(null, 'use ');
                                $so->output($context->commandStyle, $context->argvZero . ' php-library:upgrade');
                                $so->outputLine(null, ' to upgrade this folder');
                                break;

                        case LibraryComponentFolder::STATE_EMPTY:
                                $so->outputLine(null, 'folder is empty');
                                break;

                        default:
                                $se->output($context->errorStyle, $context->errorPrefix);
                                $se->outputLine(null, 'I do not know what to do with this folder');
                                break;
                }

                return 1;
        }
}
}