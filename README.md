YouTube
=======

YouTube视频解析类


2012-12-28创建：
使用方法

简单的调用：

require(./class/youtube.php);

$yt = new YouTube('http://www.youtube.com/watch?v=iQDm6XzgbPw&list=PL14BFF58590E9CE26');

$yt->getVdatas(); //获取所有分辨率的视频

$yt->getVdata();  //获取flv-320*240质量的视频（默认，可指定质量，见下面示例）






指定质量调用：

require(./class/youtube.php);

$yt = new YouTube('http://www.youtube.com/watch?v=iQDm6XzgbPw&list=PL14BFF58590E9CE26','mp4-640*320');

$yt->getVdatas(); //获取所有分辨率的视频

$yt->getVdata();  //获取mp4-640*320质量的视频

另外可根据需要指定返回数据类型，默认维数组。
具体请自行测试。。。。。。。




调试程序：
require(./class/youtube.php);

$yt = new YouTube('http://www.youtube.com/watch?v=iQDm6XzgbPw&list=PL14BFF58590E9CE26','mp4-640*320');

$yt->debug(); //输出调试信息

