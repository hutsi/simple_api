<?php

$settings = array(
    "required" => array(
        "userid" => null,
        "productid" => null,
        "price" => null
    ),
    "optional" => array(
        "description" => "no description",
        "answerType" => "json",
        "another_param"=>"value",
        "another_param2"=>"value2",
        "another_param3"=>"value3",
        "another_param4"=>"value4"
    ),
    "validation_rules" => array(
        "check_incomeIsNotEmptyArray" => true,
        "check_emptyParams"=>true,
        "check_unnecesarryParams" => true,
        "check_requiredParams" => true,
        "check_dependenciesParams"=>true,
        "dependencies"=>array(
            "another_param2"=>array("another_param3", "another_param4"),
            "another_param3"=>array("another_param")
        )
    )
);

switch ($_SERVER["REQUEST_METHOD"])
{
    case "POST":
        $incoming = $_POST;
        break;

    case "GET":
        $incoming = $_GET;
        break;
}

/** includeing our base class */
require_once ("protected/Api.php");
$api = new Api($settings);

/** returns an array of [Status, Code, Data] of validation */
$validStatus = $api->getValidState($incoming);
/** processing the code of validation */

switch ($validStatus["Code"])
{
    case 600:
        $response = $api->sendMsg($validStatus["Status"], "Params not set or empty",
            "Please, try again");
        echo $response;
        break;

    case 602:
        $response = $api->sendMsg($validStatus["Status"], "Unnecessary params: " . $validStatus["Data"],
            "Please, try again");
        echo $response;
        break;

    case 601:
        $response = $api->sendMsg($validStatus["Status"],
            "Required parameters should be set: " . $validStatus["Data"],
            "Please, try again");
        echo $response;
        break;

    case 603:
        $response = $api->sendMsg($validStatus["Status"],
            "Congratulation. Ty For using our service!", $validStatus["Data"]);
        echo $response;
        break;
    case 604:
        $response = $api->sendMsg($validStatus["Status"], "Dependence Error",
            "You requested parameter: ".$validStatus["OtherInfo"]." but it need to set with it params: ". $validStatus["Data"]);
        echo $response;
        break;
    case 605:
        $response = $api->sendMsg($validStatus["Status"], "Empty Params",
            "This params you send are empty: ".$validStatus["Data"]);
        echo $response;
        break;
    
    default:
        $response = $api->sendMsg("Notice", "I cant stand you..",
            "Maybe validation rules set to false. Turn it on!");
        echo $response;
        break;
}
