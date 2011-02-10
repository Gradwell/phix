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

use Phin_Project\ValidationLib\Validator;

class DefinedArg
{
        public $name;
        public $desc;
        public $defaultValue = null;
        public $isRequired = false;

        protected $validators = array();

        /**
         * Define an argument to a switch
         *
         * @param string $argName
         * @param string $desc
         */
        public function __construct($argName, $desc)
        {
                $this->name = $argName;
                $this->desc = $desc;
        }

        /**
         * Define this argument as being optional
         *
         * @return DefinedArg $this
         */
        public function setIsOptional()
        {
                $this->isRequired = false;
                return $this;
        }

        /**
         * Define this argument as being required
         *
         * @return DefinedArg $this
         */
        public function setIsRequired()
        {
                $this->isRequired = true;
                return $this;
        }

        public function setValidator(Validator $validator)
        {
                $this->validators[] = $validator;
                return $this;
        }

        /**
         * Is this argument optional?
         * 
         * @return boolean
         */
        public function testIsOptional()
        {
                if ($this->isRequired == false)
                {
                        return true;
                }
                return false;
        }

        /**
         * Is this argument required?
         *
         * @return boolean
         */
        public function testIsRequired()
        {
                return $this->isRequired;
        }

        /**
         * Does this arg have a specific validator defined?
         * 
         * @param string $validatorName
         * @return boolean
         */
        public function testMustValidateWith($validatorName)
        {
                foreach ($this->validators as $validator)
                {
                        if (get_class($validator) == $validatorName)
                        {
                                return true;
                        }
                }

                return false;
        }

        /**
         * Test a value to see if it is a permitted value for this argument
         *
         * We return an array of error messages. If the value is valid,
         * the returned array will be empty.
         *
         * @param mixed $value
         * @return array
         */
        public function testIsValid($value)
        {
                foreach ($this->validators as $validator)
                {
                        if (!$validator->isValid($value))
                        {
                                // validation failed
                                return $validator->getMessages();
                        }
                }

                // if we get here, then the value is valid
                // we return an empty array
                return array();
        }

        /**
         * Remember the default value for this arg
         * 
         * @param mixed $value
         * @return DefinedArg $this
         */
        public function setDefaultValue($value)
        {
                $this->defaultValue = $value;
                return $this;
        }
}