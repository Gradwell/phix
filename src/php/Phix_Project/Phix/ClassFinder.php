<?php
/**
 * Copyright (c) 2010 Martin Wernståhl.
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
 *   * Neither the name of Martin Wernståhl nor the names of his
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
 * @author      Martin Wernståhl <m4rw3r@gmail.com>
 * @copyright   2010 Martin Wernståhl
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://github.com/m4rw3r
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\Phix;

/**
 * Searches through a set of paths for classes, returns a list of classes
 * and in which file they are located, does NOT include the files.
 *
 * If duplicate classes are found, returns only the first file that
 * contains the class
 */

class ClassFinder
{	
	/**
	 * The regex determining which files to search.
	 * 
	 * @var string
	 */
	protected $fileRegex = '/\.php$/';
	
	/**
	 * A list of paths to search.
	 * 
	 * @var array(string)
	 */
	protected $foldersToSearch = array();
		
        public function addFolderToSearch($folder)
        {
                // this is one way to see if the folder actually exists
                // or not!
                if ($folder == '.')
                {
                        $folder = \getcwd();
                }
                $realPathToFolder = realpath($folder);

                if (!$realPathToFolder)
                {
                        throw new \Exception('folder ' . $folder . ' does not exist');
                }
                
                $this->foldersToSearch[$realPathToFolder] = $realPathToFolder;
        }

        public function addFoldersToSearch($folders)
        {
                foreach ($folders as $folder)
                {
                        $this->addFolderToSearch($folder);
                }
        }

        public function getFoldersToSearch()
        {
                return $this->foldersToSearch;
        }

        public function setFileRegex($fileRegex)
        {
                $this->fileRegex = $fileRegex;
        }
	
	// -----------------------------------------------------------------

	/**
	 * Searches the supplied paths for the files and returns a list 
	 * of classes and in which file they're located.
	 * 
	 * @return array(class => file)
	 */
	public function findClassFiles()
	{
                $return = array();

		foreach($this->foldersToSearch as $folderToSearch)
		{
			// Search the folder
			$diriter  = new \RecursiveDirectoryIterator($folderToSearch);
			$iteriter = new \RecursiveIteratorIterator($diriter, \RecursiveIteratorIterator::LEAVES_ONLY);
			$files    = new \RegexIterator($iteriter, $this->fileRegex);
			
			foreach($files as $filename => $fileinfo)
			{
                                $searchArray = array
                                (
                                        $folderToSearch . DIRECTORY_SEPARATOR,
                                        '.php',
                                        DIRECTORY_SEPARATOR,
                                );
                                $replaceArray = array
                                (
                                        '',
                                        '',
                                        '[_\\\\]',
                                );

                                $requiredClassish = '~' . str_replace($searchArray, $replaceArray, $filename) . '~';

                                $foundClasses = $this->getClasses($filename);

                                // do we have a PSR0-compliantly-named class
                                // in the file?
                                //
                                // this is needed because Linux distros install
                                // any tests that we ship *inside* the php_dir
                                // folder <-- braindead :( :(

                                $hasPSR0Class = false;
                                foreach ($foundClasses as $class)
                                {
                                        if (preg_match($requiredClassish, $class))
                                        {
                                                $hasPSR0Class = true;
                                        }
                                }

                                if ($hasPSR0Class)
                                {
                                        foreach($foundClasses as $class)
                                        {
                                                if(!isset($return[$class]))
                                                {
                                                        // class is new!
                                                        $return[$class] = $filename;
                                                }
                                        }
                                }
			}
		}
		
		return $return;
	}
	
	// -----------------------------------------------------------------

	/**
	 * Tokenizes the file and iterates all tokens in search of classes.
	 * 
	 * @param  string         Path to file
	 * @return array(string)  List of classnames
	 */
	protected function getClasses($filepath)
	{
		$tokens = token_get_all(file_get_contents($filepath));
		
		$classes          = array();
		$is_classname     = false;
		$is_namespace     = false;
		$inside_namespace = false;
		$indentation      = 0;
		$current_ns       = null;
		
		foreach($tokens as $token)
		{
			if( ! is_array($token))
			{
				// we're only interested in brackets
				switch($token)
				{
					case '{':
						$indentation++;
						break;
						
					case '}':
						$indentation--;
						break;
				}
				
				// no class name or namespace name can follow brackets
				$is_classname = false;
				$is_namespace = false;
				
				continue;
			}
			
			switch($token[0])
			{
				case T_WHITESPACE:
					// No need to count
					break;
					
				case T_CLASS:
				case T_INTERFACE:
					// Next is a classname
					$is_classname = true;
					break;
					
				case T_NAMESPACE:
					// Next is a namespace and we're inside it
					$is_namespace = true;
					$inside_namespace = true;
					
					// reset so we're sure that we get an empty namespace if the user decides
					// to create one ("namespace;"):
					$current_ns = '';
					break;
					
				case T_STRING:
					if($is_classname)
					{
						// Found a class, add the namespace if we have one (which isn't the global, "empty" namespace)
						$classes[] = ($inside_namespace && ! empty($current_ns) ? $current_ns.'\\' : '').$token[1];
					}
					// namespaces cannot be within indentation
					elseif($is_namespace && $indentation == 0)
					{
						$current_ns .= $token[1];
						continue;
					}
					// Intentionally no break
					
				case T_NS_SEPARATOR:
					// Allow multple levels of namespaces
					if($is_namespace && $indentation == 0)
					{
						$current_ns .= $token[1];
						continue;
					}
					// Intentionally no break
					
				default:
					// Something else, not a namespace or class
					$is_classname = false;
					$is_namespace = false;
			}
		}
		
		return $classes;
	}
}
