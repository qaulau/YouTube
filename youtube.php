<?php
/***
 * YouTube视频解析类
 * 支持播放页地址及视频ID解析，及支持返回单视频(可指定分辨率)和所有分辨率视频
 * getVdatas()为所有分辨率视频
 * getVdata()为单视频
 * 指定分辨率格式有3gp-176x144、3gp-320x240、flv-320x240、flv-640x360、mp4-640x360、webm-640x360等
 *
 * @author	qaulau@hotmail.com
 * @date	2012-12-28
 * @version	YouTube 1.0
 *
***/

class YouTube
{
	private $url;		//视频地址
	private $quality;	//清晰度
	private $vid;		//视频id
	private $info;		//视频信息
	private $formats = array(//视频格式
		//FLV格式
		'0' => 'flv',
		'5' => 'flv',
		'6' => 'flv',
		'34' => 'flv',
		'35' => 'flv',
		//3GP格式
		'13' => '3gp',
		'17' => '3gp',
		'36' => '3gp',
		//MP4格式
		'18' => 'mp4',
		'22' => 'mp4',
		'37' => 'mp4',
		'38' => 'mp4',
		'82' => 'mp4',
		'83' => 'mp4',
		'84' => 'mp4',
		'85' => 'mp4',
		//WEBM格式		
		'43' => 'webm',
		'44' => 'webm',
		'45' => 'webm',
		'46' => 'webm',
		'100' => 'webm',
		'101' => 'webm',
		'102' => 'webm'
	);

	//构造函数
	public function __construct($parama,$quality = '0'){
		if(preg_match('/(youtube\.be|youtube\.com)/',$parama)){
			$this->url = $parama;
			$this->vid = $this->getVidByUrl();
		}else{
			$this->vid = $parama;
			$this->url = $this->getUrlByVid();
		}
		$this->quality = $quality;
		$this->info = $this->getVideoInfo();
	}

	//获取视频信息
	private function getVideoInfo(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_URL,'http://www.youtube.com/get_video_info?video_id='.$this->vid);
        $data = curl_exec($ch);
        curl_close($ch);
	parse_str($data,$info);
        return $info;
	}

	//根据URL获取vid
	private function getVidByUrl(){
		preg_match('/v=(.{11})/',$this->url,$matches);
		return $matches[1];
	}

	//根据id获取url
	private function getUrlByVid()
	{
	    return 'http://www.youtube.com/watch?v='.$this->vid;
	} 

	//获取视频的缩略图
	public function getThumbnails()
	{
	    $thumbnails = array(							//视频缩略图
				'big' => $this->info['iurlsd']?$this->info['iurlsd']:'http://i1.ytimg.com/vi/'.$this->vid.'/sddefault.jpg',	//大图
				'small' => $this->info['thumbnail_url']	//小图			
			);
		return $thumbnails;
	} 

	//获取所有分辨率视频
	private function getVurls()
	{
		$vurls_map = explode(',',$this->info['url_encoded_fmt_stream_map']);
		foreach($vurls_map as $link ){
			parse_str($link ,$parts);
			$link = $parts['url'].='&signature='.$parts['sig'];
			//获取视频格式
			preg_match('#(^|\D)'.$parts['itag'].'/([0-9]{2,4}x[0-9]{2,4})#', $this->info['fmt_list'], $format);
			//创建视频地址信息
			$vurls[$this->formats[$parts['itag']] .'-'. $format[2]] = array($this->formats[$parts['itag']], $format[2], $link);
		}
		return $vurls;
	} 

	/**
	 * 获取视频数据默认返回的是一个数组，如果为true的话则返回json数据
	 * */
	public function getVdatas($isJson = false){
		$info = $this->info;
		$data = array(
			'title' => $info['title'],				//视频标题
			'keywords' => $info['keywords'],		//视频关键词
			'seconds' => $info['length_seconds'],	//视频时间长度（单位秒）
			'thumbnails' => $this->getThumbnails(),	//视频缩略图
			'vurl' => $this->getVurls()				//视频播放地址
		);

		if($isJson)
			return json_encode($data);
		else
			return $data;

	}

	//获取指定分辨率视频
	public function getVdata()
	{
	    $vurls = $this->getVurls();
		$info = $this->info;
		foreach ($vurls as $quality=> $link) {
		    if($this->quality = $vurls[$quality]){
				$vurl = $vurls[$quality][2];
				break;
			}else{
				$vurl = $vurls['flv-320x240'][2];
				break;
			}
		}
		$data =	 array(
			'title' => $info['title'],				//视频标题
			'keywords' => $info['keywords'],		//视频关键词
			'seconds' => $info['length_seconds'],	//视频时间长度（单位秒）
			'thumbnails' => $this->getThumbnails(),	//视频缩略图
			'vurl' => $vurl							//视频播放地址
		);
		return $data;
	} 

	//程序调试
	public function debug()
	{
		$debugMsg = print('<b><font color="red">以下是调试信息:</font></b><pre>');
		$debugMsg .= print('<br/><font color="#0000ff">视频地址：</font>');
		$debugMsg .= $this->url?print($this->url):print('<font color="red">无法获取视频地址!</font>');
		$debugMsg .= print('<br/><font color="#0000ff">视频&nbsp;ID&nbsp;：</font>');
		$debugMsg .= $this->vid?print($this->vid):print('<font color="red">无法获取视频ID!</font>');
		$debugMsg .= print('<br/><font color="#0000ff">视频信息：</font>');
		$debugMsg .= @array_filter($this->info)?var_dump($this->info):print('<font color="red">视频信息获取失败，无法与Youtube进行连接!</font>');
		$debugMsg .= print('<br/><font color="#0000ff">视频图片：</font>');
		$debugMsg .= @array_filter($this->getThumbnails())?var_dump($this->getThumbnails()):print('<font color="red">无法获取视频图片!</font>');
		$debugMsg .= print('<br/><font color="#0000ff">播放地址：</font>');
		$debugMsg .= @array_filter($this->getVurls())?var_dump($this->getVurls()):print('<font color="red">无法获取视频播放地址!</font>');
		$debugMsg .= print('<br/><font color="#0000ff">视频数据：</font>');
		$debugMsg .= @array_filter($this->getVdatas())?var_dump($this->getVdatas()):print('<font color="red">无法获取视频数据!</font>');
		$debugMsg .= print('<br/><font color="#0000ff">指定视频：</font>');
		$debugMsg .= @array_filter($this->getVdata())?var_dump($this->getVdata()):print('<font color="red">无法获取指定视频数据!</font>');

		return $debugMsg;
	} 
    
}


?>
