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
 * @subpackage  PhixHelpers
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixHelpers;

use Phix_Project\Phix\Context;

use Gradwell\CommandLineLib\DefinedSwitch;

class SwitchesHelper
{
        static public function showSwitchSummary(Context $context, $sortedSwitches)
        {
                // makes the rest of the method more readable
                $so = $context->stdout;

                if (count($sortedSwitches['shortSwitchesWithoutArgs']) > 0)
                {
                        $so->output(null, ' [ ');
                        $so->output($context->switchStyle, '-' . implode(' -', $sortedSwitches['shortSwitchesWithoutArgs']));
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
                                $so->output(null, ' ]');
                        }
                }
        }
        
        static public function showSwitchLongDetails(Context $context, DefinedSwitch $switch)
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
}