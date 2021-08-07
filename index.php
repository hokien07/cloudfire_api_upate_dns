<?php 
require './vendor/autoload.php';
$key     = new Cloudflare\API\Auth\APIKey('email', 'api_key');
$adapter = new Cloudflare\API\Adapter\Guzzle($key);
$zone    = new Cloudflare\API\Endpoints\Zones($adapter);
$dns    = new Cloudflare\API\Endpoints\DNS($adapter);

echo "Start to get list of zone" . PHP_EOL;
$zones = $zone->listZones('', '', 1, 1000, '', '', 'all')->result;
echo "You have: " .count($zones)  . " zones." . PHP_EOL;

echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

echo "Start to update" . PHP_EOL;

foreach ($zones as $mZone)  {
    echo "Start to get record for " . $mZone->name . PHP_EOL;

    $records = $dns->listRecords($mZone->id)->result;
    echo "Have " . count($records) .  " records" . PHP_EOL;

    foreach ($records  as $record) {
        $content = $record->content;
        echo "Content is " . $content . PHP_EOL;

        if (preg_match('/tmskip/', $content)) {
            echo "Start udpate for " . $record->name . PHP_EOL;
            $newCotent = str_replace('tmskip', 'supover', $content);
            $update = [
                "type" => "CNAME",
                "name" => $record->name,
                "content" =>$newCotent,
                "ttl" => 1,
                "proxied" => false
            ];
            $dns->updateRecordDetails($record->zone_id, $record->id, $update);
            echo "Update for " . $record->name . " Successfully" .  PHP_EOL;
        }
    }
    echo PHP_EOL;
    echo PHP_EOL;
}

echo "End to update" . PHP_EOL;




?>