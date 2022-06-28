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

class User extends Processor
{
	public function processRequest(HttpRequest $request) : HttpResponse {
		try {
			$uri = $request->getEndpoint();
			switch ($uri) {
				case 'usersave':
					return $this->saveUser($request);
					break;
				case 'userload':
					return $this->loadUser($request);
					break;
				default : 
					return $this->error_handling();
					break;
			}
		} catch (\Throwable $th) {
			return $this->error_handling($th);
		}
	}

	public function saveUser(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}

			$input = $request->getJsonInput();
			$user_id = $input['UserId'];
			$data = $input['Data'];

			$db = new Dao();
			$conn = $db->openConnection();

			$user_sql = "Select * from user_data where user_id = $user_id ;";
			$query_result = $conn->query($user_sql);
			$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);

			if(empty($result)) {
				$insert_data = base64_encode(json_encode($data));	
				$insert_sql = "Insert Into user_data (user_id, data) Values ($user_id, '".$insert_data."'); ";

				$conn->query($insert_sql);
			} else {
				$result = array_pop($result);
				$user_data = $result['data'];
				
				$user_data = $user_data? json_decode(base64_decode($user_data), true): [];
				$user_data = array_merge($user_data, $data);
				$data = base64_encode(json_encode($user_data));
				$update_sql = "Update user_data set data = '".$data."' where user_id=$user_id;";
				$conn->query($update_sql);
			}

			$db->closeConnection();

			$response = [
				"success" => true
			];

			return new HttpResponse(json_encode($response), 'text/json');

		} catch (\Throwable $th) {
			return $this->error_handling($th);
		}
	} 

	public function loadUser(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}

			$input = $request->getJsonInput();
			$user_id = $input['UserId'];

			$db = new DAO();
			$conn = $db->openConnection();

			$user_id = 111;
			$user_sql = "Select * from user_data where user_id = $user_id ;";
			$query_result = $conn->query($user_sql);
			$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);
			
			if(empty($result)) {
				$data = [];
			} else {
				
				$data = base64_decode($result[0]['data']);
				$data = json_decode($data, true);
			}

			$db->closeConnection();

			$response = $data;

			return new HttpResponse(json_encode($response), 'text/json');

		} catch (\Throwable $th) {
			return $this->error_handling();
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
