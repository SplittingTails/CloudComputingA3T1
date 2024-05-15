<?php
require ('helper/DynamoDB.php');
require ('helper/helper.php');
include ("views/header.php");
$tableName2 = 'ParkSpots_users';
$region = 'us-east-1';
$config = aws_Config();
$DDbClient = new DynamoDB($config);
$documents = $DDbClient->query($tableName2, [
    'Key' => [
        'email' => [
            'S' => $_SESSION['user']['email'],
        ],
    ],
]);

$index = 0;
$count = 1;
if (isset($_SESSION['user'])) { ?>
    <div class="position-relative">
        <div class="position-absolute top-0 start-50 translate-middle-x w-75">
            <div class="row">
                <div class="col">
                    <h1>User details:</h1>
                    <h2>User name: <?php echo $_SESSION['user']['user_name'] ?></h2>
                    <h2>Email: <?php echo $_SESSION['user']['email'] ?></h2>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h1>Current Bookings</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">kerbsideid</th>
                                <th scope="col">startdatetime</th>
                                <th scope="col">enddatetime</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($documents['Items'][0]['bookinglist'] as $item) {
      
                                echo '<tr>';
                                echo '<th scope="row">' . $count . '</th>';
                                echo '<td>' . $item[0]['M']['kerbsideid']['N'] . '</td>';
                                echo '<td>' . $item[0]['M']['startdatetime']['S'] . '</td>';
                                echo '<td>' . $item[0]['M']['enddatetime ']['S'] . '</td>';
                                echo '<td>';
                                echo '<form class="" action="/post-validation" method="post">';
                                echo '<input type="hidden" id="bookingid" name="bookingid" value="' . $item[0]['M']['bookingid']['N'] . '">';
                                echo '<input type="hidden" id="index" name="index" value="' . $index . '">';
                                echo '<input type="hidden" id="kerbsideid" name="kerbsideid" value="' . $item[0]['M']['kerbsideid']['N'] . '">';
                                echo '<button type="submit" name="removebooking" value="removebooking" class="btn btn-primary">Remove</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                                $count++;
                                $index++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } else {
    header('Location: /');
}
include ("views/footer.php");
?>