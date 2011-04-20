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

class ClassFinderTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $obj = new ClassFinder();
                $this->assertTrue($obj instanceof ClassFinder);
        }

        public function testByDefaultStartsWithEmptySearchList()
        {
                $obj = new ClassFinder();
                $searchFolders = $obj->getFoldersToSearch();

                $this->assertTrue(is_array($searchFolders));
                $this->assertEquals(0, count($searchFolders));
        }

        public function testCanAddFolderToSearchList()
        {
                $obj = new ClassFinder();
                $folderToSearch = realpath(__DIR__ . '/../..');
                $obj->addFolderToSearch($folderToSearch);

                // has it been added?
                $searchFolders = $obj->getFoldersToSearch();
                $this->assertTrue(is_array($searchFolders));
                $this->assertEquals(1, count($searchFolders));
                $this->assertTrue(isset($searchFolders[$folderToSearch]));
                $this->assertEquals($folderToSearch, $searchFolders[$folderToSearch]);
        }

        public function testThrowsExceptionIfTryingToSearchFolderThatDoesNotExist()
        {
                $obj = new ClassFinder();
                $caughtException = false;

                try
                {
                        $obj->addFolderToSearch('/foo/bar//never');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }

                $this->assertTrue($caughtException);
        }

        public function testSearchesForClasses()
        {
                $obj = new ClassFinder();
                $folderToSearch = realpath(__DIR__ . '/../..');
                $obj->addFolderToSearch($folderToSearch);

                $classes = $obj->findClassFiles();
                $expectedClasses = array(
                        'Phix_Project\PhixCommands\HelpCommandTest'     => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixCommands' . DIRECTORY_SEPARATOR . 'HelpCommandTest.php',
                        'Phix_Project\PhixExtensions\CommandBaseTest'   => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixExtensions' . DIRECTORY_SEPARATOR . 'CommandBaseTest.php',
                        'Phix_Project\PhixExtensions\DummyCommand'      => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixExtensions' . DIRECTORY_SEPARATOR . 'CommandBaseTest.php',
                        'Phix_Project\PhixExtensions\DummyCommandWithNoSwitches' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixExtensions' . DIRECTORY_SEPARATOR . 'CommandBaseTest.php',
                        'Phix_Project\PhixExtensions\DummyCommandWithNoSwitchesAndOneArg' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixExtensions' . DIRECTORY_SEPARATOR . 'CommandBaseTest.php',
                        'Phix_Project\PhixExtensions\DummyCommandWithSwitches' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixExtensions' . DIRECTORY_SEPARATOR . 'CommandBaseTest.php',
                        'Phix_Project\PhixSwitches\DebugSwitchTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'DebugSwitchTest.php',
                        'Phix_Project\PhixSwitches\IncludeSwitchTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'IncludeSwitchTest.php',
                        'Phix_Project\PhixSwitches\LongHelpSwitchTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'LongHelpSwitchTest.php',
                        'Phix_Project\PhixSwitches\PhixSwitchesTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'PhixSwitchesTest.php',
                        'Phix_Project\PhixSwitches\ShortHelpSwitchTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'ShortHelpSwitchTest.php',
                        'Phix_Project\PhixSwitches\SwitchBaseTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'SwitchBaseTest.php',
                        'Phix_Project\PhixSwitches\VersionSwitchTest' => $folderToSearch . DIRECTORY_SEPARATOR . 'Phix_Project' . DIRECTORY_SEPARATOR . 'PhixSwitches' . DIRECTORY_SEPARATOR . 'VersionSwitchTest.php',
                        'Phix_Project\Phix\ClassFinderTest'     => __DIR__ . DIRECTORY_SEPARATOR . 'ClassFinderTest.php',
                        'Phix_Project\Phix\CommandsFinderTest'  => __DIR__ . DIRECTORY_SEPARATOR . 'CommandsFinderTest.php',
                        'Phix_Project\Phix\CommandsListTest'    => __DIR__ . DIRECTORY_SEPARATOR . 'CommandsListTest.php',
                        'Phix_Project\Phix\ContextTest'         => __DIR__ . DIRECTORY_SEPARATOR . 'ContextTest.php',
                        'Phix_Project\Phix\DummyCommand'        => __DIR__ . DIRECTORY_SEPARATOR . 'CommandsListTest.php',
                        'Phix_Project\Phix\DummyNotACommand'    => __DIR__ . DIRECTORY_SEPARATOR . 'CommandsListTest.php',
                );

                $this->assertEquals($expectedClasses, $classes);
        }
}