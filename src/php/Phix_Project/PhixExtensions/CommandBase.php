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
 * @subpackage  PhixExtensions
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixExtensions;
use Phix_Project\Phix\Context;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\CommandLineLib\DefinedSwitch;

class CommandBase
{
        public function getCommandName()
        {
                throw new \Exception(__METHOD__ . '() not implemented in ' . get_class($this));
        }

        public function getCommandDesc()
        {
                throw new \Exception( __METHOD__ . '() not implemented in ' . get_class($this));
        }

        public function getValidOptions()
        {
                throw new \Exception( __METHOD__ . '() not implemented in ' . get_class($this));
        }

        public function getCommandOptions()
        {
                return null;
        }

        public function getCommandArgs()
        {
                return array();
        }

        /**
         * Check to make sure that the switches and args are valid
         * 
         * @param array $parsedSwitches
         * @param array $parsedArgs
         * @param DefinedSwitches $validOptions
         */
        public function validateAndExecute($argv, $argsIndex, Context $context)
        {
                throw new \Exception(__METHOD__ . '() not implemented');
        }

        public function outputHelp(Context $context)
        {
                $so = $context->stdout;

                $options = $this->getCommandOptions();
                $args    = $this->getCommandArgs();
                
                $sortedSwitches = null;
                if ($options !== null)
                {
                        $sortedSwitches = $this->calculateSwitchDisplayOrder($options);
                }

                $this->showName($context);
                $this->showSynopsis($context, $sortedSwitches, $args);
                $this->showOptions($context, $sortedSwitches, $args);
                $this->showImplementationDetails($context);
        }

        protected function calculateSwitchDisplayOrder(DefinedSwitches $options)
        {
                // turn the list into something that's suitably sorted
                $shortSwitchesWithoutArgs = array();
                $shortSwitchesWithArgs = array();
                $longSwitchesWithoutArgs = array();
                $longSwitchesWithArgs = array();

                $allShortSwitches = array();
                $allLongSwitches = array();

                $allSwitches = $options->getSwitches();

                foreach ($allSwitches as $switch)
                {
                        foreach ($switch->shortSwitches as $shortSwitch)
                        {
                                $allShortSwitches['-' . $shortSwitch] = $switch;

                                if ($switch->testHasArgument())
                                {
                                        $shortSwitchesWithArgs[$shortSwitch] = $switch;
                                }
                                else
                                {
                                        $shortSwitchesWithoutArgs[$shortSwitch] = $shortSwitch;
                                }
                        }

                        foreach ($switch->longSwitches as $longSwitch)
                        {
                                $allLongSwitches['--' . $longSwitch] = $switch;

                                if ($switch->testHasArgument())
                                {
                                        $longSwitchesWithArgs[$longSwitch] = $switch;
                                }
                                else
                                {
                                        $longSwitchesWithoutArgs[$longSwitch] = $longSwitch;
                                }
                        }
                }

                // we have all the switches that phix supports
                // let's put them into sensible orders, and then display
                // them
                \ksort($shortSwitchesWithArgs);
                \ksort($shortSwitchesWithoutArgs);
                \ksort($longSwitchesWithArgs);
                \ksort($longSwitchesWithoutArgs);
                \ksort($allShortSwitches);
                \ksort($allLongSwitches);

                $return = array (
                        'shortSwitchesWithArgs' => $shortSwitchesWithArgs,
                        'shortSwitchesWithoutArgs' => $shortSwitchesWithoutArgs,
                        'longSwitchesWithArgs' => $longSwitchesWithArgs,
                        'longSwitchesWithoutArgs' => $longSwitchesWithoutArgs,
                        'allSwitches' => array_merge($allShortSwitches, $allLongSwitches),
                );

                return $return;
        }

        protected function showName(Context $context)
        {
                $so = $context->stdout;

                $so->outputLine(null, 'NAME');
                $so->setIndent(4);
                $so->output($context->commandStyle, $context->argvZero . ' ' . $this->getCommandName());
                $so->outputLine(null, ' - ' . $this->getCommandDesc());
                $so->addIndent(-4);
                $so->outputBlankLine();
        }

        protected function showSynopsis(Context $context, $sortedSwitches, $args)
        {
                $so = $context->stdout;

                $so->outputLine(null, 'SYNOPSIS');
                $so->setIndent(4);

                $so->output($context->commandStyle, $context->argvZero . ' ' . $this->getCommandName());

                if ($sortedSwitches !== null)
                {
                        $this->showSwitchSummary($context, $sortedSwitches);
                }

                if (count($args) > 0)
                {
                        $this->showArgsSummary($context, $args);
                }

                $so->outputBlankLine();
        }

        protected function showArgsSummary(Context $context, $args)
        {
                $so = $context->stdout;

                foreach ($args as $arg => $argDesc)
                {
                        $so->output($context->argStyle, ' ' . $arg);
                }
        }

