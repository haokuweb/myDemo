<?php
class BetService // extends CUserIdentity
{
	private $term;
    public function settle($term)
    {
    	$this->term=$term;
    	
    	$conn=Yii::app()->db;
		
		$msg="";
		$ltrRec=$conn->createCommand("select term as id,settling,status from  tbl_ltr where term=:term")->queryRow(true,array(":term"=>$this->term));
		
    	if($ltrRec==false){
			$msg="期数不对，请重新登录再试";
			$ltrRec=array("id"=>$this->term,"settling"=>false,"status"=>0);
		}elseif($ltrRec['status']==0){
			$msg="本期还未开盘";
		}
    	elseif($ltrRec['settling']==1){
			$msg="结账中,请稍等";
		}
    	elseif($ltrRec['status']==2){
			$msg="已结帐，无法重复结账";
		}
		if(strlen($msg)>0){
			CommonUtil::metaData($ltrRec,array("id"=>'',"settling"=>'bool',"status"=>'int'),false);
			return CJSON::encode(array("ltr"=>$ltrRec,"msg"=>$msg,"success"=>false));
		}
		
		$codes=$conn->createCommand("select zm1,zm2,zm3,zm4,zm5,zm6,tm from tbl_ltr_codes where term=:term")->queryRow(true,array(":term"=>$this->term));
		
		foreach($codes as $code){
			if($code==null||strlen($code)<2){
				CommonUtil::metaData($ltrRec,array('','bool','int'));
				return CJSON::encode(array("ltr"=>$ltrRec,"msg"=>"有号码为空","success"=>false));
			}
		}
		
		$conn->createCommand("update tbl_ltr set settling=1 where term='{$this->term}'")->execute();
		
		/*$dataReader=$conn->createCommand("select id,betSum,ltrBtcId,ltrTypeId,betType,betCode,rate0,rate1,rebate from tbl_bet where termid=:term")->query(array(":term"=>$this->term));
		
		while(($bet=$dataReader->read())!==false) {
			$this->isBetWin($bet['id'],$bet['betSum'],$bet['ltrBtcId'],$bet['ltrTypeId'],$bet['betType'],$bet['betCode'],$bet['rate0'],$bet['rate1'],$bet['rebate'],$codes);
		}*/
		
		$this->settleType($term);
		
		//update bet		
		//update term
		Yii::app()->db->createCommand("update tbl_ltr set settling=0,status=2,tmstatus=1,ftmstatus=1 where term='{$this->term}'")->execute();
		Yii::app()->cache->set('status',2);
		Yii::app()->cache->set('tmstatus',1);
		Yii::app()->cache->set('ftmstatus',1);
		Yii::log("backgroud settle success",CLogger::LEVEL_ERROR,'php');
    }
    
