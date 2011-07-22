<?php
class CommonUtil
{
	/**
	 * 
	 * @param $c values: 7
	 * @param $a length: 3
	 * @return [true, true, true] | => [[0, "A"], [1, "B"], [2, "C"]]
	 */
    static function getBools($c, $a) {
		$d = array($a);
		for ($b = 0; $b < $a; $b++) {
			$d[$b] = $c % 2 == 1;
			$c = $c >> 1;
		}
		return d;
    }
    
 	static function getBetRateSum(array $post) {
 		$rates=array();
 		$sums=array();
 		$head='';
 		$isMulti=false;
 		$totleSum=0;
		foreach($post as $key=>$value){
			$head = substr($key,0,4);
			if($head=='sum_'){
				$sums[substr($key,4)]=$value;
				if($value!=''){
					$totleSum+=intval($value);
				}
			}elseif($head=='rate'){
				$rates[substr($key,5)]=$value;
				$isMulti=$isMulti||(strpos($key, ',')!==false);
			}
		}
		$isMulti=$isMulti&&(count($sums)==1);
		return array('rates'=>$rates,'sums'=>$sums,'isMulti'=>$isMulti,'totleSum'=>$totleSum);
    }
    
    /**
     * 是否为多个号码
     **/
    static function isCb($a){
    	return $a == "lm" || $a == "bz" || $a == "mx" || $a == "sxl" || $a == "wsl" || $a == "dzy";
    }
    
	static function metaData(&$datas,array $meta,$multi=true){
	
		if($multi){
			$count=count($datas);
			for($i=0;$i<$count;$i++){
				$datas[$i]=self::evalMetaData($datas[$i],$meta);
			}
			return $datas;
		}else{
			return self::evalMetaData($datas,$meta);
		}
	}
	
	static function evalMetaData(array $datas,array $meta){
		if(empty($datas))
			{
				return array();
			 }
		foreach($datas as $key=>$data){
				if($meta[$key]=='bool'){
					$datas[$key]=$data>=1;
				}elseif($meta[$key]=='int'){
					$datas[$key]=intval($data);
				}elseif($meta[$key]=='float'){
					$datas[$key]=floatval($data);
				}
			}
			
			return $datas;
	}
	
	static function getCachedTerm(){
		$value=Yii::app()->cache->get('term');
		if($value===false)
		{
			$value=Yii::app()->db->createCommand("select term from tbl_site")->queryRow();
			$value=$value['term'];
		    Yii::app()->cache->set('term',$value);
		}
		return $value;
	}
	
	static function getUserStatus($userid){
		$value=Yii::app()->cache->get("u_status_$userid");
		if($value===false)
		{
			$value=0;
		}
		return $value;
	}
	
	static function getBmsx(){
		$value=Yii::app()->cache->get('bmsx');
		if($value===false)
		{
			$value=Yii::app()->db->createCommand("select bmsx from tbl_site")->queryRow();
			$value=$value['bmsx'];
		    Yii::app()->cache->set('bmsx',$value);
		}
		return $value;
	}
	
	static function getSxCode($code){
		
		return (self::getBmsx() - ($code-1) % 12 + 12) % 12;
	}
	
	static function getCachedTermStatus(){
		$value=Yii::app()->cache->mget(array('status','tmstatus','ftmstatus'));
		if($value===false||$value['status']===false)
		{
			$value=Yii::app()->db->createCommand("select status,tmstatus,ftmstatus from tbl_ltr where term='".self::getCachedTerm()."'")->queryRow();
		    Yii::app()->cache->set('status',intval($value['status']));
		    Yii::app()->cache->set('tmstatus',intval($value['tmstatus']));
		    Yii::app()->cache->set('ftmstatus',intval($value['ftmstatus']));
		}
		return $value;
	}
	
	static function runBackGround($url,$param=false,$isReturn=false){
		//$fp = fsockopen(Q::ini('appini/host'),80,$errno,$errmsg);//tcp请求
		$host=Yii::app()->getRequest()->getHostInfo();//getBaseUrl(true);
		$host=preg_replace('/http[s]?:\/\//i','',$host);//http://
	    $fp = fsockopen($host,80,$errno,$errmsg,600);//tcp请求
		if (!$fp) {
			Yii::log("$errstr ($errno)",CLogger::LEVEL_ERROR,'php');
		} else {
			//$path='/default/test';
			//fputs($fp,"GET /default/run HTTP/1.1\r\n\r\n");//GET / HTTP/1.0\nHost:www.163.com\n\n,fputs写入数据表示向网络发送数据
			if($param){
				$url=strpos($url,"?")!==false?"$url&":"$url?";
				foreach($param as $key=>$value){
					$url.="$key=".urlencode($value)."&";
				}
				$url=substr($url,0,strlen($url)-1);
			}
			fputs($fp, "GET $url HTTP/1.1\r\n");
			fputs($fp, "Host: ".$host."\r\n");
			fputs($fp, "Accept: */*\r\n");
			fputs($fp, "Referer: http://".$host."\r\n");
			fputs($fp, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)\r\n");
			fputs($fp, "Connection: Close\r\n\r\n");
			$content ='';
			if($isReturn){
				while ($str = fread($fp, 4096)){
					$content.=$str;
				}
				$content=substr($content,strpos($content,"\n",stripos($content,"Content-Type"))+3);
			}
			fclose($fp);
			return $content;
		}
	}
}
?>