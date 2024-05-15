<?php
session_start();
require ('helper/DynamoDB.php');
require ('helper/S3.php');
require ('helper/helper.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $region = 'us-east-1';
    $config = aws_Config();
    /*** init variables  ***/
    $DDbClient = new DynamoDB($config);
    //clear alert error array
    unset($_SESSION['alerts']);
    //make alert error array
    $Alerts = array();
    /*** Register form ***/
    if ($_POST['register'] == "register") {
        $tableName2 = 'ParkSpots_users';
        $documents = $DDbClient->scan([
            'TableName' => $tableName2,
        ]);
        if (empty($_POST['email'])) {
            $Alerts['email_error'] = "email is required";
        } else {
            if ($documents['Count'] !== 0) {
                for ($i = 0; $i < $documents['Count']; $i++) {
                    if ($_POST["email"] === $documents['Items'][$i]['email']['S']) {
                        $Alerts['email_error'] = "The email already exists";
                    } else {
                        $_POST['email'] = test_input($_POST["email"]);
                    }
                }
            }
        }
        if (empty($_POST['username'])) {
            $Alerts['username_error'] = "username is required";
        } else {
            if ($documents['Count'] !== 0) {
                for ($i = 0; $i < $documents['Count']; $i++) {
                    if ($_POST["username"] === $documents['Items'][$i]['user_name']['S']) {
                        $Alerts['username_error'] = "The User name already exists";
                    } else {
                        $_POST['username'] = test_input($_POST["username"]);
                    }
                }
            }
        }
        if (empty($_POST['password'])) {
            $Alerts['password_error'] = "Password is required";
        } else {
            $_POST['password'] = password_hash($_POST["password"], PASSWORD_DEFAULT);
        }
        if (count($Alerts) > 0) {
            $_SESSION['alerts'] = $Alerts;
            header('Location: /register');
            exit();
        } else {
            $DDbClient->putItem([
                'Item' => [
                    'email' => [
                        'S' => $_POST['email'],
                    ],
                    'user_name' => [
                        'S' => $_POST['username'],
                    ],
                    'password' => [
                        'S' => $_POST['password'],
                    ],
                    'bookinglist' => [
                        'L' => []
                    ],
                ],
                'ReturnConsumedCapacity' => 'TOTAL',
                'TableName' => $tableName2,
            ]);
            header('Location: /');
            exit();
        }
    }
    /*** Login form ***/
    if ($_POST['login'] == "login") {
        $tableName2 = 'ParkSpots_users';
        if (empty($_POST['email'])) {
            $Alerts['email_error'] = "ID is required";
        } else {
            $documents = $DDbClient->query($tableName2, [
                'Key' => [
                    'email' => [
                        'S' => $_POST["email"],
                    ],
                ],
            ]);
            $_POST['email'] = test_input($_POST["email"]);
        }
        if (empty($_POST['password'])) {
            $Alerts['password_error'] = "Password is required";
        } else {
            $_POST['password'] = test_input($_POST["password"]);
        }
        if ($documents['Count'] === 0) {
            $Alerts['Login_Error'] = "ID or password is invalid";
        }
        if (count($Alerts) > 0) {
            $_SESSION['alerts'] = $Alerts;
            header('Location: /');
            exit();
        } else {
            if ($_POST["email"] === $documents['Items'][0]['email']['S'] && password_verify($_POST["password"], $documents['Items'][0]['password']['S'])) {
                $_SESSION['user']['email'] = $documents['Items'][0]['email']['S'];
                $_SESSION['user']['user_name'] = $documents['Items'][0]['user_name']['S'];
                header("Location: /mainpage");
            } else {
                $Alerts['Login_Error'] = "ID or password is invalid";
                $_SESSION['alerts'] = $Alerts;
                header("Location: /");
            }
        }
        exit();
    }
    if ($_POST['booking'] == "booking") {
        $tableName2 = 'ParkSpots_booking';
        $current_Datetime = date_create('now', timezone_open("Australia/Melbourne"));
        $ustartdatetime = date_create($_POST['startdate'] . ' ' . $_POST['starttime'], timezone_open("Australia/Melbourne"));
        $uenddatetime = date_create($_POST['enddate'] . ' ' . $_POST['endtime'], timezone_open("Australia/Melbourne"));
        if (empty($_POST['kerbsideid'])) {
            $Alerts['booking_error'] = "An internal server error, please try again.";
        }
        if (empty($_POST['startdate'])) {
            $Alerts['startdate_error'] = "Start date is required";
        }
        if (empty($_POST['starttime'])) {
            $Alerts['starttime_error'] = "Start time is required";
        }
        if (empty($_POST['enddate'])) {
            $Alerts['enddate_error'] = "End date is required";
        }
        if (empty($_POST['endtime'])) {
            $Alerts['endtime_error'] = "End time is required";
        }
        if ($ustartdatetime < $ucurrentDatetime) {
            $Alerts['booking_error'] = "start date and time can't be in the past";
        } else if ($uenddatetime < $ucurrentDatetime) {
            $Alerts['booking_error'] = "End date and time can't be in the past";
        } else if ($uenddatetime < $ustartdatetime) {
            $Alerts['booking_error'] = "End date must be later start date";
        }
        if (count($Alerts) <= 0) {
            $queryResult = $DDbClient->query($tableName2, [
                'Key' => [
                    'kerbsideid' => [
                        'N' => $_POST['kerbsideid'],
                    ],
                ],
            ]);
            $check = false;
            if ($queryResult['Count'] === 0) {
                $putItem = $DDbClient->putItem([
                    'Item' => [
                        'kerbsideid' => [
                            'N' => $_POST['kerbsideid'],
                        ],
                        'formatted_address' => [
                            'S' => $_POST['formatted_address']
                        ],
                        'bookinglist' => [
                            'L' => []
                        ],
                    ],
                    'ReturnConsumedCapacity' => 'TOTAL',
                    'TableName' => $tableName2,
                ]);
            } else {
                foreach ($queryResult['Items'][0]['bookinglist']['L'] as $items) {
                    $kstartdatetime = date_create($items['startdatetime']['S'], timezone_open("Australia/Melbourne"));
                    $kendDateTime = date_create($items['enddatetime']['S'], timezone_open("Australia/Melbourne"));
                    debug_to_console(MAX($ustartdatetime, $kstartdatetime) < MIN($uenddatetime, $kendDateTime));
                    if (MAX($ustartdatetime, $kstartdatetime) < MIN($uenddatetime, $kendDateTime)) {
                        $check = true;
                        break;
                    }
                }
            }
            if ($check) {
                $Alerts['booking_error'] = "Parking spot already booked during this time. please try again.";
            }
        }
        if (count($Alerts) > 0) {
            $_SESSION['alerts'] = $Alerts;
            header('Location: /parkingspot?id=' . $_POST["kerbsideid"]);
            exit();
        } else {
            $bookingID = uniqid();
            $update = $DDbClient->updateItemAttributeByKey($tableName2, [
                'kerbsideid' => [
                    'N' => $_POST['kerbsideid'],
                ],
            ], 'bookinglist', 'L', [['M' => ['bookingid' => ['S' => $bookingID], 'enddatetime ' => ['S' => $uenddatetime->format('c')], 'email' => ['S' => $_SESSION['user']['email']], 'startdatetime' => ['S' => $ustartdatetime->format('c')]]]], true);
            $update = $DDbClient->updateItemAttributeByKey('ParkSpots_users', [
                'email' => [
                    'S' => $_SESSION['user']['email']
                ]
            ], 'bookinglist', 'L', [['M' => ['bookingid' => ['S' => $bookingID], 'enddatetime ' => ['S' => $uenddatetime->format('c')], 'email' => ['S' => $_SESSION['user']['email']], 'startdatetime' => ['S' => $ustartdatetime->format('c')], 'kerbsideid' => ['N' => $_POST['kerbsideid']], 'formatted_address' => ['S' => $_POST['formatted_address']]]]], true);
            header('Location: /profile');
        }


    }
    if ($_POST['removebooking'] == "removebooking") {
        if (empty($_POST['bookingid'])) {
            $Alerts['booking_error'] = "An internal server error, please try again.";
        }
        if (empty($_POST['index'])) {
            $Alerts['booking_error'] = "An internal server error, please try again.";
        }
        $queryResult = $DDbClient->query('ParkSpots_booking', [
            'Key' => [
                'kerbsideid' => [
                    'N' => $_POST['kerbsideid'],
                ],
            ],
        ]);
        #$queryResult = $DDbClient->unmarshalItem($queryResult);

        $index = 0;

        foreach ($queryResult['Items'][0]['bookinglist'] as $item) {
            if ($item[0]['M']['bookingid']['S'] === $_POST['bookingid']) {
                break;
            }
            $index++;
        }

        $remove = $DDbClient->removeItemfromlist(
            [
                'Key' => [
                    'kerbsideid' => [
                        'N' => $_POST['kerbsideid'],
                    ]
                ],
                'TableName' => 'ParkSpots_booking',
                'UpdateExpression' => 'Remove bookinglist[' . $index . ']',
            ]
        );
        $remove = $DDbClient->removeItemfromlist(
            [
                'Key' => [
                    'email' => [
                        'S' => $_SESSION['user']['email']
                    ]
                ],
                'TableName' => 'ParkSpots_users',
                'UpdateExpression' => 'Remove bookinglist[' . $_POST['index'] . ']',
            ]
        );
        header('Location: /profile');
    }
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
