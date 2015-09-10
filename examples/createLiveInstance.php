<?php
/**
 * Created by David Moser <david.moser@bitmovin.net>
 * Date: 31.08.15
 * Time: 15:28
 */

use bitcodin\Bitcodin;
use bitcodin\LiveInstance;

require_once __DIR__.'/../vendor/autoload.php';

Bitcodin::setApiToken('insertYourApiKey'); // Your can find your api key in the settings menu. Your account (right corner) -> Settings -> API

$liveInstance = LiveInstance::create("live stream test");

echo "Waiting until live stream is RUNNING...\n";
while($liveInstance->status != $liveInstance::STATUS_RUNNING)
{
    sleep(2);
    $liveInstance->update();
    if($liveInstance->status == $liveInstance::STATUS_ERROR)
    {
        echo "ERROR occurred!";
        throw new \Exception("Error occurred during Live stream creation");
    }
}

echo "Livestream RTMP push URL: ".$liveInstance->rtmpPushUrl."\n";
echo "MPD URL: ".$liveInstance->mpdUrl."\n";
echo "HLS URL: ".$liveInstance->hlsUrl."\n";

$liveInstance = LiveInstance::delete($liveInstance->id);

echo "Waiting until live stream is TERMINATED...\n";
while($liveInstance->status != $liveInstance::STATUS_TERMINATED)
{
    sleep(2);
    $liveInstance->update();
    if($liveInstance->status == $liveInstance::STATUS_ERROR)
    {
        echo "ERROR occurred!";
        throw new \Exception("Error occurred during Live stream deletion");
    }
}

echo "Live stream TERMINATED\n";