<?php
declare(strict_types=1);
require 'vendor\autoload.php';
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use LDAP\Result;

class DynamoDB
{

    private DynamoDbClient $DDbClient;
    private Marshaler $marshal;

    public function __construct(array $config)
    {
        $this->DDbClient = new DynamoDbClient($config);
        $this->marshal = new Marshaler();

    }


    /*function DynamoDbClient(): DynamoDbClient
    {
        

        $config = aws_Config();


        
        return new DynamoDbClient($config);

    }*/

    /*public function data_Query(array $query): AWS\Result
    {
        #$DDbClient = DynamoDbClient();
        $result = $this->DDbClient->query($query);

        return $result;
    }

*/

    public function putItem(array $array): AWS\Result
    {
        return $this->DDbClient->putItem($array);
    }


    public function scan(array $query = []): array
    {

        $result = $this->DDbClient->scan($query);
        return $this->unmarshalItem($result);

    }

    public function query(string $tableName, $key): AWS\Result
    {



        $expressionAttributeValues = [];
        $expressionAttributeNames = [];
        $keyConditionExpression = "";
        $index = 1;
        foreach ($key as $name => $value) {

            $keyConditionExpression .= "#" . array_key_first($value) . " = :v$index,";
            $expressionAttributeNames["#" . array_key_first($value)] = array_key_first($value);
            $hold = array_pop($value);
            $expressionAttributeValues[":v$index"] = [
                array_key_first($hold) => array_pop($hold),
            ];
        }
        $keyConditionExpression = substr($keyConditionExpression, 0, -1);

        $query = [
            'ExpressionAttributeValues' => $expressionAttributeValues,
            'ExpressionAttributeNames' => $expressionAttributeNames,
            'KeyConditionExpression' => $keyConditionExpression,
            'TableName' => $tableName,
        ];

        return $this->DDbClient->query($query);
    }



    public function deleteItemByKey(string $tableName, array $key)
    {
        #$DDbClient = DynamoDbClient();
        $this->DDbClient->deleteItem([
            'Key' => $key['Item'],
            'TableName' => $tableName,
        ]);
    }

    public function writeBatch(string $TableName, array $Batch, int $depth = 2)
    {
        if (--$depth <= 0) {
            throw new Exception("Max depth exceeded. Please try with fewer batch items or increase depth.");
        }
        #$DDbClient = DynamoDbClient();

        $total = 0;
        foreach (array_chunk($Batch, 25) as $Items) {
            foreach ($Items as $Item) {
                $BatchWrite['RequestItems'][$TableName][] = ['PutRequest' => ['Item' => $this->marshal->marshalItem($Item)]];
                echo '<br>';
            }
            try {
                echo "Batching another " . count($Items) . " for a total of " . ($total += count($Items)) . " items!\n";
                $response = $this->DDbClient->batchWriteItem($BatchWrite);
                $BatchWrite = [];
            } catch (Exception $e) {
                echo "uh oh...";
                echo $e->getMessage();
                die();
            }
            if ($total >= 250) {
                echo "250 movies is probably enough. Right? We can stop there.\n";
                break;
            }
        }
    }

    public function createTable(array $attributes, string $tableName)
    {
        #$DDbClient = DynamoDbClient();
        try {
            $keySchema = [];
            $attributeDefinitions = [];


            foreach ($attributes as $attribute) {
                $keySchema[] = ['AttributeName' => $attribute['AttributeName'], 'KeyType' => $attribute['KeyType']];
                $attributeDefinitions[] =
                    ['AttributeName' => $attribute['AttributeName'], 'AttributeType' => $attribute['AttributeType']];
            }

            $Result = $this->DDbClient->createTable([
                'TableName' => $tableName,
                'KeySchema' => $keySchema,
                'AttributeDefinitions' => $attributeDefinitions,
                'ProvisionedThroughput' => ['ReadCapacityUnits' => 5, 'WriteCapacityUnits' => 5],
            ]);
            dd($attributes);
            $this->DDbClient->waitUntil("TableExists", ['TableName' => $tableName]);
            
            return $Result;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function updateItemAttributeByKey(
        string $tableName,
        array $key,
        string $attributeName,
        string $attributeType,
        mixed $newValue,
        bool $append = false


    ): AWS\Result {

        $UpdateExpression = "set #NV=:NV";

        if ($append) {
            $UpdateExpression = "SET #NV = list_append(#NV, :NV)";
            #$UpdateExpression = "SET #NV[50] = :NV";
        }

        return $this->DDbClient->updateItem([
            'Key' => $key,
            'TableName' => $tableName,
            'UpdateExpression' => $UpdateExpression,
            'ExpressionAttributeNames' => [
                '#NV' => $attributeName,
            ],
            'ExpressionAttributeValues' => [
                ':NV' => [
                    $attributeType => $newValue
                ]
            ],

            'ReturnValues' => 'ALL_NEW'
        ]);


    }

    public function listTables($exclusiveStartTableName = "", $limit = 100): AWS\Result
    {
        $results = $this->DDbClient->listTables([
            'ExclusiveStartTableName' => $exclusiveStartTableName,
            'Limit' => $limit,
        ]);

        return $results;
    }

    public function unmarshalItem(AWS\Result $array): array
    {


        $data = [];
        foreach ($array['Items'] as $item) {
            $data[] = $this->marshal->unmarshalItem($item);
        }

        return $data;
    }
    public function marshalItem(array $array): array
    {
        $data = $this->marshal->marshalItem($array);


        return $data;
    }

    public function removeItemfromlist(array $key): AWS\Result {

        return $this->DDbClient->updateItem($key);


    }
}



?>