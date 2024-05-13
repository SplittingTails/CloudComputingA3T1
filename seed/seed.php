<?php
require 'vendor\autoload.php';
require ('helper\DynamoDB.php');
require ('helper\S3.php');
require_once ("helper\helper.php");

use Aws\DynamoDb\Exception\DynamoDbException;

function seed()
{
    /*** Configuration ***/
    $region = 'us-east-1';
    $config = aws_Config();
    /*** init variables  ***/
    $DDbClient = new DynamoDB($config);
    $s3Client = new S3($config);
    $tableName = 'ParkSpots';
    $tableCheck = false;
    $APIKey = '6131530b0639b7dea40aff649fd3e4a73d5b82a176ea30196e0bd0ff';
    $APIKEY2 = 'AIzaSyBSl0O5G4Ld1pKb65_qTlB2tXZELgJuYVg';
    $url = 'https://data.melbourne.vic.gov.au/api/explore/v2.1/catalog/datasets/';
    $DatasetID = 'on-street-parking-bay-sensors';
    $export = 'json';
    $Limit = 250;
    $Timezone = 'UTC';
    $use_labels = 'false';
    $epsg = '4326';
    $request_url = '' . $url . $DatasetID . '/exports/' . $export . '?limit=' . $Limit . '&timezone=' . $Timezone . '&use_labels=' . $use_labels . '&epsg=' . $epsg . '&apikey=' . $APIKey . '';
    $response = null;

    //Check User table exists else create
    $listResult = $DDbClient->listTables($tableName);
    //loop through table list to find music table
    foreach ($listResult['TableNames'] as $item) {
        if ($item == $tableName) {
            $tableCheck = true;
        }
    }
   
    //if table does not exist create it
    if (!$tableCheck) {
        try {

           $tableresult = $DDbClient->createTable(
                [
                    [
                        'AttributeName' => 'kerbsideid',
                        'KeyType' => 'HASH',
                        'AttributeType' => 'N',
                    ],
                ],
                $tableName
            );

            
        } catch (DynamoDbException $e) {
            echo $e->getMessage();
        }
    }

    /*** User ***/

    $tableName2 = 'ParkSpots_users';
    $tableCheck = false;

    //Check User table exists else create
    $listResult = $DDbClient->listTables($tableName2);
    //loop through table list to find music table
    foreach ($listResult['TableNames'] as $item) {
        if ($item == $tableName2) {
            $tableCheck = true;
        }
    }
    //if table does not exist create it
    //if table does not exist create it
    if (!$tableCheck) {
        try {

            $createTable = $DDbClient->createTable(
                [
                    [
                        'AttributeName' => 'email',
                        'KeyType' => 'HASH',
                        'AttributeType' => 'S',
                    ],
                ]
                ,
                $tableName2
            );

        } catch (DynamoDbException $e) {
            echo $e->getMessage();
        }
    }
    $tableName3 = 'ParkSpots_booking';
    $tableCheck = false;

    //Check User table exists else create
    $listResult = $DDbClient->listTables($tableName3);
    //loop through table list to find music table
    foreach ($listResult['TableNames'] as $item) {
        if ($item == $tableName3) {
            $tableCheck = true;
        }
    }
    //if table does not exist create it
    //if table does not exist create it
    if (!$tableCheck) {
        try {

            $DDbClient->createTable(
                [
                    [
                        'AttributeName' => 'kerbsideid',
                        'KeyType' => 'HASH',
                        'AttributeType' => 'N',
                    ],
                    [
                        'AttributeName' => 'bookingid',
                        'KeyType' => 'RANGE',
                        'AttributeType' => 'N',
                    ]


                ]
                ,
                $tableName3
            );

        } catch (DynamoDbException $e) {
            echo $e->getMessage();
        }
    }

    $queryResult = $DDbClient->query($tableName, [
        'Key' => [
            'kerbsideid' => [
                'N' => '5458',
            ],
        ],
    ]);

    if ($queryResult['Count'] === 0) {
        try {
            $curl = curl_init($request_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($curl);
            curl_close($curl);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $response = json_decode($response, true);
        for ($x = 0; $x < count($response); $x++) {
            $request_url2 = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $response[$x]['location']['lat'] . ',' . $response[$x]['location']['lon'] . '&location_type=ROOFTOP&result_type=street_address&key=' . $APIKEY2;
            try {
                $curl2 = curl_init($request_url2);
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
                $response2 = curl_exec($curl2);
                curl_close($curl2);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            $response2 = json_decode($response2, true);



            $response[$x]['address_components'] = $response2['results'][0]['address_components'];
            $response[$x]['formatted_address'] = $response2['results'][0]['formatted_address'];
        }

        $DDbClient->writeBatch($tableName, $response, $depth = 2);
    }
    /*** S3 Bucket  ***/

    $bucketName = 'parking-images-s3273504';

    //check bucket exists

    $listBucketsResult = $s3Client->listBuckets();
    $bucketExists = false;
    foreach ($listBucketsResult['Buckets'] as $bucket) {
        if ($bucket['Name'] === $bucketName) {
            $bucketExists = true;
        }
    }
    if (!$bucketExists) {

        $createBucketResult = $s3Client->createBucket($bucketName, $region);
        $deletePublicAccessBlockResult = $s3Client->deletePublicAccessBlock($bucketName);
        //Set bucket public settings
        $putBucketAclResult = $s3Client->putBucketAcl($bucketName);


    }

    $listObjectsV2Result = $s3Client->listObjectsV2($bucketName);

    if ($listObjectsV2Result['KeyCount'] === 0) {

        if ($response === null) {
            $response = $DDbClient->scan([
                'TableName' => $tableName,
            ]);
        }

        for ($x = 0; $x < count($response); $x++) {
            #print('lat' . $response[$x]['location']['lat']);
            $request_url3 = 'https://maps.googleapis.com/maps/api/streetview?size=600x600&location=' . $response[$x]['location']['lat'] . ',' . $response[$x]['location']['lon'] . '&key=' . $APIKEY2;
            try {
                $curl3 = curl_init($request_url3);
                curl_setopt($curl3, CURLOPT_RETURNTRANSFER, 1);
                $response3 = curl_exec($curl3);
                curl_close($curl3);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            //scan Music table and insert artist image to S3
            //loop through music info and download image
            $imgName = uniqid() . '.jpg';

            //upload image to S3
            $s3Client->putObject([
                'Body' => $response3,
                'Bucket' => $bucketName,
                'Key' => $imgName,
                'ContentType' => 'image/jpg',
            ]);

            //set ACL for image
            $s3Client->putObjectAcl([
                'ACL' => 'public-read',
                'Bucket' => $bucketName,
                'Key' => $imgName,
            ]);
            print ('kerbsideid' . $response[$x]['kerbsideid']);
            //update music table with img url for S3
            $DDbClient->updateItemAttributeByKey($tableName, [
                'kerbsideid' => [
                    'N' => '' . $response[$x]['kerbsideid'] . '',
                ],
            ], 'img_s3_location', 'S', 'https://ddbx4qqsgyss1.cloudfront.net/' . $imgName);
        }

    }
}

?>