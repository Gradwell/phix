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
 * @subpackage  Phix
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\Phix;

class ExtensionsFinder
{
        protected $context;
        
        protected $foldersToSearch = array();

        public function __construct(Context $context)
        {
                $this->context = $context;
                $this->addPhpSearchPathToSearchList();
        }

        public function addPhpSearchPathToSearchList()
        {
                foreach (explode(\PATH_SEPARATOR, \get_include_path()) as $path)
                {
                        $this->addFolderToSearch($path);
                }
        }

        public function addFolderToSearch($path)
        {
                $this->foldersToSearch[$path] = $path;
        }

        public function findExtensions()
        {
                $commandsList = new CommandsList();

                // Find all classes in php files whose paths contain "PhixCommands":
                $classFinder = new ClassFinder(explode(\PATH_SEPARATOR, \get_include_path()), '/PhixCommands.*\.php$/');
                
                try
                {
                        $files = $classFinder->getClassFiles();
                }
                catch(ClassConflictException $e)
                {
                        $this->context->stderr->output($this->context->errorStyle, $this->context->errorPrefix);
                        $this->context->stderr->outputLine(null, $e->getMessage());
                        
                        $files = $classFinder->getClassFiles();
                }

                foreach ($files as $newClass => $filename)
                {
                        include_once $filename;

                        if ($this->testIsPhixExtension($newClass))
                        {
                                // we have a winner!
                                $commandsList->importCommandsFromExtension($newClass);
                        }
                }

                return $commandsList;
        }

        protected function testIsPhixExtension($className)
        {
                $refObj = new \ReflectionClass($className);
                if ($refObj->implementsInterface('\Phix_Project\PhixExtensions\CommandInterface'))
                {
                        return true;
                }

                return false;
        }
}