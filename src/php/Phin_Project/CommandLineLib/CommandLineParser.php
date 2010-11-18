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
 * @subpackage  CommandLineLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\CommandLineLib;

class CommandLineParser
{
        public function parseSwitches($args, DefinedOptions $expectedOptions, $argIndex = 1)
        {
                // make sure $expectedOptions knows what switches have been defined
                $expectedOptions->allSwitchesAreLoaded();

                // create our return values
                $parsedOptions = new ParsedOptions();
                $argCount = count($args);
                
                // let's work through the args from left to right
                while ($argIndex < $argCount)
                {
                        // are we looking at a switch or not?
                        if ($args[$argIndex] == '--')
                        {
                                // var_dump('Special case: --');
                                // special case - end of switches
                                // skip over it
                                $argIndex++;
                                break;
                        }                        
                        else if ($args[$argIndex]{0} !== '-')
                        {
                                // var_dump('Not a switch');
                                // special case - end of switches
                                break;
                        }
                        // yes we are ... parse it
                        // is it a short switch or a long switch?
                        else if ($args[$argIndex]{1} !== '-')
                        {
                                // var_dump('Parsing short switch');
                                // it is a short switch
                                $argIndex = $this->parseShortSwitch($args, $argIndex, $parsedOptions, $expectedOptions);
                        }
                        else
                        {
                                // var_dump('Parsing long switch');
                                // it is a long switch
                                $argIndex = $this->parseLongSwitch($args, $argIndex, $parsedOptions, $expectedOptions);
                        }
                }

                return array($parsedOptions, $argIndex);
        }

        protected function parseShortSwitch($args, $argIndex, ParsedOptions $parsedOptions, DefinedOptions $expectedOptions)
        {
                // $args[$i] contains one or more short switches, which
                // we expect to be defined in $options

                $switchStringLength = strlen($args[$argIndex]);
                for ($j = 1; $j < $switchStringLength; $j++)
                {
                        $shortSwitch = $args[$argIndex]{$j};

                        // is this a valid switch?
                        if (!$expectedOptions->testHasShortSwitch($shortSwitch))
                        {
                                // we didn't expect this switch
                                throw new \Exception("unknown switch " . $shortSwitch);
                        }

                        // yes it is
                        $switch = $expectedOptions->getShortSwitch($shortSwitch);
                        $arg    = null;

                        // should it have an argument?
                        if ($switch->testHasArgument())
                        {
                                // yes, but it may be optional
                                // are we the first switch in this string?
                                if ($j = 1)
                                {
                                        // assume the rest of the string is the argument
                                        if ($j != $switchStringLength - 1)
                                        {
                                                $arg = $this->parseArgument($args, $argIndex, 2, $switch, '-' . $shortSwitch);
                                                // we've finished with this string,
                                                // so set $j to exit the loop
                                                $j = $switchStringLength - 1;
                                        }
                                        else
                                        {
                                                $arg = $this->parseArgument($args, $argIndex + 1, 0, $switch, '-' . $shortSwitch);
                                                if ($arg !== null)
                                                {
                                                        $argIndex++;
                                                }
                                        }
                                }
                                else
                                {
                                        // are we at the end of the list of switches?
                                        if ($j != $switchLength - 1)
                                        {
                                                // no, we are not
                                                // this is invalid behaviour
                                                throw new \Exception('switch ' . $shortSwitch . ' expected argument');
                                        }

                                        $arg = $this->parseArgument($args[$argIndex + 1], 0, $switch, '-' . $shortSwitch);
                                        if ($arg !== null)
                                        {
                                                $argIndex++;
                                        }
                                }
                        }

                        // var_dump("Adding switch " . $switch->name);
                        $parsedOptions->addSwitch($switch, $arg);
                }

                // increment our counter through the args
                $argIndex++;

                // return the counter
                return $argIndex;
        }

        protected function parseLongSwitch($args, $argIndex, ParsedOptions $parsedOptions, DefinedOptions $expectedOptions)
        {
                // $args[i] contains a long switch, and might contain
                // a parameter too
                $equalsPos = strpos($args[$argIndex], '=');
                if ($equalsPos !== false)
                {
                        $longSwitch = substr($args[$argIndex], 2, $equalsPos - 2);
                }
                else
                {
                        $longSwitch = substr($args[$argIndex], 2);
                }
                $arg = null;

                // is this a switch we expected?
                if (!$expectedOptions->testHasLongSwitch($longSwitch))
                {
                        throw new \Exception("unknown switch " . $longSwitch);
                }

                // yes it is
                $switch = $expectedOptions->getLongSwitch($longSwitch);

                // should it have an argument?
                if ($switch->testHasArgument())
                {
                        // did we find one earlier?
                        if ($equalsPos !== false)
                        {
                                // yes we did
                                $arg = $this->parseArgument($args, $argIndex, $equalsPos + 1, $switch, '--' . $longSwitch);
                        }
                        else
                        {
                                // no we did not; it might be next
                                $arg = $this->parseArgument($args, $argIndex + 1, 0, $switch, '--' . $longSwitch);
                                if ($arg !== null)
                                {
                                        $argIndex++;
                                }
                        }
                }

                // increment to the next item in the list
                $argIndex++;

                // remember the switch we've parsed
                $parsedOptions->addSwitch($switch, $arg);

                // all done
                return $argIndex;
        }

        protected function parseArgument($args, $argIndex, $startFrom, DefinedSwitch $switch, $switchSeen)
        {
                // initialise the return value
                $arg = null;

                // is the argument optional or required?
                if ($switch->testHasOptionalArgument())
                {
                        // it is optional ... but is
                        // it there?
                        if (isset($args[$argIndex]))
                        {
                                // yes it is
                                $arg = substr($args[$argIndex], $startFrom);
                        }
                }
                else
                {
                        // argument is required ... but
                        // is it there?
                        if (!isset($args[$argIndex]))
                        {
                                // no it is not
                                // error!
                                throw new \Exception('switch ' . $switchSeen . ' expected argument');
                        }

                        // yes it is
                        $arg = substr($args[$argIndex], $startFrom);
                }

                // did we get an argument?
                if (strlen(trim($arg)) == 0)
                {
                        // no, we did not
                        throw new \Exception('switch ' . $switchSeen . ' expected argument');
                }

                return $arg;
        }
}