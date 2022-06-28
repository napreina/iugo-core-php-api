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
use PDO;
use PDOException;

class DAO
{
	private $server = "mysql:host=localhost;dbname=iugo";

    private $user = "root";

    private $pass = "";

    private $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );

    protected $con;

    /* Function for opening connection */
    public function openConnection()

    {
        try
        {

            $this->con = new PDO($this->server, $this->user, $this->pass, $this->options);

            return $this->con;
        }
        catch (PDOException $e)
        {
			return "There is some problem in connection: " . $e->getMessage();
        }
    }

    /* Function for closing connection */
    public function closeConnection()
    {
        $this->con = null;
    }
}
