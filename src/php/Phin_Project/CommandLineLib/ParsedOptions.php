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
        /**
         *
         * @var array
         */
        protected $switchesByName  = array();

        /**
         *
         * @var array
         */
	protected $switchesByOrder = array();

        public function addSwitch(DefinedOptions $expectedOptions, $name, $arg = true)
        {
                $this->requireValidExpectedSwitchName($expectedOptions, $name);
                $this->addSwitchByName($expectedOptions, $name, $arg);
                $this->addSwitchByOrder($expectedOptions, $name, $arg);
        }

        protected function addSwitchByName(DefinedOptions $expectedOptions, $name, $arg, $isDefaultValue = false)
        {
                if (!isset($this->switchesByName[$name]))
                {
                        $this->switchesByName[$name] = new ParsedSwitch($expectedOptions->getSwitchByName($name));
                }
                $this->switchesByName[$name]->addToInvokeCount();
                $this->switchesByName[$name]->addValue($arg);

                if ($isDefaultValue)
                {
                        $this->switchesByName[$name]->setIsUsingDefaultValue();
                }
        }

        protected function addSwitchByOrder(DefinedOptions $expectedOptions, $name, $arg, $isDefaultValue = false)
        {
                $parsedOption = new ParsedSwitch($expectedOptions->getSwitchByName($name));
                $parsedOption->addToInvokeCount();
                $parsedOption->addValue($arg);
                if ($isDefaultValue)
                {
                        $parsedOption->setIsUsingDefaultValue();
                }

		$this->switchesByOrder[] = $parsedOption;
        }

        public function addDefaultValue(DefinedOptions $expectedOptions, $switchName, $value)
        {
                $this->requireValidExpectedSwitchName($expectedOptions, $switchName);
                // has this switch already been invoked by the user?
                if (isset($this->switchesByName[$switchName]))
                {
                        // yes
                        return;
                }
                $this->addSwitchByName($expectedOptions, $switchName, $value, true);
                $this->addSwitchByOrder($expectedOptions, $switchName, $value, true);
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

        public function getSwitchesByOrder()
        {
                return $this->switchesByOrder;
        }

        public function getArgsForSwitch($switchName)
        {
                $this->requireValidSwitchName($switchName);
                return $this->switchesByName[$switchName]->values;
        }

        public function getFirstArgForSwitch($switchName)
        {
                $this->requireValidSwitchName($switchName);
                return $this->switchesByName[$switchName]->values[0];
        }

        public function getInvokeCountForSwitch($switchName)
        {
                $this->requireValidSwitchName($switchName);
                return $this->switchesByName[$switchName]->invokes;
        }

        public function validateSwitchValues()
        {
                $return = array();

                // loop over the switches
                foreach ($this->switchesByName as $name => $switch)
                {
                        $validationErrors = $switch->validateValues();
                        $return = array_merge($return, $validationErrors);
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

        protected function requireValidExpectedSwitchName(DefinedOptions $expectedOptions, $switchName)
        {
                if (!$expectedOptions->testHasSwitchByName($switchName))
                {
                        throw new \Exception("Unknown switch name " . $switchName);
                }
        }
}
