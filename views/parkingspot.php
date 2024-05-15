<?php
require ('helper/DynamoDB.php');
require ('helper/helper.php');
include ("views/header.php");
$kerbsideid = $_GET['id'];
$region = 'us-east-1';
$config = aws_Config();
$tableName = 'ParkSpots';
$DDbClient = new DynamoDB($config);
$result = $DDbClient->query($tableName, [
    'Key' => [
        'kerbsideid' => [
            'N' => $kerbsideid,
        ],
    ],
]);
$date = date_create($result['Items'][0]['status_timestamp']['S'], timezone_open("Australia/Melbourne"));
$date = date_format($date, "d/m/Y H:i:s");
if (isset($_SESSION['user'])) { ?>
    <div class="position-relative">
        <div class="position-absolute top-0 start-50 translate-middle-x w-75">
            <div class="row mt-5">
                <div class="col">
                    <?php echo '<a href=\'' . $result['Items'][0]['img_s3_location']['S'] . '\'><img src="' . $result['Items'][0]['img_s3_location']['S'] . '" class="img-fluid img-thumbnail" alt="' . $result['Items'][0]['formatted_address']['S'] . '"></a>' ?>
                </div>
                <div class="col">
                    <?php
                    echo '<p class="fw-bold">Location</p><p class="fw-normal">' . $result['Items'][0]['formatted_address']['S'] . '</p>';
                    echo '<p class="fw-bold">Status of Park</p><p class="fw-normal">' . $result['Items'][0]['status_description']['S'] . '</p>';
                    echo '<p class="fw-bold">Last Status Update</p><p class="fw-normal">' . $date . '</p>';
                    ?>
                </div>
            </div>
            <form class="p-4 border border-black rounded" action="/post-validation" method="post">
                <div class="row">
                    <div class="col">
                        <input type="hidden" id="kerbsideid" name="kerbsideid" value="<?php echo $kerbsideid ?>">
                        <input type="hidden" id="formatted_address" name="formatted_address" value="<?php echo  $result['Items'][0]['formatted_address']['S'] ?>">
                        <label for="startdate" class="form-label">Start Date:</label>
                        <input type="date" id="startdate" class="form-control" name="startdate"
                            min="<?php echo date("Y-m-d"); ?>" aria-describedby="startdateHelp">
                        <?php if (isset($_SESSION['alerts']['startdate_error']))
                            echo '<div id="startdateHelp" class="form-text text-danger">' . $_SESSION['alerts']['startdate_error'] . '</div>'; ?>
                        <label for="starttime" class="form-label">Start Time:</label>
                        <input id="starttime" type="time" class="form-control" name="starttime"
                            aria-describedby="starttimeHelp" ?>
                        <?php if (isset($_SESSION['alerts']['starttime_error']))
                            echo '<div id="starttimeHelp" class="form-text text-danger">' . $_SESSION['alerts']['starttime_error'] . '</div>'; ?>
                    </div>
                    <div class="col">
                        <label for="enddate" class="form-label">End Date:</label>
                        <input type="date" id="enddate" class="form-control" name="enddate"
                            min="<?php echo date("Y-m-d"); ?>" aria-describedby="enddateeHelp">
                        <?php if (isset($_SESSION['alerts']['enddate_error']))
                            echo '<div id="startdateHelp" class="form-text text-danger">' . $_SESSION['alerts']['enddate_error'] . '</div>'; ?>

                        <label for="endtime" class="form-label">End Time:</label>
                        <input id="endtime" type="time" class="form-control" name="endtime"
                            aria-describedby="endtimeeHelp" />
                        <?php if (isset($_SESSION['alerts']['endtime_error']))
                            echo '<div id="startdateHelp" class="form-text text-danger">' . $_SESSION['alerts']['endtime_error'] . '</div>'; ?>

                    </div>
                    <div class="row">
                        <div class="col">
                            <?php if (isset($_SESSION['alerts']['booking_error']))
                                echo '<p class="fw-bold text-danger m-2">' . $_SESSION['alerts']['booking_error'] . '</p>'; ?>
                            <button type="submit" name="booking" value='booking' class="btn btn-primary m-2">Book</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
} else {
    header('Location: /');
}
include ("views/footer.php");

?>