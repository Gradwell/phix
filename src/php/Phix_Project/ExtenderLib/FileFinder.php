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
 * @subpackage  ExtenderLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\ExtenderLib;

class FileFinder
{
        public function findPhpFilesFromPartialNamespace($partialNamespace)
        {
                // step 1: turn our partial namespace into a needle
                //
                // this makes sure that we have the right DIRECTORY_SEPARATORs
                // for the local operating system
                $needle = \__gwc_normalise_path($partialNamespace);

                // step 2: find the folder(s) that match the namespace
                $folders = $this->findAllMatchingSubFoldersFromIncludeDirs($partialNamespace);

                // step 3: find the PHP files in those folders that are
                // interesting to us
                $files = array();
                foreach ($folders as $folder)
                {
                        $files = array_merge($files, $this->findAllFilesInFolder($folder, '|.php$|'));
                }

                // all done
                return $files;
        }

        protected function findAllMatchingSubFoldersFromIncludeDirs($needle)
        {
                $searchFolders = explode(PATH_SEPARATOR, \get_include_path());

                $return = array();
                foreach ($searchFolders as $searchFolder)
                {
                        $return = array_merge($return, $this->findAllMatchingSubFoldersFromFolder($searchFolder, $needle));
                }

                return $return;
        }

        protected function findAllMatchingSubFoldersFromFolder($folder, $needle)
        {
                // does the folder exist?
                if (!is_dir($folder))
                {
                        // no - return an empty array
                        // should we throw an exception instead?
                        return array();
                }

                // let's look inside
                // $dirStack is a queue, not a stack, hence the unusual
                // way of dealing with it here
                $dirQueue = new \SplQueue();
                $dirQueue->enqueue($folder);

                $return   = array();

                // this loop is designed to avoid recursion, just in case
                // we ever find ourselves up against the filesystem
                // from hell
                while ($dirQueue->count())
                {
                        $dirName = $dirQueue->dequeue();

                        $dh = \dir($dirName);
                        if (!$dh)
                        {
                                // not a valid directory
                                continue;
                        }

                        // what is in this directory?
                        while (false !== ($dirEntry = $dh->read()))
                        {
                                // skip over self and parent and hidden folders
                                if ($dirEntry{0} == '.')
                                {
                                        continue;
                                }

                                $potential = $dh->path . DIRECTORY_SEPARATOR . $dirEntry;

                                if (is_dir($potential))
                                {
                                        $dirQueue->enqueue($potential);
                                }
                        }

                        // does this directory itself look interesting?
                        if (strpos($dirName, $needle) !== false)
                        {
                                // $subFolder = substr($dirName, strlen($folder) + strlen(\DIRECTORY_SEPARATOR));
                                $return[] = $dirName;
                        }
                }

                return $return;
        }

        protected function findAllFilesInFolder($folder, $regex)
        {
                // do we have a real directory to look at?
                if (!is_dir($folder))
                {
                        // no we do not
                        return array();
                }

                // let's take a look inside
                $return = array();
                $dh = \dir($folder);
                while (false !== ($entry = $dh->read()))
                {
                        $potential = $dh->path . DIRECTORY_SEPARATOR . $entry;

                        // skip over all the non-files
                        if (!is_file($potential))
                        {
                                continue;
                        }

                        // does this file match our regex?
                        if (preg_match($regex, $entry))
                        {
                                // yes it does
                                $return[] = $potential;
                        }
                }

                return $return;
        }
}