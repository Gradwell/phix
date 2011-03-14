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
 * @package     Gradwell
 * @subpackage  ComponentMaker
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Gradwell\ComponentMaker\Entities;

class ComponentFolder
{
        const BUILD_PROPERTIES = 'build.properties';

        const STATE_UNKNOWN = 0;
        const STATE_EMPTY = 1;
        const STATE_UPTODATE = 2;
        const STATE_NEEDSUPGRADE = 3;

        /**
         * The folder that contains the component we represent
         * @var string folder
         */
        public $folder = null;

        /**
         * The path to the build.properties file in the folder
         * @var string
         */
        public $buildPropertiesFile = null;
        
        /**
         * The current state of the folder
         * @var int 
         */
        public $state = self::STATE_UNKNOWN;

        /**
         * The current version of the properties file
         * @var int
         */
        public $componentVersion = 0;

        public $pathToDataFolder = null;

        public function __construct($folder)
        {
                $this->folder = $folder;
                $this->buildPropertiesFile = $folder . '/' . self::BUILD_PROPERTIES;
                $this->pathToDataFolder = static::DATA_FOLDER;
                $this->loadFolderState();
        }

        public function loadFolderState()
        {
                // has this folder been turned into a component before?
                if (!file_exists($this->buildPropertiesFile))
                {
                        $this->state = self::STATE_EMPTY;
                        return;
                }

                // we have a build.properties file
                // let's have a peak inside
                $properties = \parse_ini_file($this->buildPropertiesFile);

                // if it does not have the contents we expect
                // we will discard it
                $expected = array ('component.type', 'component.version');
                foreach ($expected as $expectedProperty)
                {
                        if (!isset($properties[$expectedProperty]))
                        {
                                $this->state = self::STATE_EMPTY;
                                return;
                        }
                }

                // okay, we have a build.properties file that we like
                $this->componentVersion = $properties['component.version'];
                $this->state = self::STATE_UPTODATE;

                // now, does the folder need an upgrade?
                if ($this->componentVersion < static::LATEST_VERSION)
                {
                        $this->state = self::STATE_NEEDSUPGRADE;
                }

                // all done
        }

        public function getStateAsText()
        {
                $stateText = array
                (
                        self::STATE_UNKNOWN             => 'unknown',
                        self::STATE_EMPTY               => 'empty',
                        self::STATE_UPTODATE            => 'up to date',
                        self::STATE_NEEDSUPGRADE        => 'needs upgrade'
                );

                if (isset($stateText[$this->state]))
                {
                        return $stateText[$this->state];
                }

                return 'state not recognised';
        }

        public function copyFilesFromDataFolder($files, $dest='/')
        {
                foreach ($files as $filename)
                {
                        $srcFile = $this->pathToDataFolder . '/' . $filename;
                        $destFile = $this->folder . $dest . $filename;

                        if (!copy($srcFile, $destFile))
                        {
                                throw new \Exception('unable to copy ' . $srcFile . ' to ' . $destFile);
                        }
                }
        }

        public function replaceFolderContentsFromDataFolder($src, $dest='/')
        {
                $srcFolder  = $this->pathToDataFolder . '/' . $src;
                $destFolder = $this->folder . $dest;

                \rmdir($destFolder);
                \mkdir($destFolder);

        }

        public function testHasBuildProperties()
        {
                if (file_exists($this->buildPropertiesFile))
                {
                        return true;
                }

                return false;
        }

        public function editBuildPropertiesVersionNumber($newNumber)
        {
                if (!$this->testHasBuildProperties())
                {
                        return false;
                }

                $buildProperties = file_get_contents($this->buildPropertiesFile);
                $buildProperties = \preg_replace('|^component.version.*=.*$|m', 'component.version=' . $newNumber, $buildProperties);
                \file_put_contents($this->buildPropertiesFile, $buildProperties);
        }
}