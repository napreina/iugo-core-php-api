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

class Transaction extends Processor
{
	public $secret_key = 'NwvprhfBkGuPJnjJp77UPJWJUpgC7mLz';
	public function processRequest(HttpRequest $request) : HttpResponse {
		try {
			$uri = $request->getEndpoint();
			switch ($uri) {
				case 'transaction':
					return $this->saveTranaction($request);
					break;
				case 'transactionstats':
					return $this->transactionStats($request);
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

	private function saveTranaction(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}
			$input = $request->getJsonInput();
			$verifier = $input['Verifier'];
			$transaction_id = $input['TransactionId'];
			$user_id = $input['UserId'];
			$currency_amount = $input['CurrencyAmount'];
			$sha1_hash_key = sha1($this->secret_key.$transaction_id.$user_id.$currency_amount);

			if($verifier != $sha1_hash_key) {
				return $this->error_handling(null, $verifier);
			}

			$db = new DAO();

			$created_at = date('Y-m-d H:i:s');
			try {
				
				$conn = $db->openConnection();
				$validation_sql = "Select id from transactions where verifier like '".$verifier."'";
				$query_result = $conn->query($validation_sql);
				$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);

				if(empty($result)) {
					$sql = "INSERT INTO `transactions`(`transaction_id`, `user_id`, `currency_amount`, `created_at`, `verifier`) VALUES ('" . $transaction_id . "','" . $user_id . "','" . $currency_amount . "','" . $created_at . "','".$verifier."')";

					$conn->query($sql);
					$db->closeConnection();
				} else {
					return $this->error_handling($th=null, "This data already exists");	
				}
				
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

	public function transactionStats(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}

			$input = $request->getJsonInput();
			$user_id = $input['UserId'];

			$db = new DAO();
			
			try {
				$conn = $db->openConnection();	
				$sql = "Select user_id as UserId, count(id) as TransactionCount, sum(currency_amount) as CurrencySum from transactions where user_id = $user_id;";

				$query_result = $conn->query($sql);
				if(empty($query_result))
					return $this->error_handling();

				$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);
				$db->closeConnection();
				
			} catch (\Throwable $th) {
				return $this->error_handling($th);
			}

			$response = array_pop($result);

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
