<?php

 /**************************************************************************
 * CONFIDENTIAL
 *
 *  2003-2018 IUGO Mobile Entertainment Inc
 *  All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains the property of
 * IUGO Mobile Entertainment Inc.  The intellectual and technical concepts
 * contained herein are proprietary to IUGO Mobile Entertainment Inc. and
 * may be covered by U.S. and Foreign Patents, patents in process, and are
 * protected by trade secret or copyright law.  Dissemination of this
 * information or reproduction of this material is strictly forbidden unless
 * prior written permission is obtained from IUGO Mobile Entertainment Inc.
 */

namespace IUGO;

class ClassLoader {
	private $namespace, $path;

	function __construct ($namespace, $path) {
		assert ('\\' === substr ($namespace, 0, 1));
		$this->namespace = ($namespace === '\\') ? '\\' : "$namespace\\";
		$this->path = $path;
	}

	# Returns TRUE iff $class is in $this->namespace.
	function class_in_namespace ($class) {
		return strpos("\\$class", $this->namespace) === 0;
	}

	# Returns the part of $class which extends beyond $this->namespace.
	# This includes the class name itself.
	function class_tail ($class) {
		assert ($this->class_in_namespace ($class));
		return substr ($class, strlen ($this->namespace) - 1);
	}

	# Returns the path of the file which contains the given class.
	function class_file_path ($class) {
		assert ($this->class_in_namespace ($class));
		return $this->path
			. DIRECTORY_SEPARATOR
			. str_replace (
				'\\',
				DIRECTORY_SEPARATOR,
				$this->class_tail ($class))
			. '.php';

	}

	function load_class ($class) {
		if ($this->class_in_namespace ($class)) {
			$path = $this->class_file_path ($class);
			if (is_readable ($path))
				include ($path);
		}
	}

	static function register ($namespace, $path) {
		$loader = new ClassLoader ($namespace, $path);
		spl_autoload_register (array ($loader, 'load_class'));
	}
}

