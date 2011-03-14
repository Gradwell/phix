<?php

/**
 * Copyright (c) 2010 Gradwell dot com Ltd.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Gradwell dot com Ltd nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Gradwell
 * @subpackage  ComponentMaker
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Gradwell\ComponentMaker\PhixCommands;

use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Phix_Project\PhixExtensions\CommandInterface;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\CommandLineLib\DefinedSwitch;

use Gradwell\ComponentMaker\Entities\WebappComponentFolder;

if (!class_exists('Gradwell\ComponentMaker\PhixCommands\PhpWebappInit'))
{
class PhpWebappInit extends ComponentCommandBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'php-webapp:init';
        }

        public function getCommandDesc()
        {
                return 'initialise the directory structure of a php-webapp component';
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
                $lib = new WebappComponentFolder($folder);
                if ($lib->state != WebappComponentFolder::STATE_EMPTY)
                {
                        $se->output($context->errorStyle, $context->errorPrefix);

                        // what do we need to tell the user to do?
                        switch ($lib->state)
                        {
                                case WebappComponentFolder::STATE_UPTODATE:
                                        $se->outputLine(null, "folder has already been initialised");
                                        break;

                                case WebappComponentFolder::STATE_NEEDSUPGRADE:
                                        $se->outputLine(null, "folder has been initialised; needs upgrade");
                                        $se->output(null, 'use ');
                                        $se->output($context->commandStyle, $context->argvZero . ' php-webapp:upgrade');
                                        $se->outputLine(null, ' to upgrade this folder');
                                        break;

                                default:
                                        $se->outputLine(null, 'I do not know what to do with this folder');
                                        break;
                        }

                        return 1;
                }

                // if we get here, we have a green light
                $lib->createComponent();

                // if we get here, it worked (ie, no exception!!)
                $so->outputLine(null, 'Initialised empty php-webapp component in ' . $folder);
        }
}
}