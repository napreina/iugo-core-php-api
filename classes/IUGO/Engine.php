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

class Engine
{
	protected $processors;

	public function __construct(array $processors)
	{
		foreach($processors as $endpoint => $processor)
		{
			if(!is_a($processor, 'IUGO\Processor'))
				throw new \Exception("Processor for $endpoint is the wrong type");
		}

		$this->processors = $processors;
	}

	public function processRequest($request)
	{
		$endpoint = $request->getEndpoint();

		if(!isset($this->processors[$endpoint]))
		{
			http_response_code(404);
			echo "<h1>$endpoint not found</h1>";
			die();
		}

		try {
			$response = $this->processors[$endpoint]->processRequest($request);
		}
		catch(\Exception $e)
		{
			http_response_code(500);
			echo '<h1>Uncaught Exception: ' . $e->getMessage() . '</h1>';
			die();
		}

		http_response_code($response->getCode());
		header('Content-Type: ' . $response->getType());
		echo $response->getContent();
	}
}
