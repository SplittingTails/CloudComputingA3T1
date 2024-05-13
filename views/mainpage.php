<?php
require ('helper\DynamoDB.php');
include ("header.php");
$pageTitle = 'Main Page';
/*** Configuration ***/
$region = 'us-east-1';
$config = aws_Config();
/*** init variables  ***/
$DDbClient = new DynamoDB($config);
$lastEvaluatedKey;
$tableName = 'ParkSpots';
$result = $DDbClient->scan([
    'TableName' => $tableName
]);
// The page to display (Usually is received in a url parameter)
$page = intval($_GET['page']);

// The number of records to display per page
$page_size = 25;

// Calculate total number of records, and total number of pages
$total_records = count($result);
$total_pages = ceil($total_records / $page_size);

// Validation: Page to display can not be greater than the total number of pages
if ($page > $total_pages) {
    $page = $total_pages;
}

// Validation: Page to display can not be less than 1
if ($page < 1) {
    $page = 1;
}

// Calculate the position of the first record of the page to display
$offset = ($page - 1) * $page_size;
$count = $offset + 1;
// Get the subset of records to be displayed from the array
$data = array_slice($result, $offset, $page_size);



?>
<?php if (isset($_SESSION['user'])) { ?>
    <div class="position-relative">
        <div class="col">
            <h1>Search for a car spot</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $name) {
                        echo '<tr>';
                        echo '<th scope="row">'.$count.'</th>';
                        echo '<td><a href=\'/parkingspot?id=' . $name['kerbsideid'] . '\'>' . $name['kerbsideid'] . '</a></td>';
                        echo '<td><a href=\'/parkingspot?id=' . $name['kerbsideid'] . '\'>' . $name['address_components'][0]['short_name'] . '</a></td>';
                        echo '<td><a href=\'/parkingspot?id=' . $name['kerbsideid'] . '\'>' . $name['address_components'][1]['short_name'] . '</a></td>';
                        echo '<td><a href=\'/parkingspot?id=' . $name['kerbsideid'] . '\'>' . $name['address_components'][2]['short_name'] . '</a></td>';
                        echo '<td><a href=\'/parkingspot?id=' . $name['kerbsideid'] . '\'>' . $name['address_components'][4]['short_name'] . '</a></td>';
                        echo '<td><a href=\'/parkingspot?id=' . $name['kerbsideid'] . '\'>' . $name['address_components'][6]['short_name'] . '</a></td>';
                        echo '</tr>';
                        $count++;
                    } ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php for ($x = 1; $x < $total_pages + 1; $x++) {
                        echo '<li class="page-item"><a class="page-link" href="\mainpage?page=' . $x . '">' . $x . '</a></li>';
                    } ?>
                </ul>
            </nav>
        </div>
    </div>


<?php } else {
    header('Location: /');
} ?>
<?php include ("footer.php");
