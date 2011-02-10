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
 * @package     Phix_Project
 * @subpackage  Phix
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.Phix-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\Phix;

use Phix_Project\CommandLineLib\CommandLineParser;
use Phix_Project\CommandLineLib\DefinedSwitches;
use Phix_Project\CommandLineLib\ParsedSwitches;

use Phix_Project\PhixSwitches\PhixSwitches;

class Phix
{
        protected $libraryNamespaces = array();
        
        /**
         * It all happens here!
         *
         * @param array $argv
         */
        public function main($argv)
        {
                // step 0: create some plumbing
                $context = new Context();
                $context->argvZero = $argv[0];

                // step 1: parse the command-line args
                // we do this first because it may change where we look
                // for our commands
                $context->PhixDefinedSwitches = $this->buildPhixSwitches();
                list($PhixParsedSwitches, $argsIndex) = $this->parsePhixArgs($context, $argv);

                // step 2: process the switches we have just parsed
                //
                // we parse the switches twice; once to find out where to
                // look for extensions, and then once more to decide what
                // to do once the extensions are loaded
                $errCode = $this->processPhixSwitchesBeforeExtensionLoad($context, $PhixParsedSwitches);
                if ($errCode !== null)
                {
                        return $errCode;
                }

                $context->commandsList = $this->loadPhixExtensions($context, $PhixParsedSwitches);

                $errCode = $this->processPhixSwitchesAfterExtensionLoad($context, $PhixParsedSwitches);
                if ($errCode !== null)
                {
                        return $errCode;
                }

                // step 3: do we have a valid command to execute?
                if (!isset($argv[$argsIndex]))
                {
                        // no command given - special case
                        $argv[$argsIndex] = 'help';
                }
                $errCode = $this->validateCommand($argv, $argsIndex, $context);
                if ($errCode !== null)
                {
                        return $errCode;
                }

                // step 4: execute the validated command
                $errCode = $this->processCommand($argv, $argsIndex, $context);

                // all done
                return $errCode;
        }
        
        protected function buildPhixSwitches()
        {
                $switches = new DefinedSwitches();
                \Phix_Project\PhixSwitches\PhixSwitches::buildSwitches($switches);

                // all done
                return $switches;
        }

        protected function parsePhixArgs(Context $context, $argv)
        {
                // switches before the first command are switches that
                // affect Phix.
                //
                // switches after the first command are switches for
                // that command

                $parser = new CommandLineParser();
                return $parser->parseSwitches($argv, 1, $context->PhixDefinedSwitches);
        }

        protected function processPhixSwitchesBeforeExtensionLoad(Context $context, ParsedSwitches $ParsedSwitches)
        {
                // what switches do we have?
                $parsedSwitches = $ParsedSwitches->getSwitches();

                // let's deal with them
                foreach ($parsedSwitches as $parsedSwitch)
                {
                        $switchName = $parsedSwitch->name;
                        $className = '\\Phix_Project\\PhixSwitches\\' . ucfirst($switchName) . 'Switch';
                        $errCode = $className::processBeforeExtensionLoad($context, $ParsedSwitches->getArgsForSwitch($switchName));
                        if ($errCode !== null)
                        {
                                return $errCode;
                        }
                }

                // all done - return NULL to signify that we're not yet
                // ready to terminate Phix
                return null;
        }

        protected function loadPhixExtensions(Context $context, ParsedSwitches $ParsedSwitches)
        {
                // create something to find the commands
                $extensionsFinder = new ExtensionsFinder();

                // seed the commandsFinder with a list of where to look
                // if the user has given us any hints
                if ($ParsedSwitches->testHasSwitch("include"))
                {
                        $switch = $ParsedSwitches->getSwitch("include");
                        $args   = $ParsedSwitches->getArgsForSwitch("include");

                        foreach ($args as $path)
                        {
                                $extensionsFinder->addFolderToSearch($path);
                        }
                }

                // alright, let's thrash that hard disk
                // hope you have an SSD ;-)
                $commandsList = $extensionsFinder->findExtensions();

                // all done
                return $commandsList;
        }

        protected function processPhixSwitchesAfterExtensionLoad(Context $context, ParsedSwitches $ParsedSwitches)
        {
                // what switches do we have?
                $parsedSwitches = $ParsedSwitches->getSwitches();

                // let's deal with them
                foreach ($parsedSwitches as $parsedSwitch)
                {
                        $switchName = $parsedSwitch->name;
                        $className = '\\Phix_Project\\PhixSwitches\\' . ucfirst($switchName) . 'Switch';
                        $errCode = $className::processAfterExtensionLoad($context, $ParsedSwitches->getArgsForSwitch($switchName));
                        if ($errCode !== null)
                        {
                                return $errCode;
                        }
                }

                // all done - return NULL to signify that we're not yet
                // ready to terminate Phix
                return null;
        }

        protected function validateCommand($argv, $argsIndex, Context $context)
        {
                // $argsIndex points to the command that the user
                // wishes to execute

                $commandName = $argv[$argsIndex];

                // do we have a recognised command?
                if (!$context->commandsList->testHasCommand($commandName))
                {
                        // no, we do not
                        $this->outputUsageHint($context, "unknown command '$commandName'");
                        return 1;
                }

                // if we get here, we know that we have a valid command
                return null;
        }

        protected function processCommand($argv, $argsIndex, Context $context)
        {
                $commandName = $argv[$argsIndex];
                $argsIndex++;

                $command = $context->commandsList->getCommand($commandName);

                $errCode = $command->validateAndExecute($argv, $argsIndex, $context);
                return $errCode;
        }

        protected function outputUsageHint(Context $context, $errorMessage)
        {
                $se = $context->stderr;
                $se->output($context->errorStyle, 'error: ');
                $se->outputLine(null, $errorMessage);
                $se->output(null, "use ");
                $se->output($context->exampleStyle, 'Phix -h');
                $se->outputLine(null, " for help");
        }
}
