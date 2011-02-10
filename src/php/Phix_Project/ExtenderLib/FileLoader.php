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

class FileLoader
{
        /**
         * Loads a PHP file, and returns a list of the new classes
         * that have been declared by the loaded file
         *
         * @param string $filename
         * @return array
         */
        public function loadPhpFile($filename)
        {
                $frozenClasses = $this->freezeClassNames();
                $this->includePhpFile($filename);
                $newClasses = $this->thawClassNames($frozenClasses);

                return $newClasses;
        }

        protected function freezeClassNames()
        {
                return \get_declared_classes();
        }

        protected function thawClassNames($frozenClasses)
        {
                return array_values(array_diff(\get_declared_classes(), $frozenClasses));
        }

        protected function includePhpFile($filename)
        {
                if (!file_exists($filename))
                {
                        throw new \Exception('cannot find filename ' . $filename);
                }

                if (!\is_readable($filename))
                {
                        throw new \Exception('file ' . $filename . ' exists, but is unreadable');
                }

                $existingVars = array_keys(\get_defined_vars());

                include_once $filename;

                $newVars = array_diff(array_keys(\get_defined_vars()), $existingVars);
                foreach ($newVars as $varName)
                {
                        if ($varName !== 'existingVars' && $varName !== 'filename')
                        {
                                $GLOBALS[$varName] = $$varName;
                        }
                }
        }

}