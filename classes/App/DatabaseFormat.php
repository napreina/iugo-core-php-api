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

class DatabaseFormat extends Processor
{
	public function processRequest(HttpRequest $request) : HttpResponse {
		try {
			$uri = $request->getEndpoint();
			switch ($uri) {
				case 'databaseformat':
					return $this->formatDatabase($request);
					break;
				default : 
					return $this->error_handling();
					break;
			}
		} 
		catch (\Throwable $th) {
			$this->error_handling($th);
		}
	}

	private function formatDatabase(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}
			$input = $request->getJsonInput();
			$token = $input['token']?? null;

			if($token != 'team_player') {
				return $this->error_handling(null, "Please add the token");
			}

			$db = new DAO();
			
			try {
				
				$conn = $db->openConnection();
				$validation_sql = "TRUNCATE TABLE lb_scores;TRUNCATE TABLE transactions;TRUNCATE TABLE user_data;";
				$conn->query($validation_sql);
				
			} catch (\Throwable $th) {
				return $this->error_handling($th);
			}

			$response = [
				'Success' => true
			];

			return new HttpResponse(json_encode($response), 'text/json');
			
		} catch (\Throwable $th) {
			return $this->error_handling($th);
		}
	}

	public function error_handling(\Throwable $th = null, $custom_message = null) : HttpResponse {
		$errorMessage = $th != null? $th->getMessage(): $custom_message??"There is an issue with your request";
		$response = [
			"Error" => true, 
			"ErrorMessage" =>  $errorMessage
		];

		return new HttpResponse(json_encode($response), 'text/json');
	}
}
