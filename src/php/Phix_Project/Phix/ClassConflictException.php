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
 * Exception telling that a conflicting class name was discovered
 * by the class finder.
 */
class ClassConflictException extends \RuntimeException
{
	// TODO: Is this threshold good?
	const MAX_MESSAGE_LENGTH = 40;
	
	protected $classes   = array();
	
	protected $files     = array();
	
	/**
	 * @param  array(array('class' => string, 'file' => string), ...)
	 */
	function __construct(array $conflicts)
	{
		$this->classes = array_map(function($elem)
		{
			return $elem['class'];
		}, $conflicts);
		
		$this->files = array_map(function($elem)
		{
			return $elem['file'];
		}, $conflicts);
		
		$classlist = implode(', ', $this->classes);
		$filelist  = implode(', ', $this->files);
		
		if(strlen($classlist) > static::MAX_MESSAGE_LENGTH)
		{
			$classlist = substr($classlist, 0, static::MAX_MESSAGE_LENGTH).'...';
		}
		
		if(strlen($filelist) > static::MAX_MESSAGE_LENGTH)
		{
			$filelist = substr($filelist, 0, static::MAX_MESSAGE_LENGTH).'...';
		}
		
		parent::__construct('ClassFinder: Found conflicting class(es): '.$classlist.' in files: '.$filelist);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of unique class names which were conflicted.
	 * 
	 * @return array(string)
	 */
	public function getConflictingClasses()
	{
		return array_unique($this->classes);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of files containing conflicting classes.
	 * 
	 * @return array(string)
	 */
	public function getConflictingFiles()
	{
		return array_unique($this->files);
	}
}