    public function settleType($term)
    {
    	$conn=Yii::app()->db;
    	$codes=$conn->createCommand("select zm1,zm2,zm3,zm4,zm5,zm6,tm from tbl_ltr_codes where term=:term")->queryRow(true,array(":term"=>$term));
    	$sql="update tbl_ltr_type set bet=:bet,win=:win where btc=:btc";
    	//$typeCmd=$conn->createCommand("update tbl_ltr_type set bet=:bet,win=:win where btc=:btc");

    	$intCode=array();
    	$sxCode=array();
    	foreach($codes as $k=>$v){
    		$intCode[$k]=intval($v);
    		$sxCode[$k]=CommonUtil::getSxCode(intval($v));
    	}
    	$btcArrs=array();
    	
    	//tm
    	$r=$intCode['tm']-1;
    	$conn->createCommand($sql." and (id='tm_0_{$r}' or id='tm_1_{$r}')")->execute(array(":bet"=>0,":win"=>1,"btc"=>"tm"));
    	$conn->createCommand($sql." and (id<>'tm_0_{$r}' and id<>'tm_1_{$r}')")->execute(array(":bet"=>0,":win"=>0,"btc"=>"tm"));
    	$btcArrs[]="'tm'";
    	
    	//tmds
    	if($codes['tm']=='49'){
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"tmds"));
    	}else{
    		$r=$intCode['tm']%2;//48: 0  双： _1
    		$conn->createCommand($sql." and id='tmds_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"tmds"));
    		$conn->createCommand($sql." and id='tmds_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"tmds"));
    	}
    	$btcArrs[]="'tmds'";
    	
    	//tmdx
    	if($codes['tm']=='49'){
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"tmdx"));
    	}else{
    		$r=$intCode['tm']>24?1:0;//24
    		$conn->createCommand($sql." and id='tmdx_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"tmdx"));
    		$conn->createCommand($sql." and id='tmdx_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"tmdx"));
    	}
    	$btcArrs[]="'tmdx'";
    	
    	//tmhsds
    	if($codes['tm']=='49'){
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"tmhsds"));
    	}else{
    		$r=substr($codes['tm'],0,1)+substr($codes['tm'],1,1);
    		$r=$r%2;//24
    		$conn->createCommand($sql." and id='tmhsds_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"tmhsds"));
    		$conn->createCommand($sql." and id='tmhsds_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"tmhsds"));
    	}
    	$btcArrs[]="'tmhsds'";
    	
    	//tmwsdx
    	if($codes['tm']=='49'){
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"tmwsdx"));
    	}else{
    		$r=$intCode['tm']%10>4?1:0;
    		$conn->createCommand($sql." and id='tmwsdx_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"tmwsdx"));
    		$conn->createCommand($sql." and id='tmwsdx_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"tmwsdx"));
    	}
    	$btcArrs[]="'tmwsdx'";
    	
   		 //tmqs
   		 //	  DECLARE t_sx_jq varchar(15) default '11,7,10,6,9,1';
	     //DECLARE t_sx_ys varchar(15) default '0,8,4,3,2,5';
	    $jqCodes=array(11,7,10,6,9,1);
    	if($codes['tm']=='49'){
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"tmqs"));
    	}else{
    		$r=in_array($sxCode['tm'],$jqCodes)?1:0;
    		$conn->createCommand($sql." and id='tmqs_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"tmqs"));
    		$conn->createCommand($sql." and id='tmqs_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"tmqs"));
    	}
    	$btcArrs[]="'tmqs'";
    	
    	//tmsb
    	$sbs=array(0, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2 );
		$r=$sbs[$intCode['tm']-1];
		
		$conn->createCommand($sql." and id='tmsb_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"tmsb"));
    	$conn->createCommand($sql." and id<>'tmsb_$r'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"tmsb"));
    	
    	$btcArrs[]="'tmsb'";
    	
    	//zm
   	    $r=array();
			for($i=1;$i<=6;$i++){
				$r[]="'zm_0_".($intCode["zm$i"]-1)."'";
				$r[]="'zm_1_".($intCode["zm$i"]-1)."'";
			}
		$r=join(',',$r);
		$conn->createCommand($sql." and id in ( $r )")->execute(array(":bet"=>0,":win"=>1,"btc"=>"zm"));
    	$conn->createCommand($sql." and id not in ( $r )")->execute(array(":bet"=>0,":win"=>0,"btc"=>"zm"));
    	$btcArrs[]="'zm'";
    	
    	$zhsum=0;
			for($i=1;$i<=6;$i++){
				$zhsum+=intval($intCode["zm$i"]);
			}
		$zhsum+=intval($intCode["tm"]);
		
		//zhds
    	$r=$zhsum%2;//48: 0  双： _1
    	$conn->createCommand($sql." and id='zhds_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zhds"));
    	$conn->createCommand($sql." and id='zhds_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zhds"));
    	$btcArrs[]="'zhds'";
    	
    	//zhdx
    	$r=$zhsum>174?1:0;
    	$conn->createCommand($sql." and id='zhdx_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zhdx"));
    	$conn->createCommand($sql." and id='zhdx_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zhdx"));
    	$btcArrs[]="'zhdx'";
    	
    	//tx
    	$r=$sxCode['tm'];
		
		$conn->createCommand($sql." and id='tx_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"tx"));
    	$conn->createCommand($sql." and id<>'tx_$r'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"tx"));
    	$btcArrs[]="'tx'";
    	
    	//bb
    	if($codes['tm']=='49'){
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"bbds"));
    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"bbdx"));
    	}else{
    		$r=$sbs[$intCode['tm']-1]*2+1-$intCode['tm']%2;
    		$conn->createCommand($sql." and id='bbds_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"bbds"));
    		$conn->createCommand($sql." and id<>'bbds_$r'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"bbds"));
    		
    		$r=$sbs[$intCode['tm']-1]*2+$intCode['tm']>24?0:1;
    		$conn->createCommand($sql." and id='bbdx_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"bbdx"));
    		$conn->createCommand($sql." and id<>'bbdx_$r'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"bbdx"));
    	}
    	$btcArrs[]="'bbds'";
    	$btcArrs[]="'bbdx'";
    	
    	//sx
    	$r=array();
			foreach($sxCode as $codeVal){
				$r[]="'sx_".($codeVal)."'";
			}
		$r=join(',',$r);
		$conn->createCommand($sql." and id in ( $r )")->execute(array(":bet"=>0,":win"=>1,"btc"=>"sx"));
    	$conn->createCommand($sql." and id not in ( $r )")->execute(array(":bet"=>0,":win"=>0,"btc"=>"sx"));
    	$btcArrs[]="'sx'";
    	
    	//ws
    	$r=array();
			foreach($intCode as $codeVal){
				$r[]="'ws_".($codeVal%10)."'";
			}
		$r=join(',',$r);
		$conn->createCommand($sql." and id in ( $r )")->execute(array(":bet"=>0,":win"=>1,"btc"=>"ws"));
    	$conn->createCommand($sql." and id not in ( $r )")->execute(array(":bet"=>0,":win"=>0,"btc"=>"ws"));
    	$btcArrs[]="'ws'";
    	
    	//zt_0...5
    	for($i=0;$i<=5;$i++){
    		$r=$intCode["zm".($i+1)]-1;
    		$conn->createCommand($sql." and id='zt_{$i}_{$r}'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"zt_$i"));
    		$conn->createCommand($sql." and id<>'zt_{$i}_{$r}'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"zt_$i"));
    		$btcArrs[]="'zt_$i'";
    		
    		//tmds
	    	if($codes["zm".($i+1)]=='49'){
	    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"zt_{$i}_ds"));
	    	}else{
	    		$r=$intCode["zm".($i+1)]%2;//48: 0  双： _1
	    		$conn->createCommand($sql." and id='zt_{$i}_ds_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zt_{$i}_ds"));
	    		$conn->createCommand($sql." and id='zt_{$i}_ds_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zt_{$i}ds"));
	    	}
	    	$btcArrs[]="'zt_{$i}_ds'";
	    	
	    	//tmdx
	    	if($codes["zm".($i+1)]=='49'){
	    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"zt_{$i}_dx"));
	    	}else{
	    		$r=$intCode["zm".($i+1)]>24?1:0;//24
	    		$conn->createCommand($sql." and id='zt_{$i}_dx_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zt_{$i}_dx"));
	    		$conn->createCommand($sql." and id='zt_{$i}_dx_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zt_{$i}_dx"));
	    	}
	    	$btcArrs[]="'zt_{$i}_dx'";
	    	
	    	//tmhsds
	    	if($codes["zm".($i+1)]=='49'){
	    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"zt_{$i}_hsds"));
	    	}else{
	    		$r=substr($codes["zm".($i+1)],0,1)+substr($codes["zm".($i+1)],1,1);
	    		$r=$r%2;//24
	    		$conn->createCommand($sql." and id='zt_{$i}_hsds_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zt_{$i}_hsds"));
	    		$conn->createCommand($sql." and id='zt_{$i}_hsds_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zt_{$i}_hsds"));
	    	}
	    	$btcArrs[]="'zt_{$i}_hsds'";
	    	
	    	//tmwsdx
	    	if($codes["zm".($i+1)]=='49'){
	    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"zt_{$i}_wsdx"));
	    	}else{
	    		$r=$intCode["zm".($i+1)]%10>4?1:0;
	    		$conn->createCommand($sql." and id='zt_{$i}_wsdx_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zt_{$i}_wsdx"));
	    		$conn->createCommand($sql." and id='zt_{$i}_wsdx_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zt_{$i}_wsdx"));
	    	}
	    	$btcArrs[]="'zt_{$i}_wsdx'";
	    	
	   		 //tmqs
	   		 //	  DECLARE t_sx_jq varchar(15) default '11,7,10,6,9,1';
		     //DECLARE t_sx_ys varchar(15) default '0,8,4,3,2,5';
	    	if($codes["zm".($i+1)]=='49'){
	    		$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>2,"btc"=>"zt_{$i}_qs"));
	    	}else{
	    		$r=in_array($sxCode["zm".($i+1)],$jqCodes)?1:0;
	    		$conn->createCommand($sql." and id='zt_{$i}_qs_0'")->execute(array(":bet"=>0,":win"=>$r,"btc"=>"zt_{$i}_qs"));
	    		$conn->createCommand($sql." and id='zt_{$i}_qs_1'")->execute(array(":bet"=>0,":win"=>1-$r,"btc"=>"zt_{$i}_qs"));
	    	}
	    	$btcArrs[]="'zt_{$i}_qs'";
	    	
	    	//tmsb
			$r=$sbs[$intCode["zm".($i+1)]-1];
			
			$conn->createCommand($sql." and id='zt_{$i}_sb_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"zt_{$i}_sb"));
	    	$conn->createCommand($sql." and id<>'zt_{$i}_sb_$r'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"zt_{$i}_sb"));
	    	$btcArrs[]="'zt_{$i}_sb'";
    	}
    	
    	//qsb
    	$sb=array(0,0,0);
    	for($i=1;$i<=6;$i++){
				$sb[$sbs[$intCode['zm'.$i]-1]]+=1;
			}
		$isHeju=false;
		foreach($sb as $s){
			if($s==0){
				$isHeju=true;
				break;
			}
		}
		if($isHeju){
			$conn->createCommand($sql." and id='qsb_3'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"qsb"));
    		$conn->createCommand($sql." and id<>'qsb_3'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"qsb"));
		}else{
			$sb[$sbs[$intCode['tm']-1]]+=1.5;
			$r=max($sb);
			for($i=0;$i<count($sb);$i++){
				if($r==$sb[$i]){
					$r=$i;
					break;
				}
			}
			$conn->createCommand($sql." and id='qsb_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"qsb"));
    		$conn->createCommand($sql." and id<>'qsb_$r'")->execute(array(":bet"=>0,":win"=>0,"btc"=>"qsb"));
		}
		$btcArrs[]="'qsb'";
    	
    	//zx
    	$zx=array();
    	foreach($intCode as $codeV){
    		if($codeV!=49){
				$zx[$codeV%12]=1;
    		}
		}
		$zxN=count($zx);
		$r=-1;
    	switch ($zxN) {
		    case 0:
		    case 1:
		    	$r=-1;
		        break;
		    case 2:
		    case 3: 
		    case 4:
		        $r=0;
		        break;
		    case 5:
		        $r=1;
		        break;
		    case 6:
		        $r=2;
		        break;
		    case 7:
		        $r=3;
		        break;
		    default:
		        $r=-1;
		}
		
		if($r==-1){
			$conn->createCommand($sql)->execute(array(":bet"=>0,":win"=>0,"btc"=>"zx"));
		}else{
			//1-2%2+4  zx_5 shuang
			$conn->createCommand($sql." and (id='zx_$r' or id='zx_".(1-$zxN%2+4)."')")->execute(array(":bet"=>0,":win"=>1,"btc"=>"zx"));
	    	$conn->createCommand($sql." and (id<>'zx_$r' and id<>'zx_".(1-$zxN%2+4)."')")->execute(array(":bet"=>0,":win"=>0,"btc"=>"zx"));
		}
		$btcArrs[]="'zx'";
    	
    	//qm
    	//有多少个单、双、大、小、合单、合双数
    	$qm=array(0,0,0,0,0,0);//vals:01234567
    	foreach($intCode as $codeV){
    		if($codeV==49){
				continue;
    		}
    		$qm[1-$codeV%2]+=1;
    		$qm[2+($codeV>24?0:1)]+=1;
    		$r=$codeV%10+floor($codeV/10);
    		$qm[1-($r%2)+4]+=1;
		}
		for($i=0;$i<6;$i++){
			$conn->createCommand($sql." and id='qm_{$qm[$i]}_$i'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"qm_{$qm[$i]}"));
		}
		for($i=0;$i<8;$i++){
			$btcArrs[]="'qm_$i'";
		}

		//sx
		//$sxWin=array();
		foreach($intCode as $codeV){
			$r=CommonUtil::getSxCode($codeV);
			$conn->createCommand($sql." and id='sx_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"sx"));
		}
		$btcArrs[]="'sx'";
		
		//ws
    	foreach($intCode as $codeV){
			$r=$codeV%10;
			$conn->createCommand($sql." and id='ws_$r'")->execute(array(":bet"=>0,":win"=>1,"btc"=>"ws"));
		}
		$btcArrs[]="'ws'";
    	
    	$btcs=join(',',$btcArrs);
    	$conn->createCommand("update tbl_bet b set b.victory=(select t.win from tbl_ltr_type t where t.id=b.ltrTypeId) where  termid='$term' and ltrBtcId in ($btcs)")->execute();
    	$conn->createCommand("update tbl_bet b set b.hyResult=if(victory=2,0,if(victory=1,betSum*rate0-(1-rebate/100)*betSum,-(1-rebate/100)*betSum)) where  termid='$term' and ltrBtcId in ($btcs)")->execute();
    	
    	$conn->createCommand("call sp_settle('$term')")->execute();
		return '';
    }
    
    
	public function isBetWin($betid,$betSum,$btc,$typeid,$typeName,$betCode,$rate0,$rate1,$rebate,$codes){
		$victory=0;
		$rate=$rate0;
		$hyResult=0;
		if($btc=='tm'){
			//get code
			$code=str_pad($betCode+1, 2, "0", STR_PAD_LEFT);
				if($codes['tm']==$code){
					$victory=1;
				}
		}elseif($btc=='tmds'){
			//$betCode 0 特单   1 特双  49 除外
			if($codes['tm']==49){
				$victory=2;
			}elseif(intval($codes['tm'])%2==1-$betCode){
				$victory=1;
			}
		}elseif($btc=='tmdx'){
			//get code
			if($codes['tm']==49){
				$victory=2;
			}elseif($betCode==0&&intval($codes['tm'])>24){
				$victory=1;
			}elseif($betCode==1&&intval($codes['tm'])<=24){
				$victory=1;
			}
		}elseif($btc=='tmhsds'){
			//开出的特码的个位数和十位数之和为单数
			$sum=substr($codes['tm'],0,1)+substr($codes['tm'],1,1);
			if($codes['tm']==49){
				$victory=2;
			}elseif($sum%2==1-$betCode){
				$victory=1;
			}
		}elseif($btc=='tmwsdx'){
			//开出的特码的尾数为5到9,49算和局
			$code=substr($codes['tm'],1,1);
			if($codes['tm']==49){
				$victory=2;
			}elseif($betCode==0&&$code>4){
				$victory=1;
			}elseif($betCode==1&&$code<=4){
				$victory=1;
			}
		}elseif($btc=='tmqs'){
			//$betCode=0 家禽 开出之特码生肖为猪,羊,狗,马,鸡,牛,49算和局
			//$betCode=1野兽 开出之特码生肖为鼠,猴,龙,兔,虎,蛇。49算和局
			$code=substr($codes['tm'],1,1);
			if($codes['tm']==49){
				$victory=2;
			}elseif($betCode==0&&$code>4){
				$victory=1;
			}elseif($betCode==1&&$code<=4){
				$victory=1;
			}
		}elseif($btc=='tmsb'){
			//get code
			$sbs=array(0, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2 );
			//Ltr.Code.sbColors = [ "red", "blue", "green" ];
			$code=intval($codes['tm'])-1;
			if($sbs[$code]==$betCode){
				$victory=1;
			}
		}elseif($btc=='zm'){
			//get code
			$code=str_pad($betCode+1, 2, "0", STR_PAD_LEFT);
			for($i=1;$i<=6;$i++){
				if($codes["zm$i"]==$code){
					$victory=1;
					break;
				}
			}
		}elseif($btc=='zhds'){
			//get code
			$code=0;
			for($i=1;$i<=6;$i++){
				$code+=intval($codes["zm$i"]);
			}
			$code+=intval($codes["tm"]);
			if($code%2==1-$betCode){
				$victory=1;
			}
		}elseif($btc=='zhdx'){
			//get code
			$code=0;
			for($i=1;$i<=6;$i++){
				$code+=intval($codes["zm$i"]);
			}
			$code+=intval($codes["tm"]);
			if($betCode==0&&$code>174){
				$victory=1;
			}elseif($betCode==1&&$code<=174){
				$victory=1;
			}
			
		}elseif($btc=='lm'){
			//10,11,12 or 10,11
			$count=0;
			for($i=1;$i<=6;$i++){
				if(strpos(','.$betCode.',',','.(intval($codes["zm$i"])-1).',')!==false){
					$count++;
				}
			}
			if($typeid=='lm_0'){
				//中二：下注的号码有且仅有2个号码出现在正码里面 
				//中三：下注的三个号码全都出现在正码里面 				
				if($count==2){
					$victory=1;
				}elseif($count==3){
					$victory=1;
					$rate=$rate1;
				}
			}elseif($typeid=='lm_1'){
				if($count==3){
					$victory=1;
				}
			}elseif($typeid=='lm_2'){
				if($count==2){
					$victory=1;
				}
			}elseif($typeid=='lm_3'){
				//中二：下注的二个号码全都出现在6个正码里面，
				//中特：下注的二个号码一个出现在正码里面，一个出现在特码里面
				if($count==2){
					$victory=1;
				}elseif($count==1){
					$codesArr=split(',',$betCode);
					foreach($codesArr as $codeRow){
					if($codeRow+1==intval($codes['tm'])){
						$victory=1;
						$rate=$rate1;
						break;
					}
					}
				}
			}elseif($typeid=='lm_4'){
				//下注的二个号码一个出现在正码里面，一个出现在特码里面
				if($count==1){
					$codesArr=split(',',$betCode);
					foreach($codesArr as $codeRow){
					if($codeRow+1==intval($codes['tm'])){
						$victory=1;
						$rate=$rate1;
						break;
					}
					}
				}
			}
			
		}elseif($btc=='tx'){
			//选一个生肖  开出的特码  开出的特码属于下注的生肖 
			$code=str_pad($betCode+1, 2, "0", STR_PAD_LEFT);
			if($codes['tm']==$code){
				$victory=1;
			}
		}elseif($btc=='tmds,tmdx,tmhsds,tmwsdx,tmqs,zhds,zhdx,ztds,ztdx,ztds,ztdx,zthsds,ztwsdx,ztqs'){
			//get code
			$code=str_pad($betCode+1, 2, "0", STR_PAD_LEFT);
			if($codes['tm']==$code){
				$victory=1;
			}
		}elseif($btc=='tmds,tmdx,tmhsds,tmwsdx,tmqs,zhds,zhdx,ztds,ztdx,ztds,ztdx,zthsds,ztwsdx,ztqs'){
			//get code
			$code=str_pad($betCode+1, 2, "0", STR_PAD_LEFT);
			if($codes['tm']==$code){
				$victory=1;
			}
		}elseif($btc=='tmds,tmdx,tmhsds,tmwsdx,tmqs,zhds,zhdx,ztds,ztdx,ztds,ztdx,zthsds,ztwsdx,ztqs'){
			//get code
			$code=str_pad($betCode+1, 2, "0", STR_PAD_LEFT);
			if($codes['tm']==$code){
				$victory=1;
			}
		}
		
		if($victory==1){
			$hyResult=$betSum*$rate-(1-$rebate/100)*$betSum;// b*s  -b+ t/100*b  b(r+t/100-1)
		}elseif($victory==0){
			$hyResult=-$betSum;
		}elseif($victory==2){
			$hyResult=0;
		}
		Yii::app()->db->createCommand("update tbl_bet set hyResult=$hyResult,victory=$victory where id=$betid")->execute();
    }
}
?>