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

class DefinedOptions
{
        const FLAG_NONE = 0;
        const FLAG_HASPARAM = 1;
        const FLAG_CANBEDUPLICATED = 2;

        public $shortSwitches = array();
        public $longSwitches = array();
        public $switches = array();
        public $args = array();

        /**
         * Add a switch to the list of allowed switches
         * 
         * @param string $name
         * @param string $desc
         * @return DefinedSwitch 
         */
        public function addSwitch($name, $desc)
        {
                $this->switches[$name] = new DefinedSwitch($name, $desc);
                return $this->switches[$name];
        }

        /**
         * Tell this object that we've finished adding switches, so that
         * it can work out what short and long switches have been defined
         */
        public function allSwitchesAreLoaded()
        {
                foreach ($this->switches as $switch)
                {
                        foreach ($switch->getShortSwitches() as $shortSwitch)
                        {
                                $this->shortSwitches[$shortSwitch] = $switch;
                        }
                        
                        foreach ($switch->getLongSwitches() as $longSwitch)
                        {
                                $this->longSwitches[$longSwitch] = $switch;
                        }
                }
        }

        public function testHasArgByName($argName)
        {
                if (isset($this->args[$argName]))
                {
                        return true;
                }

                return false;
        }

        public function getArgByName($argName)
        {
                if (!isset($this->args[$argName]))
                {
                        throw new Exception('Unknown argument ' . $argName);
                }

                return $this->args[$argName];
        }
        
        public function testHasSwitchByName($switchName)
        {
                if (isset($this->switches[$switchName]))
                {
                        return true;
                }

                return false;
        }
        
        public function testHasShortSwitch($switch)
        {
                if (isset($this->shortSwitches[$switch]))
                {
                        return true;
                }

                return false;
        }

        public function getShortSwitch($switchName)
        {
                if (isset($this->shortSwitches[$switchName]))
                {
                        return $this->shortSwitches[$switchName];
                }

                throw new CommandException("unknown switch $switch");
        }

        public function testHasLongSwitch($switch)
        {
                if (isset($this->longSwitches[$switch]))
                {
                        return true;
                }

                return false;
        }
        public function getLongSwitch($switch)
        {
                if (isset($this->longSwitches[$switch]))
                {
                        return $this->longSwitches[$switch];
                }

                throw new CommandException("unknown switch $switch");
        }

        public function getSwitchByName($name)
        {
                if (isset($this->switches[$name]))
                {
                        return $this->switches[$name];
                }

                throw new CommandException("unknown switch $switch");
        }

        public function testHasListOfLongAndShortSwitches()
        {
                if (count($this->shortSwitches) == 0 && count($this->longSwitches) == 0)
                {
                        return false;
                }

                return true;
        }

        public function getSwitches()
        {
                return $this->switches;
        }
}