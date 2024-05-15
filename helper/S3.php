<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3
{

    private S3Client $s3Client;

    public function __construct(array $config)
    {
        $this->s3Client = new S3Client($config);
    }
    /*function s3Client(): S3Client
    {

        $config = aws_Config();
        return new S3Client($config);

    }*/

    function putObject(array $item)
    {
        #$s3Client = s3Client();

        try {
            $this->s3Client->putObject($item);
        } catch (Exception $exception) {
            echo $exception->getMessage();
            exit("Please fix error with file upload before continuing.");
        }
    }

    function putObjectAcl(array $item)
    {
        #$s3Client = s3Client();

        //set ACL for image
        $this->s3Client->putObjectAcl($item);
    }


    function createBucket(string $bucketName, string $region)
    {
        #$s3Client = s3Client();
        try {
            return $this->s3Client->createBucket([
                'Bucket' => $bucketName,
                'CreateBucketConfiguration' => ['LocationConstraint' => $region],
                'ObjectOwnership' => 'BucketOwnerPreferred'
            ]);
        } catch (Exception $exception) {
            echo "Failed to create bucket $bucketName with error: " . $exception->getMessage();
            exit("Please fix error with bucket creation before continuing.");
        }
        #$this->s3Client->waitUntil('BucketExists', ['Bucket' => $bucketName]);
    }

    function listObjectsV2(string $bucketName): AWS\Result
    {
        #$s3Client = s3Client();

        return $this->s3Client->listObjectsV2([
            'Bucket' => $bucketName,
        ]);


    }

    function deletePublicAccessBlock(string $bucketName)
    {
        //Remove public block
#$s3Client = s3Client();
        return $this->s3Client->deletePublicAccessBlock([
            'Bucket' => $bucketName, // REQUIRED
        ]);
        
    }

    function putBucketAcl(string $bucketName)
    {
        //Set bucket public settings
#$s3Client = s3Client();
        return $this->s3Client->putBucketAcl([
            'ACL' => 'public-read',
            'Bucket' => $bucketName, // REQUIRED
        ]);
    }
    function listBuckets(): AWS\Result
    {
        return $this->s3Client->listBuckets([
        ]);
    }
}