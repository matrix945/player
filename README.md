# MoodleSchoolFilePlugin

# Dependence:
1) 阿里phpsdk 
可以用这个link 里面的 composer 安装https://developer.aliyun.com/article/753970  
也可以直接下载sdk 放置到所需目录 https://blog.csdn.net/wwz123124/article/details/97546179  


# TODO:
1) some aliplayer settings need be adjusted like autoplay, frame size etc



# Install
1) git clone this repo to /moodle/local
2) **Moodle PITFALL** comment the code  
```php
$aliToken = $DB->get_record('local_player', ['id' => '1']);  
if(DEBUG){var_dump($aliToken);}

define('accessKeyId' , $aliToken->{'accesskeyid'});
define('accessKeySecret' , $aliToken->{'accesskeysecret'});
```
Moodle will run code before db installation. It hits the error get_record can't find db...

3) Refresh your homepage the installation guidence will show up. It install plugin and db automatically.
4) Uncomment the code after installation and insert your Ali key and token to moodle_local_player


# how to use?
1) Upload your vedio to ali vod
2) Apply for Ali key and secret
3) encode your vedio as HSL
4) Install plugin
5) play encode video by tye /moodle/local/player?name=<vedio name>
