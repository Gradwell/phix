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

class ParsedOptions
{
        protected $switchesByName = array();
        protected $argsForSwitches = array();
        protected $invokeCount = array();

        public function addSwitch(DefinedSwitch $switch, $arg = null)
        {
                // store the switch if we haven't seen it before
                if (!isset($this->switchesByName[$switch->name]))
                {
                        $this->switchesByName[$switch->name] = $switch;
                        $this->argsForSwitches[$switch->name] = array();
                        $this->invokeCount[$switch->name] = 0;
                }

                // keep track of how many times this switch has
                // been used on the command line
                $this->invokeCount[$switch->name]++;

                // keep track of the arguments passed in for
                // this switch
                $this->argsForSwitches[$switch->name][] = $arg;
        }

        public function testHasSwitch($switchName)
        {
                if (isset($this->switchesByName[$switchName]))
                {
                        return true;
                }

                return false;
        }

        public function getSwitches()
        {
                return $this->switchesByName;
        }

        public function getSwitch($switchName)
        {
                $this->requireValidSwitchName($switchName);
                return $this->switchesByName[$switchName];
        }

        public function getArgsForSwitch($switchName)
        {
                $this->requireValidSwitchName($switchName);
                return $this->argsForSwitches[$switchName];
        }

        public function getInvokeCountForSwitch($switchName)
        {
                $this->requireValidSwitchName($switchName);
                return $this->invokeCount[$switchName];
        }

        public function getMergedSwitchValues($defaults)
        {
                $return = array();

                foreach ($defaults as $name => $defaultValue)
                {
                        // have we seen this switch?
                        if ($this->testHasSwitch($name))
                        {
                                $return[$name] = $this->getArgsForSwitch($name);
                        }
                        else
                        {
                                $return[$name][] = $defaultValue;
                        }
                }

                return $return;
        }

        protected function requireValidSwitchName($switchName)
        {
                if (!$this->testHasSwitch($switchName))
                {
                        throw new \Exception("Unknown switch name " . $switchName);
                }
        }
}