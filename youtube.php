<?php
class YouTube
{
    private $url;		//视频地址
	private $quality;	//清晰度
	private $vid;		//视频id
	private $formats = array(//视频格式
		//FLV格式
		'5' => 'flv',
		'6' => 'flv',
		'34' => 'flv',
		'35' => 'flv',
		//3GP格式
		'13' => '3gp',
		'17' => '3gp',
		'36' => '3gp',
		//MP4格式
		'18' => 'flv',
		'22' => 'flv',
		'37' => 'flv',
		'38' => 'flv',
		'82' => 'flv',
		'83' => 'flv',
		'84' => 'flv',
		'85' => 'flv',
		//WEBM格式		
		'34' => 'webm',
		'37' => 'webm',
		'38' => 'webm',
		'82' => 'webm',
		'83' => 'webm',
		'84' => 'webm',
		'85' => 'webm'
	);
	//构造函数
	public function __construct($parama,$quality = '0'){
		if(preg_match('/youtube.com/',$parama)){
			$this->url = $parama;
			$this->vid = $this->getVidByUrl();
		}else{
			$this->vid = $parama;
			$this->url = $this->getUrlByVid();
		}
		$this->quality = $quality;
	}
	//获取视频信息
	private function getVideoInfo(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL,'http://www.youtube.com/get_video_info?video_id='.$this->vid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        $data = curl_exec($ch);
        curl_close($ch);
		//$data = file_get_contents("http://www.youtube.com/get_video_info?video_id={$this->vid}");
		parse_str($data,$info);
		if(@$info['status'] == 'ok') {
            return $info;
        }else
            return false;
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
	
	//获取播放地址
	private function getVurl()
	{
	    
	} 
	
	/**
	 * 获取视频数据默认返回的是一个数组，如果为true的话则返回json数据
	 * */
	public function getVideoData($isJson = false){
		$info = this->getVideoInfo();
		$data = array(
			'title' => $info['title'],				//视频标题
			'keywords' => $info['keywords'],		//视频关键词
			'seconds' => $info['length_seconds'],	//视频时间长度（单位秒）
			'thumbnails' => array(					//视频缩略图
				'big' => $info['iurlsd'],			//大图
				'small' => $info['thumbnail_url']	//小图			
			);
			'vurl' => $this->getVurl();				//视频播放地址
		);
		
		if($isJson)
			return json_encode($data);
		else
			return $data;
		
	}
	//调试信息
	public function debug()
	{
		$debugMsg = '<b><font color="red">以下是调试信息:</font></b><br/>';
		$debugMsg .= '<br/><font color="#0000ff">视频地址：</font>';
		$debugMsg .= $this->url?$this->url:'<font color="red">无法获取视频地址!</font>';
		$debugMsg .= '<br/><font color="#0000ff">视频&nbsp;ID&nbsp;：</font>';
		$debugMsg .= $this->vid?$this->vid:'<font color="red">无法获取视频ID!</font>';
		$debugMsg .= '<br/><font color="#0000ff">视频信息：</font>';
		$debugMsg .= $this->getVideoInfo()?'获取视频信息正常!':'<font color="red">视频信息获取失败，无法与Youtube进行连接!</font>';
		$debugMsg .= '<br/><font color="#0000ff">视频图片：</font>';
		$debugMsg .= $this->getThumbnails()?$this->getThumbnails():'<font color="red">无法获取视频图片!</font>';
		$debugMsg .= '<br/><font color="#0000ff">播放地址：</font>';
		$debugMsg .= $this->getVideoUrl()?getVideoUrl():'<font color="red">无法获取视频播放地址!</font>';
		return $debugMsg;
	} 
    
}
