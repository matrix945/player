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
 * @package     local_player
 * @author      Frank
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/player/lib.php');
//require __DIR__ . '/vendor/autoload.php';
global $DB;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


$PAGE->set_url(new moodle_url('/local/player/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Player');

echo $OUTPUT->header();

// or  strpos($_GET['name'], '.mp4') !== FALSE
if(($_GET['name']) != null  ){

    $vid = retrieveVidBasedOnFileName($_GET['name']);
    $auth = GetVideoPlayAuth($vid);
    echo "try to retrieve " . $_GET['name'];
    echo "</br>";
    echo $vid;
    echo "</br>";
    echo $auth->toArray()['PlayAuth'];
}

$templatecontext = (object)[
    'Vid' => $vid,
    'Playauth' => $auth->toArray()['PlayAuth'],
    "Autoplay" => 'true',
    "Format" => "m3u8",
//    "source" => 'http://vod1.cia-online.cn/customerTrans/245ce76add4218a8cad80c7228595b21/3a3ad169-178f0248415-0012-d1ca-ddb-f9972.mp4'
];

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>Aliplayer Online Settings</title>
    <link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.9.3/skins/default/aliplayer-min.css" />
    <script type="text/javascript" charset="utf-8" src="https://g.alicdn.com/de/prismplayer/2.9.3/aliplayer-min.js"></script>
</head>
<body>
<div class="prism-player" id="player-con"></div>
<script>
    var player = new Aliplayer({
                "id": "player-con",
                "vid": "$vid",
                "playauth": "$playauth",
                "qualitySort": "asc",
                "format": "m3u8",
                "mediaType": "video",
                "encryptType": 1,
                "width": "100%",
                "height": "500px",
                "autoplay": false,
                "isLive": false,
                "rePlay": false,
                "playsinline": true,
                "preload": true,
                "controlBarVisibility": "hover",
                "useH5Prism": true
            }, function (player) {
                console.log("The player is created");
            }
    );
</script>
</body>
';


$vars = array(
    '$vid' => $vid,
    '$playauth' => $auth->toArray()['PlayAuth'],
);

echo '</br>';
echo '</br>';



//echo strtr($html, $vars);

// FIXME: somehow the ali player code is not compatible with Moodle footer. You will receive an error
//  vid's video URL hasn't been fetched
echo $OUTPUT->render_from_template('local_player/manage', $templatecontext);

//echo $OUTPUT->footer();
