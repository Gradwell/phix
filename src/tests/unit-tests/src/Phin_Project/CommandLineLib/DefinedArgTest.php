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

use Phin_Project\ValidationLib\MustBeValidFile;
use Phin_Project\ValidationLib\MustBeValidPath;
use Phin_Project\ValidationLib\MustBeWriteable;

class DefinedArgTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertTrue($obj->testIsOptional());
                $this->assertFalse($obj->testIsRequired());
        }

        public function testCanCreateOptionalArg()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);
                $obj->setIsOptional();

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertTrue($obj->testIsOptional());
                $this->assertFalse($obj->testIsRequired());
        }

        public function testCanCreateRequiredArg()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);
                $obj->setIsRequired();

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertTrue($obj->testIsRequired());
                $this->assertFalse($obj->testIsOptional());
        }

        public function testCanRequireAValidFile()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);
                $obj->setValidator(new MustBeValidFile());

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertTrue($obj->testIsOptional());
                $this->assertTrue($obj->testMustValidateWith('Phin_Project\ValidationLib\MustBeValidFile'));
        }

        public function testCanRequireAValidPath()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);
                $obj->setValidator(new MustBeValidPath());

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertTrue($obj->testIsOptional());
                $this->assertTrue($obj->testMustValidateWith('Phin_Project\ValidationLib\MustBeValidPath'));
        }

        public function testCanRequireWriteableArg()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);
                $obj->setValidator(new MustBeWriteable());

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertTrue($obj->testIsOptional());
                $this->assertTrue($obj->testMustValidateWith('Phin_Project\ValidationLib\MustBeWriteable'));
        }

        public function testCanSetDefaultValueForArg()
        {
                $name = '<command>';
                $desc = 'The <command> you need help with';

                $obj = new DefinedArg($name, $desc);
                $obj->setDefaultValue('help');

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertEquals($desc, $obj->desc);
                $this->assertEquals('help', $obj->defaultValue);
        }
}