        protected function showSwitchSummary(Context $context, $sortedSwitches)
        {
                $so = $context->stdout;

                if (count($sortedSwitches['shortSwitchesWithoutArgs']) > 0)
                {
                        $so->output(null, ' [ ');
                        $so->output($context->switchStyle, implode(' -', $sortedSwitches['shortSwitchesWithoutArgs']));
                        $so->output(null, ' ]');
                }

                if (count($sortedSwitches['longSwitchesWithoutArgs']) > 0)
                {
                        $so->output(null, ' [ ');
                        $so->output($context->switchStyle, implode(' --', $sortedSwitches['longSwitchesWithoutArgs']));
                        $so->output(null, ' ]');
                }

                if (count($sortedSwitches['shortSwitchesWithArgs']) > 0)
                {
                        foreach ($sortedSwitches['shortSwitchesWithArgs'] as $shortSwitch => $switch)
                        {
                                $so->output(null, ' [ ');
                                $so->output($context->switchStyle, '-' . $shortSwitch . ' ');
                                $so->output($context->argStyle, $switch->arg->name);
                                $so->output(null, ' ]');
                        }
                }

                if (count($sortedSwitches['longSwitchesWithArgs']) > 0)
                {
                        foreach ($sortedSwitches['longSwitchesWithArgs'] as $longSwitch => $switch)
                        {
                                $so->output(null, ' [ ');
                                if ($switch->testHasArgument())
                                {
                                        $so->output($context->switchStyle, '--' . $longSwitch . '=');
                                        $so->output($context->argStyle, $switch->arg->name);
                                }
                                else
                                {
                                        $so->output($context->switchStyle, '--' . $longSwitch);
                                }
                                $so->output(null, ' ]');
                        }
                }
        }

        protected function showOptions(Context $context, $sortedSwitches, $args)
        {
                $so = $context->stdout;

                $so->setIndent(0);
                $so->outputLine(null, 'OPTIONS');
                $so->addIndent(4);

                if ($sortedSwitches !== null)
                {
                        $this->showSwitchDetails($context, $sortedSwitches);
                }

                if (count($args) > 0)
                {
                        $this->showArgsDetails($context, $args);
                }

                $so->addIndent(-4);
        }

        protected function showSwitchDetails(Context $context, $sortedSwitches)
        {
                $so = $context->stdout;

                // keep track of the switches we have seen, to avoid
                // any duplication of output
                $seenSwitches = array();

                foreach ($sortedSwitches['allSwitches'] as $shortOrLongSwitch => $switch)
                {
                       // have we already seen this switch?
                        if (isset($seenSwitches[$switch->name]))
                        {
                                // yes, skip it
                                continue;
                        }
                        $seenSwitches[$switch->name] = $switch;

                        // we have not seen this switch before
                        $this->showSwitchLongDetails($context, $switch);
                }
        }

        protected function showArgsDetails(Context $context, $args)
        {
                $so = $context->stdout;
                
                foreach ($args as $argName => $argDesc)
                {
                        $this->showArgLongDetails($context, $argName, $argDesc);
                }
        }

        protected function showSwitchLongDetails(Context $context, DefinedSwitch $switch)
        {
                $so = $context->stdout;

                $shortOrLongSwitches = $switch->getHumanReadableSwitchList();
                $append = false;

                foreach ($shortOrLongSwitches as $shortOrLongSwitch)
                {
                        if ($append)
                        {
                                $so->output(null, ' | ');
                        }
                        $append = true;

                        $so->output($context->switchStyle, $shortOrLongSwitch);

                        // is there an argument?
                        if ($switch->testHasArgument())
                        {
                                if ($shortOrLongSwitch{1} == '-')
                                {
                                        $so->output(null, '=');
                                }
                                else
                                {
                                        $so->output(null, ' ');
                                }
                                $so->output($context->argStyle, $switch->arg->name);
                        }
                }

                $so->outputLine(null, '');
                $so->addIndent(4);
                $so->outputLine(null, $switch->desc);
                if (isset($switch->longdesc))
                {
                        $so->outputBlankLine();
                        $so->outputLine(null, $switch->longdesc);
                }

                // output the default argument, if it is set
                if ($switch->testHasArgument() && isset($switch->arg->defaultValue))
                {
                        $so->outputBlankLine();
                        $so->output(null, 'The default value for ');
                        $so->output($context->argStyle, $switch->arg->name);
                        $so->output(null, ' is: ');
                        $so->outputLine($context->exampleStyle, $switch->arg->defaultValue);
                }

                $so->addIndent(-4);
                $so->outputBlankLine();
        }

        protected function showArgLongDetails(Context $context, $argName, $argDesc)
        {
                $so = $context->stdout;

                $so->outputLine($context->argStyle, $argName);
                $so->addIndent(4);
                $so->outputLine(null, $argDesc);
                $so->addIndent(-4);
                $so->outputBlankLine();
        }

        protected function showImplementationDetails(Context $context)
        {
                $so = $context->stdout;

                $so->outputLine(null, 'IMPLEMENTATION');
                $so->addIndent(4);
                $so->outputLine(null, 'This command is implemented in the PHP class:');
                $so->outputBlankLine();
                $so->output($context->commandStyle, '* ');
                $so->addIndent(2);
                $so->outputLine(null, get_class($this));
                $so->addIndent(-2);
                $so->outputBlankLine();
                $so->outputLine(null, 'which is defined in the file:');
                $so->outputBlankLine();
                $so->output($context->commandStyle, '* ');
                $so->addIndent(2);
                $so->outputLine(null, $this->getSourceFilename());
                $so->addIndent(-6);
        }

        protected function getSourceFilename()
        {
                $reflect = new \ReflectionClass($this);
                return $reflect->getFileName();
        }
}