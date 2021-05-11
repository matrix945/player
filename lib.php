<?php
// This file is part of Moodle Course Rollover Plugin
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local_message
 * @author      Kristian
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require __DIR__ . '/vendor/autoload.php';
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

define(DEBUG , true);

global $CFG, $DB;

$aliToken = $DB->get_record('local_player', ['id' => '1']);
if(DEBUG){var_dump($aliToken);}

define('accessKeyId' , $aliToken->{'accesskeyid'});
define('accessKeySecret' , $aliToken->{'accesskeysecret'});


function GetVideoPlayAuth($vid)
{
    AlibabaCloud::accessKeyClient('LTAI5tAsdVMjhjPXbCDnmGrQ', 'vg6rxyhWDGoL3LYy39tw5LEJvby67m')
        ->regionId('cn-hangzhou')
        ->asDefaultClient();

    try {
        $result = AlibabaCloud::rpc()
            ->product('vod')
            // ->scheme('https') // https | http
            ->version('2017-03-21')
            ->action('GetVideoPlayAuth')
            ->method('POST')
            ->host('vod.cn-shanghai.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "cn-hangzhou",
                    'VideoId' => $vid,
                    'AuthInfoTimeout' => "600",
                ],
            ])
            ->request();
//    print_r($result->toArray());
    } catch (ClientException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    } catch (ServerException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    }
    return $result;
}

function searchMedia(){

    AlibabaCloud::accessKeyClient(accessKeyId, accessKeySecret)
        ->regionId('cn-hangzhou')
        ->asDefaultClient();

    try {
        $result = AlibabaCloud::rpc()
            ->product('vod')
            // ->scheme('https') // https | http
            ->version('2017-03-21')
            ->action('SearchMedia')
            ->method('POST')
            ->host('vod.cn-shanghai.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "cn-hangzhou",
                ],
            ])
            ->request();
//        print_r($result->toArray());
    } catch (ClientException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    } catch (ServerException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    }
    return $result;
}

//SearchMedia Ali-API
function parseMediaArray(){
    $mediaArray = array();
    $result = searchMedia();

//    var_dump($result->toArray());

    foreach ($result->toArray()['MediaList'] as $item){
        if ($item['MediaType'] == 'video' ){
            array_push($mediaArray , $item['MediaId']);
        }
//        var_dump($item->{'MediaId'});

    }

    var_dump($mediaArray);
    return $mediaArray;

}

//GetVideoInfos
function getVideoInfos($vidArray){

//    Stringfy array from parseMediaArray() to string
//    Formate: 123,213,123,123....

    $vids = implode(',', $vidArray);


    AlibabaCloud::accessKeyClient(accessKeyId, accessKeySecret)
        ->regionId('cn-hangzhou')
        ->asDefaultClient();

    try {
        $result = AlibabaCloud::rpc()
            ->product('vod')
            // ->scheme('https') // https | http
            ->version('2017-03-21')
            ->action('GetVideoInfos')
            ->method('POST')
            ->host('vod.cn-shanghai.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "cn-hangzhou",
                    'VideoIds' => $vids,
                ],
            ])
            ->request();
//        print_r($result->toArray());
    } catch (ClientException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    } catch (ServerException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    }

    return $result;
}

function parseVideoInfos($videoInfos){

    $vidVnameArray = array();

    $videoInfos->toArray();

    foreach ($videoInfos->toArray()['VideoList'] as $item){

//        视频状态。默认获取所有视频，多个使用英文逗号（,）分隔。取值包括：
//        Uploading：上传中。
//        UploadFail：上传失败。
//        UploadSucc：上传完成。
//        Transcoding：转码中。
//        TranscodeFail：转码失败。
//        Blocked：屏蔽。
//        Normal：正常。

        if ( $item['Status'] == 'Normal' ){
            $vidVnameArray[$item['Title']] = $item['VideoId'];

        }


    }


    var_dump($vidVnameArray);
    return $vidVnameArray;
}

function retrieveVidBasedOnFileName($videoName){

    $mediaArray = parseMediaArray();

    $videoInfos = getVideoInfos($mediaArray);

    $data = parseVideoInfos($videoInfos);

    var_dump($data[$videoName]);
    return $data[$videoName];

}