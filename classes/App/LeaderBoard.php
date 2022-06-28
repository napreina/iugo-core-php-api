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

class LeaderBoard extends Processor
{
	public function processRequest(HttpRequest $request) : HttpResponse {
		try {
			$uri = $request->getEndpoint();
			switch ($uri) {
				case 'scorepost':
					return $this->postScore($request);
					break;
				case 'leaderboardget':
					return $this->getLeaderboard($request);
					break;
				default : 
					return $this->error_handling();
					break;
			}


		} catch (\Throwable $th) {
			return $this->error_handling();
		}
	}

	public function postScore(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}

			$input = $request->getJsonInput();
			$user_id = $input['UserId'];
			$leaderboard_id = $input['LeaderboardId'];
			$score = $input['Score'];

			$db = new DAO();
			$conn = $db->openConnection();

			$search_sql = "Select * from lb_scores where user_id = $user_id and lb_id = $leaderboard_id and score >= $score Order By score Desc limit 1;";
			$query_result = $conn->query($search_sql);
			$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);

			if(empty($result)) {
				$delete_sql = "Delete from lb_scores where user_id = $user_id and lb_id = $leaderboard_id and score < $score;";
				$conn->query($delete_sql);

				$insert_sql = "Insert Into lb_scores (user_id,lb_id,score) Values ($user_id, $leaderboard_id, $score);";
				$conn->query($insert_sql);

			} else {
				$result = array_pop($result);
				$user_id = $result['user_id'];
				$leaderboard_id = $result['lb_id'];
				$score = $result['score'];
			}

			$rank_sql = "Select count(user_id) as rank From lb_scores Where lb_id=$leaderboard_id and score > $score;";
			$query_result = $conn->query($rank_sql);
			$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);
			$result = array_pop($result);
			$rank = $result['rank']+1;

			$db->closeConnection();
			
			$response = [
				"UserId" => $user_id, 
				"LeaderboardId" => $leaderboard_id,
				"Score" => $score, 
				"Rank" => $rank
			];

			return new HttpResponse(json_encode($response), 'text/json');

		} catch (\Throwable $th) {
			return $this->error_handling($th);
		}
	}

	public function getLeaderBoard(HttpRequest $request) : HttpResponse {
		try {
			if($request->getMethod() != 'POST') {
				return $this->error_handling();
			}
			
			$input = $request->getJsonInput();
			$user_id = $input['UserId'];
			$leaderboard_id = $input['LeaderboardId'];
			$offset = $input['Offset'];
			$limit = $input['Limit'];

			$db = new DAO();
			$conn = $db->openConnection();

			$user_lb_sql = "Select * From lb_scores Where lb_id = $leaderboard_id and user_id =$user_id order by score Desc limit 1";
			$query_result = $conn->query($user_lb_sql);
			$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);
			if(empty($result)) {
				$score = 0;
			} else {
				$result = array_pop($result);
				$score = $result['score'];
			}
			
			$rank_sql = "Select count(user_id) as rank From lb_scores Where lb_id=$leaderboard_id and score > $score;";
			$query_result = $conn->query($rank_sql);
			$result = $query_result->fetchAll(\PDO::FETCH_ASSOC);
			if(empty($result)) {
				$rank = 1;	
			} else {
				$result = array_pop($result);
				$rank = $result['rank']+1;
			}			
			
			$entitiy_sql = "SELECT user_id as UserId, score as Score, ROW_NUMBER() OVER(ORDER BY score desc) Rank  FROM `lb_scores` WHERE lb_id = $leaderboard_id order by score desc limit $offset, $limit;";
			$query_result = $conn->query($entitiy_sql);
			$entries = $query_result->fetchAll(\PDO::FETCH_ASSOC);

			$db->closeConnection();

			$response = [
				"UserId" => $user_id, 
				"LeaderboardId" => $leaderboard_id, 
				"Score" => $score, 
				"Rank" => $rank, 
				"Entries" => $entries
			];

			return new HttpResponse(json_encode($response), 'text/json');

		} catch (\Throwable $th) {
			return $this->error_handling($th);
		}
	}

	public function error_handling(\Throwable $th = null) : HttpResponse {
		$errorMessage = $th != null? $th->getMessage(): "There is an issue with your request";
		$response = [
			"Error" => true, 
			"ErrorMessage" =>  $errorMessage
		];
		return new HttpResponse(json_encode($response), 'text/json');
	}
}
