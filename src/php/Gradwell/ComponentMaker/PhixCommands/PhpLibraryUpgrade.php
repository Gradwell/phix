<?php

namespace Gradwell\ComponentMaker\PhixCommands;

use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Phix_Project\PhixExtensions\CommandInterface;
use Phix_Project\CommandLineLib\DefinedSwitches;
use Phix_Project\CommandLineLib\DefinedSwitch;

use Gradwell\ComponentMaker\Entities\LibraryComponentFolder;

if (!class_exists('Gradwell\ComponentMaker\PhixCommands\PhpLibraryUpgrade'))
{
class PhpLibraryUpgrade extends PhpLibraryBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'php-library:upgrade';
        }

        public function getCommandDesc()
        {
                return 'upgrade the structure of a php-library component to the latest version';
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
                if ($lib->state != LibraryComponentFolder::STATE_NEEDSUPGRADE)
                {
                        $se->output($context->errorStyle, $context->errorPrefix);

                        // what do we need to tell the user to do?
                        switch ($lib->state)
                        {
                                case LibraryComponentFolder::STATE_UPTODATE:
                                        $se->outputLine(null, "folder is already at latest version");
                                        break;

                                case LibraryComponentFolder::STATE_EMPTY:
                                        $se->outputLine(null, "folder is not a php-library");
                                        $se->output(null, 'use ');
                                        $se->output($context->commandStyle, $context->argvZero . ' php-library:init');
                                        $se->outputLine(null, ' to initialise this folder');
                                        break;

                                default:
                                        $se->outputLine(null, 'I do not know what to do with this folder');
                                        break;
                        }

                        return 1;
                }

                // if we get here, we have a green light
                $lib->upgradeComponent();

                // if we get here, it worked (ie, no exception!!)
                $so->outputLine(null, 'Upgraded php-library component in ' . $folder . ' to the latest version');
        }
}
}