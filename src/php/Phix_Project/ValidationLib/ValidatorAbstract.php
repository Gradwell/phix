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
 * @subpackage  ValidationLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.Phix-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib;

/**
 * This is inspired by Zend_Validate_Abstract, but has been put on a
 * much-needed diet :)
 */
abstract class ValidatorAbstract implements Validator
{
        /**
         * The value that the validator has been asked to check
         * @var mixed
         */
        protected $value;

        /**
         * An array of the error messages generated during validation
         * @var array an array of error messages when validation fails
         */
        protected $errorMsgs = array();

        /**
         * Get the array of error messages
         *
         * If validation was successful, the array will be empty, but it
         * will still be an array rather than a boolean
         *
         * @return array
         */
        public function getMessages()
        {
                return $this->errorMsgs;
        }

        public function hasMessages()
        {
                if (count($this->errorMsgs) > 0)
                {
                        return true;
                }

                return false;
        }
        
        protected function _setValue($value)
        {
                $this->value = $value;
                $this->errorMsgs = array();
        }

        protected function _error($msg)
        {
                if (!isset($this->_messageTemplates[$msg]))
                {
                        throw new \Exception("Unknown error message '$msg'");
                }
                
                // work out how to format the error message
                $type = gettype($this->value);

                switch ($type)
                {
                        case 'object':
                                $value = get_class($this->value);
                                break;

                        case 'boolean':
                                if ($this->value)
                                {
                                        $value = 'TRUE';
                                }
                                else
                                {
                                        $value = 'FALSE';
                                }
                                break;

                        case 'integer':
                        case 'float':
                        case 'double':
                        case 'string':
                                $value = $this->value;
                                break;

                        default:
                                $value = '';
                                break;
                }

                $searchList = array (
                        '%value%',
                        '%type%'
                );

                $replaceList = array (
                        $value,
                        $type
                );

                $this->errorMsgs[] = str_replace($searchList, $replaceList, $this->_messageTemplates[$msg]);
        }
}