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
 * @subpackage  ValidationLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\ValidationLib;

class MustBeWriteableTest extends ValidationLibTestBase
{
        /**
         *
         * @return MustBeWriteable
         */
        protected function setUp()
        {
                // setup the test
                $obj = new MustBeWriteable();
                $messages = $obj->getMessages();
                $this->assertTrue(is_array($messages));
                $this->assertEquals(0, count($messages));

                $this->obj = $obj;
                
                // create the files and folders that we need
                $this->rwfile = $this->tempname();
                \file_put_contents($this->rwfile, 'test file');
                \chmod($this->rwfile, 0777);
                
                $this->rofile = $this->tempname();
                \file_put_contents($this->rofile, 'test file');
                \chmod($this->rofile, 0444);

                $this->nrfile = $this->tempname();
                \file_put_contents($this->nrfile, 'test file');
                \chmod($this->nrfile, 0);
                
                $this->rwdir = $this->tempname();
                \unlink($this->rwdir);
                \mkdir($this->rwdir, 0777);
                
                $this->rodir = $this->tempname();
                \unlink($this->rodir);
                \mkdir($this->rodir, 0444);
                
                $this->nrdir = $this->tempname();
                \unlink($this->nrdir);
                \mkdir($this->nrdir, 0);
        }

        protected function tearDown()
        {
                $toUnlink = array
                (
                        $this->rwfile,
                        $this->rofile,
                        $this->nrfile,
                        $this->rwdir,
                        $this->rodir,
                        $this->nrdir
                );

                foreach ($toUnlink as $unlinkMe)
                {
                        \chmod($unlinkMe, 0777);
                        if (is_file($unlinkMe))
                        {
                                \unlink($unlinkMe);
                        }
                        else
                        {
                                \rmdir($unlinkMe);
                        }
                }
        }

        protected function tempname()
        {
                if (is_dir('/tmp'))
                {
                        $file = tempnam('/tmp', 'tst');
                }
                else
                {
                        $file = tempnam(__DIR__, 'tst');
                }

                return $file;
        }

        public function testCorrectlyDetectsWriteableFile()
        {
                // check the file
                $this->doTestIsValid($this->obj, $this->rwfile);
        }

        public function testCorrectlyDetectsReadonlyFile()
        {
                $this->doTestIsNotValid($this->obj, $this->rofile, array("'$this->rofile' exists, but is not writeable"));
        }

        public function testCorrectlyDetectsUnreadableFile()
        {
                $this->doTestIsNotValid($this->obj, $this->nrfile, array("'$this->nrfile' exists, but is not writeable"));
        }

        public function testCorrectlyDetectsMissingFile()
        {
                $file = $this->tempname();
                \unlink($file);
                $this->doTestIsNotValid($this->obj, $file, array("'$file' does not exist; file or directory expected"));
        }

        public function testCorrectlyDetectsWriteableDirectory()
        {
                $this->doTestIsValid($this->obj, $this->rwdir);
        }

        public function testCorrectlyDetectsReadonlyDirectory()
        {
                $this->doTestIsNotValid($this->obj, $this->rodir, array("'$this->rodir' exists, but is not writeable"));
        }

        public function testCorrectlyDetectsUnreadableDirectory()
        {
                $this->doTestIsNotValid($this->obj, $this->nrdir, array("'$this->nrdir' exists, but is not writeable"));
        }
}