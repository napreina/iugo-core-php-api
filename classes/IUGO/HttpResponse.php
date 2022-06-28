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

class HttpResponse
{
	protected $content, $type, $code;

	public function __construct(string $content, string $type = 'text/html', int $code = 200)
	{
		$this->content = $content;
		$this->type = $type;
		$this->code = $code;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getCode()
	{
		return $this->code;
	}
}