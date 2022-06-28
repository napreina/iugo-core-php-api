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

# A Request which derives its state from the
# standard PHP global variables.

class HttpRequest {
	private $_GET, $_POST, $_SERVER, $input;

	private $endpoint, $path;

	public function __construct (
		array $GET, array $POST, array $SERVER, string $input)
	{
			$this->_GET = $GET;
			$this->_POST = $POST;
			$this->_SERVER = $SERVER;
			$this->input = $input;
	}

	protected function parseURI()
	{
		if(is_null($this->endpoint))
		{
			$uri = $this->getURI();
			$path = strstr($uri, '?', true) ?: $uri;
			$strippedPath = substr($path, 1);
			$endpoint = strstr($strippedPath, '/', true) ?: $strippedPath;

			$this->endpoint = $endpoint;
			$this->path = $path;
		}
	}

	public function getEndpoint()
	{
		$this->parseURI();
		return $this->endpoint;
	}

	public function getPath()
	{
		$this->parseURI();
		return $this->path;
	}

	public function getUrlParam ($key) {
		return isset ($this->_GET[$key])
		? $this->_GET[$key]
		: NULL;
	}

	public function getPostParam ($key) {
		return isset ($this->_POST[$key])
		? $this->_POST[$key]
		: NULL;
	}

	public function getUrlParams () { return $this->_GET; }
	public function getPostParams () { return $this->_POST; }

	public function getInput () {
		return $this->input;
	}

	public function getJsonInput() {
		$input = $this->input;
		$input = get_magic_quotes_gpc() ? stripslashes($input) : $input;
		return json_decode($input, true);
	}

	public function getMethod () {
		return $this->_SERVER['REQUEST_METHOD'];
	}

	public function getURI () {
		return $this->_SERVER['REQUEST_URI'];
	}
}
