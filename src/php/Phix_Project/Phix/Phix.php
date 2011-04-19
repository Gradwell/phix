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
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\Phix;

use Gradwell\CommandLineLib\CommandLineParser;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\CommandLineLib\ParsedSwitches;

use Phix_Project\PhixSwitches\PhixSwitches;

class Phix
{
        protected $lastContext;

        protected $libraryNamespaces = array();
        
        /**
         * It all happens here!
         *
         * @param array $argv
         */
        public function main($argv)
        {
                // step 0: create some plumbing
                $this->lastContext = $context = new Context();
                $context->argvZero = $argv[0];

                // Register error handler
                \set_error_handler(array($this, 'errorHandler'));

                // step 1: parse the command-line args
                // we do this first because it may change where we look
                // for our commands
                $context->phixDefinedSwitches = $this->buildPhixSwitches();
                list($phixParsedSwitches, $argsIndex) = $this->parsePhixArgs($context, $argv);

                // step 2: process the switches we have just parsed
                //
                // we parse the switches twice; once to find out where to
                // look for extensions, and then once more to decide what
                // to do once the extensions are loaded
                $errCode = $this->processPhixSwitchesBeforeExtensionLoad($context, $phixParsedSwitches, $argv, $argsIndex);
                if ($errCode !== null)
                {
                        return $errCode;
                }

                $context->commandsList = $this->loadPhixExtensions($context, $phixParsedSwitches);

                $errCode = $this->processPhixSwitchesAfterExtensionLoad($context, $phixParsedSwitches);
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
                $switches = PhixSwitches::buildSwitches();

                // all done
                return $switches;
        }

        protected function parsePhixArgs(Context $context, $argv)
        {
                // switches before the first command are switches that
                // affect phix.
                //
                // switches after the first command are switches for
                // that command

                $parser = new CommandLineParser();
                return $parser->parseSwitches($argv, 1, $context->phixDefinedSwitches);
        }

        protected function processPhixSwitchesBeforeExtensionLoad(Context $context, ParsedSwitches $ParsedSwitches, $argv, $argsIndex)
        {
                // what switches do we have?
                $parsedSwitches = $ParsedSwitches->getSwitches();

                // let's deal with them
                foreach ($parsedSwitches as $parsedSwitch)
                {
                        $switchName = $parsedSwitch->name;
                        $className = '\\Phix_Project\\PhixSwitches\\' . ucfirst($switchName) . 'Switch';
                        $errCode = $className::processBeforeExtensionLoad($context, $ParsedSwitches->getArgsForSwitch($switchName), $argv, $argsIndex);
                        if ($errCode !== null)
                        {
                                return $errCode;
                        }
                }

                // all done - return NULL to signify that we're not yet
                // ready to terminate phix
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
                // ready to terminate phix
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
                $se->output($context->exampleStyle, 'phix -h');
                $se->outputLine(null, " for help");
        }
        
        public function errorHandler($error_code, $message = '', $file = '', $line = 0)
        {
                $se = $this->lastContext->stderr;
                
                $se->output($this->lastContext->errorStyle, $this->lastContext->errorPrefix);
                $se->outputLine(null, $this->getErrorName($error_code).': '.$message);
                $se->outputLine(null, 'Occurred in: '.$file.':'.$line);
        }

        protected static function getErrorName($error_code, $message = '', $file = '', $line = 0)
        {
                $errors = array(
                        E_ERROR             => 'Error',
                        E_WARNING           => 'Warning',
                        E_PARSE             => 'Parse Error',
                        E_NOTICE            => 'Notice',
                        E_COMPILE_ERROR     => 'Compile Error',
                        E_COMPILE_WARNING   => 'Compile Warning',
                        E_USER_ERROR        => 'Error',
                        E_USER_WARNING      => 'Warning',
                        E_USER_NOTICE       => 'Notice',
                        E_STRICT            => 'Strict',
                        E_RECOVERABLE_ERROR => 'Recoverable Error',
                        E_DEPRECATED        => 'Deprecated'
                );
                
                return $errors[$error_code];
        }
}
