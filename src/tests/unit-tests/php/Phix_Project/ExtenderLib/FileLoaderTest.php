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
 * @subpackage  ExtenderLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.Phix-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ExtenderLib;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
        public function testCanLoadPhpFile()
        {
                $fileLoader = new FileLoader();
                $newClasses = $fileLoader->loadPhpFile(__DIR__ . '/DummyClass1.php');

                $this->assertTrue(is_array($newClasses));
                $this->assertEquals(1, count($newClasses));
                $this->assertEquals('Phix_Project\\ExtenderLib\\DummyClass1', $newClasses[0]);
        }

        public function testThrowsExceptionIfFileNotFound()
        {
                $fileLoader = new FileLoader();
                $caughtException = false;
                try
                {
                        $fileLoader->loadPhpFile(__DIR__ . '/BogusClass1.php');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testThrowsExceptionIfFileUnreadable()
        {
                // make sure the file is unreadable
                \chmod(__DIR__ . '/DummyClass3.php', 000);

                $fileLoader = new FileLoader();
                $caughtException = false;
                try
                {
                        $fileLoader->loadPhpFile(__DIR__ . '/DummyClass3.php');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);

                // put the file back to its proper permissions
                \chmod(__DIR__ . '/DummyClass3.php', 0755);
        }

        public function testGlobalVariablesInLoadedFileAppearInGlobalScope()
        {
                $this->assertTrue(!isset($GLOBALS['testVar']));

                // BIG FAT NOTE:
                //
                // We *have* to load a different class here, because DummyClass1
                // has already been loaded, and cannot be loaded again
                $fileLoader = new FileLoader();
                $newClasses = $fileLoader->loadPhpFile(__DIR__ . '/DummyClass2.php');

                $this->assertTrue(isset($GLOBALS['testVar']));
                $this->assertEquals('trout', $GLOBALS['testVar']);
        }

}