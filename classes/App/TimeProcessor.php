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

namespace App;

use IUGO\HttpRequest;
use IUGO\HttpResponse;
use IUGO\Processor;

class TimeProcessor extends Processor
{
	public function processRequest(HttpRequest $request) : HttpResponse {
		return new HttpResponse(json_encode(['timestamp' => time()]), 'text/json');
	}
}
