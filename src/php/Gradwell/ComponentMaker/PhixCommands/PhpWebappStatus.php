<?php

namespace Gradwell\ComponentMaker\PhixCommands;

use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Phix_Project\PhixExtensions\CommandInterface;
use Phix_Project\CommandLineLib\DefinedSwitches;
use Phix_Project\CommandLineLib\DefinedSwitch;

use Gradwell\ComponentMaker\Entities\WebappComponentFolder;

if (!class_exists('Gradwell\ComponentMaker\PhixCommands\PhpWebappStatus'))
{
class PhpWebappStatus extends ComponentCommandBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'php-webapp:status';
        }

        public function getCommandDesc()
        {
                return 'check the status of a php-webapp component';
        }

        public function getCommandArgs()
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
                $lib = new WebappComponentFolder($folder);

                // what do we need to tell the user to do?
                switch ($lib->state)
                {
                        case WebappComponentFolder::STATE_UPTODATE:
                                $so->outputLine(null, "folder has already been initialised");
                                break;

                        case WebappComponentFolder::STATE_NEEDSUPGRADE:
                                $so->outputLine(null, "folder has been initialised; needs upgrade");
                                $so->output(null, 'use ');
                                $so->output($context->commandStyle, $context->argvZero . ' php-webapp:upgrade');
                                $so->outputLine(null, ' to upgrade this folder');
                                break;

                        case WebappComponentFolder::STATE_EMPTY:
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