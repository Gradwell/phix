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

class DefinedArg
{
        const FLAG_NONE = 0;
        const FLAG_MUSTBEVALIDFILE    = 1;
        const FLAG_MUSTBEWRITEABLE    = 2;
        const FLAG_MUSTBEVALIDPATH    = 4;
        const FLAG_MUSTBEVALIDCOMMAND = 8;
        const FLAG_ISREQUIRED         = 16;
        const FLAG_MUSTBEVALIDNAMESPACE = 32;

        public $name;
        public $desc;
        public $flags = 0;
        public $defaultValue = null;

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
                if ($this->flags & self::FLAG_ISREQUIRED)
                {
                        $this->flags ^= self::FLAG_ISREQUIRED;
                }
                return $this;
        }

        /**
         * Define this argument as being required
         *
         * @return DefinedArg $this
         */
        public function setIsRequired()
        {
                $this->flags |= self::FLAG_ISREQUIRED;
                return $this;
        }

        /**
         * Define that this argument must be a valid command
         *
         * @return DefinedArg $this
         */
        public function setMustBeValidCommand()
        {
                $this->flags |= self::FLAG_MUSTBEVALIDCOMMAND;
                return $this;
        }

        /**
         * Define that this argument must be a valid file on disk
         *
         * @return DefinedArg $this
         */
        public function setMustBeValidFile()
        {
                $this->flags |= self::FLAG_MUSTBEVALIDFILE;
                return $this;
        }

        /**
         * Define that this argument must be a valid PHP namespace
         *
         * @return DefinedArg $this
         */
        public function setMustBeValidNamespace()
        {
                $this->flags |= self::FLAG_MUSTBEVALIDNAMESPACE;
                return $this;
        }

        /**
         * Define that this argument must be a valid folder on disk
         *
         * @return DefinedArg $this
         */
        public function setMustBeValidPath()
        {
                $this->flags |= self::FLAG_MUSTBEVALIDPATH;
                return $this;
        }

        /**
         * Define that this argument must be writeable (combine with
         * $this->setMustBeValidFile() or $this->setMustBeValidPath())
         *
         * @return DefinedArg $this
         */
        public function setMustBeWriteable()
        {
                $this->flags |= self::FLAG_MUSTBEWRITEABLE;
                return $this;
        }

        /**
         * Is this argument optional?
         * 
         * @return boolean
         */
        public function testIsOptional()
        {
                if ($this->flags & self::FLAG_ISREQUIRED)
                {
                        return false;
                }

                return true;
        }

        /**
         * Is this argument required?
         *
         * @return boolean
         */
        public function testIsRequired()
        {
                if ($this->flags & self::FLAG_ISREQUIRED)
                {
                        return true;
                }

                return false;
        }

        /**
         * Must this argument be a valid command?
         *
         * @return boolean
         */
        public function testMustBeValidCommand()
        {
                if ($this->flags & self::FLAG_MUSTBEVALIDCOMMAND)
                {
                        return true;
                }

                return false;
        }

        /**
         * Must this argument be a valid file?
         *
         * @return boolean
         */
        public function testMustBeValidFile()
        {
                if ($this->flags & self::FLAG_MUSTBEVALIDFILE)
                {
                        return true;
                }

                return false;
        }

        /**
         * Must this argument be a valid namespace?
         *
         * @return boolean
         */
        public function testMustBeValidNamespace()
        {
                if ($this->flags & self::FLAG_MUSTBEVALIDNAMESPACE)
                {
                        return true;
                }

                return false;
        }

        /**
         * Must this argument be a valid path?
         *
         * @return boolean
         */
        public function testMustBeValidPath()
        {
                if ($this->flags & self::FLAG_MUSTBEVALIDPATH)
                {
                        return true;
                }

                return false;
        }

        /**
         * Must this argument be writeable?
         *
         * @return boolean
         */
        public function testMustBeWriteable()
        {
                if ($this->flags & self::FLAG_MUSTBEWRITEABLE)
                {
                        return true;
                }

                return false;
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