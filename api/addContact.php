<?php

    require 'Contact.php';
    require 'supportFunctions.php';
    require 'dbInfo.cfg.php';

    // Get and sanitize xhr data
    $xhrRequest = getXhrRequestInfo();

    // Validation
    if (is_null($xhrRequest)) {
        returnError(null);
    } 
    else {

        // Get and prepare fields for SQL Query
        $xFirstName = $xhrRequest[ContactFields::FIRST_NAME];
        $xLastName = $xhrRequest[ContactFields::LAST_NAME];
        $xEmail = $xhrRequest[ContactFields::EMAIL];
        $xPhone = $xhrRequest[ContactFields::PHONE];
        $xContactType = $xhrRequest[ContactFields::CONTACT_TYPE];
        $xCity = $xhrRequest[ContactFields::CITY];
        $xState = $xhrRequest[ContactFields::STATE];
        $xZip = $xhrRequest[ContactFields::ZIP];
        $xOwnerID = $xhrRequest[ContactFields::OWNER_ID];

        // Connect to database
        $sqlConnection = new mysqli('localhost', dbinfo::$dbUser, dbinfo::$dbPass, dbinfo::$db);
        if ($sqlConnection->connect_error) {
            error_log($sqlConnection->connect_error);
            returnError(null);
        }

        //log any error that occurs
        mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);

        // Connection established. Begin query and try to store data

        try {
            $sqlQuery = "INSERT INTO `contacts` (`id`, `firstName`, `lastName`, `email`, `phoneNumber`, 
                        `contactType`, `city`, `state`, `zip`, `ownerID`) VALUES 
                        (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $sqlStmt = $sqlConnection->prepare($sqlQuery);
            
            // Bind Parameters
            $sqlStmt->bind_param("sssssssii", $xFirstName, $xLastName, $xEmail, $xPhone, $xContactType,
                                $xCity, $xState, $xZip, $xOwnerID);
            
            // Execute SQL Statement
            $success = $sqlStmt->execute();

            if ($success) {
                $returnArr = array("success" => 1);
            }
            else {
                $returnArr = array("success" => -1);
            } 

            returnXhrRequestAsJson($returnArr);
        }
        catch (Exception $exception) {
            error_log("Exception occured during Request handling: " . $exception->getMessage());
            returnError(null);
        }
    }

    function returnError($errorCode) {
        returnXhrRequestAsJson(array("error occured"));
        exit();
    }
?>