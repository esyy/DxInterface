<?php
/**
 * 订票选座
 * @Author esyy   
 * @version 2.01
 * @var data 2016/5/13
 */
class DxInterface
{

	/*************测试api及授权码***********/
	 //private $authCode = "##############";//授权码
	 //private $pid = #############;//授权码
	 //private $api_url = "http://a#####################/";//API请求地址
	 
	/****正式api及授权码*****/
	
	private $authCode = '##################';//授权码
	private $pid = ###################;//授权码
	private $api_url = "http://a########################m/";
	
	private $format = 'json';//返回数据格式
	private $cineUpdateTime = "0000-00-00 00:00:00";
	
	/**
	 * ------------------------------------------------------
	 * 获取座位示意图
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @return json array
	 * ------------------------------------------------------
	 */
	public function cinema_hall($params=FALSE)
	{
		$params = $params ? $params :array(
				'cid' => 1
		);
		$url = 'cinema/halls/';
	
		return $this->request_api($params,$url);
	}
	
	/**
	 * ------------------------------------------------------
	 * 获取座位示意图
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @return json array
	 * ------------------------------------------------------
	 */
	public function hall_seats($params=FALSE)
	{
		$params = $params ? $params :array(
				'cid' => 1,
				'hall_id' => 2,
		);
		$url = 'cinema/hall-seats/';
		
		return $this->request_api($params,$url);
	}
	
	/**
	 * ------------------------------------------------------
	 * 获取座位状态
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @return json array
	 * ------------------------------------------------------
	 */
	public function seat_status($params=FALSE)
	{
		!isset($params['play_update_time']) && $params['play_update_time'] = "0000-00-00 00:00:00";
		$url = 'play/seat-status/';

		return $this->request_api($params,$url);
	}
	/**
	 * ------------------------------------------------------
	 * 获取影院放映计划
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @return json array
	 * ------------------------------------------------------
	 */
	public function cinema_plays($params=FALSE)
	{
		$params = $params ? $params :array(
				'cid' => 4,
		);
		$url = '/cinema/plays/';
	
		return $this->request_api($params,$url);
	}
	
	/**
	 * ------------------------------------------------------
	 * 获取影院的放映计划列表
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @param int play_id 放映场次id
	 * @param int seat_id 座位id（可以包含多个座位id用‘,’隔开）
	 * @param string play_update_time 场次最后更新时间（从获取影院的放映计划列表接口中获取。获取路径：获取影院的放映计划列表 ->返回结果->cineUpdateTime）
	 * @return lockFlag int
	 * ------------------------------------------------------
	 */
	public function seat_lock($params=FALSE)
	{
		$url = 'seat/lock';
	
		return $this->request_api($params,$url);
	}
	/**
	 * ------------------------------------------------------
	 * 获取影院的放映计划列表
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @param int play_id 放映场次id
	 * @param int lock_flag 锁座码
	 * @param int seat_id 座位id（可以包含多个座位id用‘,’隔开)
	 * @return lockFlag int
	 * ------------------------------------------------------
	 */
	public function seat_unlock($params=FALSE)
	{
		$url = 'seat/unlock';
	
		return $this->request_api($params,$url);
	}
	/**
	 * ------------------------------------------------------
	 * 锁定付款
	 * @param int cid 影院id
	 * @param int pid 合作伙伴id
	 * @param int play_id 放映场次id
	 * @param int seat_id 座位id（可以包含多个座位id用‘,’隔开）
	 * @param string play_update_time 场次最后更新时间（从获取影院的放映计划列表接口中获取。获取路径：获取影院的放映计划列表 ->返回结果->cineUpdateTime）
	 * @return lockFlag int
	 * ------------------------------------------------------
	 */
	public function seat_lock_buy($params=FALSE)
	{
		$url = 'seat/lock-buy';
		return $this->request_api($params,$url);
	}
	
	/**
	* ------------------------------------------------------
	* 查询可以请求的影院信息
	* @param int cid 影院id
	* @param int pid 合作伙伴id
	* @return 
	* ------------------------------------------------------
	*/
	public function partner_cinemas($params=FALSE)
	{
		$url = 'partner/cinemas';
		return $this->request_api($params,$url);
	}

	/*
	 * ------------------------------------------------------
	 * 请求API并且换返回数据
	 * @param array or string $condition
	 * @return json array
	 * ------------------------------------------------------
	 */
	private function request_api($params,$url=FALSE)
	{
		//数据格式
		$params['pid']  = $this->pid;
		$params['format']  = $this->format;
		
		ksort($params); // 参数排好序
		$authCode = $this->authCode; // 授权码
		
		//生成请求参数 cid=1&format=xml&pid=10000
		$query = urldecode(http_build_query($params));
		
		//获取参数
		$sig = md5(md5($authCode.$query) . $authCode);
		
		//添加密匙
		$params['_sig']  = $sig;
		
		$rs_json = $this->curl_post($this->api_url.$url."/",$params);
		$rs_arr = json_decode($this->gzdecode($rs_json), true);
		
		//print_r($this->api_url.$url.$query.'&_sig='.$sig);
		
		$LOG_DIR= PATH_SEPARATOR==';'?'E######api\\':'/app############i/';
		@file_put_contents($LOG_DIR.'dx_seat_api_'.date('Ymd').'.log',"\r\n".date("Y-m-d H:i:s")."_method:".$url."\r\n".$this->api_url.$url.$query.'&_sig='.$sig."\r\n".var_export($params,true).var_export($rs_arr,true).date('m-d H:i')."\r\n",FILE_APPEND);
		
		$api_url = $this->api_url.$url."/?{$query}&_sig=".$sig;

		if($rs_arr['res']['status'] != 1 || !$rs_arr['res']['data'])
		{
			return $rs_arr;
		}
		return $rs_arr['res']['data'];
	}
	
	/*
	 * ------------------------------------------------------
	 * curl post方式获取 
	 * @param array or string $condition
	 * @return json array
	 * ------------------------------------------------------
	 */
	public function curl_post($url,$params)
	{
		//file_put_contents('seat_api.txt',$url.var_export($params,true).date('m-d H:i'),FILE_APPEND);
		$header = array();
		$curlPost = $params;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$response = curl_exec($ch);
		if(curl_errno($ch)){
			print curl_error($ch);
		}
		curl_close($ch);
		return $response;
	}
	
	/*
	 * ------------------------------------------------------
	 * get方式获取
	 * @param array or string $condition
	 * @return json array
	 * ------------------------------------------------------
	 */
	public function get_contents($query,$url,$params)
	{
		$api_url = $this->api_url.$url."/?{$query}&_sig=".$params['_sig'];
		$rs_json = file_get_contents($api_url);
		return $rs_json;
	}

	function gzdecode ($data) {
		$flags = ord(substr($data, 3, 1));
		$headerlen = 10;
		$extralen = 0;
		$filenamelen = 0;
		if ($flags & 4) {
			$extralen = unpack('v' ,substr($data, 10, 2));
			$extralen = $extralen[1];
			$headerlen += 2 + $extralen;
		}
		if ($flags & 8) // Filename
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 16) // Comment
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 2) // CRC at end of file
			$headerlen += 2;
		$unpacked = @gzinflate(substr($data, $headerlen));
		if ($unpacked === FALSE)
			$unpacked = $data;
		return $unpacked;
	}
}
