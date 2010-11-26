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
 * @package     Phin_Project
 * @subpackage  PhinCommands
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\PhinCommands;

use Phin_Project\Phin\CommandsList;
use Phin_Project\Phin\Context;
use Phin_Project\PhinExtensions\CommandBase;
use Phin_Project\CommandLineLib\DefinedOptions;
use Phin_Project\CommandLineLib\DefinedSwitch;

class HelpCommand extends CommandBase
{
        public function getCommandName()
        {
                return 'help';
        }

        public function getCommandDesc()
        {
                return 'get detailed help about a specific phin command';
        }

        public function parseAndValidate($args, $argsIndex, Context $context)
        {
                // $argsIndex points to the first of our arguments
                // ... if it is set at all
                if (!isset($args[$argsIndex]))
                {
                        // show the general help, and bail
                        $this->showGeneralHelp($context);
                        return 0;
                }
                $commandForHelp = $args[$argsIndex];
        }

        protected function showGeneralHelp(Context $context)
        {
                // get the list of switches in display order
                $sortedSwitches = $this->calculatePhinSwitchDisplayOrder($context);

                $so = $context->stdout;

                $so->output($context->highlightStyle, "phin @@PACKAGE_VERSION@@;");
                $so->outputLine($context->urlStyle, ' http://www.phin-tool.org');
                $so->outputLine(null, 'Copyright (c) 2010 Gradwell dot com Ltd. Released under the BSD license');
                $so->outputBlankLine();
                $so->outputLine(null, 'SYNOPSIS');
                $so->setIndent(4);
                $so->output($context->commandStyle, 'phin');
                $this->showPhinSwitchSummary($context, $sortedSwitches);
                $so->outputLine(null, ' [ command ] [ command-options ]');
                $so->outputBlankLine();
                $so->setIndent(0);
                $so->outputLine(null, 'OPTIONS');
                $so->outputBlankLine();
                $so->setIndent(4);
                $this->showPhinSwitchDetails($context, $sortedSwitches);
                $this->showCommands($context);
        }

        protected function calculatePhinSwitchDisplayOrder(Context $context)
        {
                // turn the list into something that's suitably sorted
                $shortSwitchesWithoutArgs = array();
                $shortSwitchesWithArgs = array();
                $longSwitchesWithoutArgs = array();
                $longSwitchesWithArgs = array();

                $allShortSwitches = array();
                $allLongSwitches = array();

                $allSwitches = $context->phinDefinedOptions->getSwitches();

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

                // we have all the switches that phin supports
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

        protected function showPhinSwitchSummary(Context $context, $sortedSwitches)
        {
                $so = $context->stdout;

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
        }

        protected function showPhinSwitchDetails(Context $context, $sortedSwitches)
        {
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

        protected function showCommands(Context $context)
        {
                
        }
}