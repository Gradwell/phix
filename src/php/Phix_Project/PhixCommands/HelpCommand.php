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
 * @subpackage  PhixCommands
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixCommands;

use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Phix_Project\PhixExtensions\CommandBase;
use Phix_Project\PhixExtensions\CommandInterface;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\CommandLineLib\DefinedSwitch;

if (!\class_exists('Phix_Project\PhixCommands\HelpCommand'))
{
class HelpCommand extends CommandBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'help';
        }

        public function getCommandDesc()
        {
                return 'get detailed help about a specific phix command';
        }

        public function validateAndExecute($args, $argsIndex, Context $context)
        {
                // $argsIndex points to the first of our arguments
                // ... if it is set at all
                if (!isset($args[$argsIndex]))
                {
                        // show the general help, and bail
                        $this->showGeneralHelp($context);
                        return 0;
                }

                // if we get here, the user wants detailed help with a
                // specific command
                $commandForHelp = $args[$argsIndex];
                $se = $context->stderr;

                // is this a valid command?
                if (!$context->commandsList->testHasCommand($commandForHelp))
                {
                        $se->output($context->errorStyle, $context->errorPrefix);
                        $se->outputLine(null, ' unknown command ' . $commandForHelp);
                        $se->output(null, 'use ');
                        $se->output($context->commandStyle, 'phix --help');
                        $se->outputLine(null, ' for a list of all available commands');
                        return 1;
                }

                // we have a command to show the details of
                $so = $context->stdout;

                $command = $context->commandsList->getCommand($commandForHelp);
                $command->outputHelp($context);


                return 0;
        }

        protected function showGeneralHelp(Context $context)
        {
                // get the list of switches in display order
                $sortedSwitches = $this->calculatePhixSwitchDisplayOrder($context);

                $so = $context->stdout;

                $so->output($context->highlightStyle, "phix " . $context->version);
                $so->outputLine($context->urlStyle, ' http://gradwell.github.com');
                $so->outputLine(null, 'Copyright (c) 2010 Gradwell dot com Ltd. Released under the BSD license');
                $so->outputBlankLine();
                $this->showPhixSwitchSummary($context, $sortedSwitches);
                $this->showPhixSwitchDetails($context, $sortedSwitches);
                $this->showCommandsList($context);
        }

        protected function calculatePhixSwitchDisplayOrder(Context $context)
        {
                // turn the list into something that's suitably sorted
                $shortSwitchesWithoutArgs = array();
                $shortSwitchesWithArgs = array();
                $longSwitchesWithoutArgs = array();
                $longSwitchesWithArgs = array();

                $allShortSwitches = array();
                $allLongSwitches = array();

                $allSwitches = $context->phixDefinedSwitches->getSwitches();

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

        protected function showPhixSwitchSummary(Context $context, $sortedSwitches)
        {
                $so = $context->stdout;

                $so->outputLine(null, 'SYNOPSIS');
                $so->setIndent(4);
                $so->output($context->commandStyle, $context->argvZero);

                if (count($sortedSwitches['shortSwitchesWithoutArgs']) > 0)
                {
                        $so->output(null, ' [ ');
                        $so->output($context->switchStyle, '-' . implode('', $sortedSwitches['shortSwitchesWithoutArgs']));
                        $so->output(null, ' ]');
                }

                if (count($sortedSwitches['longSwitchesWithoutArgs']) > 0)
                {
                        $so->output(null, ' [ ');
                        $so->output($context->switchStyle, '--' . implode(' --', $sortedSwitches['longSwitchesWithoutArgs']));
                        $so->output(null, ' ]');
                }
                
                if (count($sortedSwitches['shortSwitchesWithArgs']) > 0)
                {
                        foreach ($sortedSwitches['shortSwitchesWithArgs'] as $shortSwitch => $switch)
                        {
                                $so->output(null, ' [ ');
                                $so->output($context->switchStyle, '-' . $shortSwitch);
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
                
                $so->outputLine(null, ' [ command ] [ command-options ]');
                $so->outputBlankLine();
        }

        protected function showPhixSwitchDetails(Context $context, $sortedSwitches)
        {
                $so = $context->stdout;
                
                $so->setIndent(0);
                $so->outputLine(null, 'OPTIONS');
                $so->addIndent(4);
                $so->outputLine(null, 'Use the following switches in front of any <command> to have the following effects.');
                $so->outputBlankLine();
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
                $so->addIndent(-4);
                $so->outputBlankLine();
        }

        protected function showCommandsList(Context $context)
        {
                $so = $context->stdout;

                $so->setIndent(0);
                $so->outputLine(null, 'COMMANDS');
                $so->addIndent(4);

                $sortedCommands = $context->commandsList->getListOfCommands();
                \ksort($sortedCommands);

                // work out our longest command name length
                $maxlen = 0;
                foreach ($sortedCommands as $commandName => $command)
                {
                        if (strlen($commandName) > $maxlen)
                        {
                                $maxlen = strlen($commandName);
                        }
                }

                foreach ($sortedCommands as $commandName => $command)
                {
                        $so->output($context->commandStyle, $commandName);
                        $so->addIndent($maxlen + 1);
                        $so->output($context->commentStyle, '# ');
                        $so->addIndent(2);
                        $so->outputLine(null, $command->getCommandDesc());
                        $so->addIndent(0 - $maxlen - 3);
                }

                $so->outputBlankLine();
                $so->output(null, 'See ');
                $so->output($context->commandStyle, $context->argvZero . ' help <command>');
                $so->outputLine(null, ' for detailed help on <command>');
        }

        protected function outputHelp(Context $context)
        {
                $so = $context->stdout;

                $so->outputLine($context->commandStyle, $context->argvZero . ' ' . $this->getCommandName());
                $so->addIndent(4);
                $so->outputLine(null, "Show this help message");
                $this->outputImplementationDetails($context);
                $so->addIndent(-4);
                $so->outputBlankLine();

                $so->output($context->commandStyle, $context->argvZero . ' ' . $this->getCommandName());
                $so->outputLine($context->argStyle, ' <command>');
                $so->addIndent(4);
                $so->outputLine(null, "Show detailed help for <command>");
                $this->outputImplementationDetails($context);
                $so->addIndent(-4);
        }
}
}