<?php

/**
 * Copyright (c) 2011 Gradwell dot com Ltd.
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
 * @subpackage  Phix
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2011 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\Phix;

use Phix_Project\PhixExtensions\CommandBase;
use Phix_Project\PhixExtensions\CommandInterface;

class DummyCommand extends CommandBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'dummy';
        }

        public function getCommandDesc()
        {
                return 'dummy desc';
        }
}

class DummyNotACommand
{

}

class CommandsListTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $obj = new CommandsList();
                $this->assertTrue($obj instanceof CommandsList);
        }

        public function testStartsWithEmptyListOfCommands()
        {
                // setup
                $obj = new CommandsList();

                // do the test
                $commands = $obj->getListOfCommands();

                // did it work?
                $this->assertTrue(is_array($commands));
                $this->assertEquals(0, count($commands));
        }

        public function testCanAddCommandToList()
        {
                // setup
                $obj = new CommandsList();

                // do the test
                $obj->addClass('\Phix_Project\Phix\DummyCommand');

                // did it work?
                $this->assertTrue($obj->testHasCommand('dummy'));
                $commands = $obj->getListOfCommands();
                $this->assertTrue(isset($commands['dummy']));
                $this->assertTrue($commands['dummy'] instanceof DummyCommand);
        }

        public function testCannotAddANonCommandToList()
        {
                // setup
                $obj = new CommandsList();

                // do the test
                $caughtException = false;
                try
                {
                        $obj->addClass('\Phix_Project\Phix\DummyNotACommand');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;                
                }

                $this->assertTrue($caughtException);

                $commands = $obj->getListOfCommands();
                $this->assertTrue(is_array($commands));
                $this->assertEquals(0, count($commands));
        }

        public function testCanCheckToSeeIfCommandInList()
        {
                // setup
                $obj = new CommandsList();
                $obj->addClass('\Phix_Project\Phix\DummyCommand');

                // do the test
                $this->assertTrue($obj->testHasCommand('dummy'));
        }

        public function testCanGetACommandOnceAddedToList()
        {
                // setup
                $obj = new CommandsList();
                $obj->addClass('\Phix_Project\Phix\DummyCommand');
                $this->assertTrue($obj->testHasCommand('dummy'));

                // do the test
                $command = $obj->getCommand('dummy');
                $this->assertTrue($command instanceof DummyCommand);
        }

        public function testThrowsExceptionIfGettingCommandNotInList()
        {
                // setup
                $obj = new CommandsList();
                $caughtException = false;
                $this->assertFalse($obj->testHasCommand('foobar'));

                // test
                try
                {
                        $obj->getCommand('foobar');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }

                // did it work?
                $this->assertTrue($caughtException);
        }
}