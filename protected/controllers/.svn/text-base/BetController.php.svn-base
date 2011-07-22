<?php

/**
 * 下注单
 * 
 * betSum
	8
 
betTime
	"2011-02-06 12:16:48"
	
betType
	"特码A49"
	
betUserId
	"xcf6"
	
betUserName
	"会员测试"
	
bhUserId
	null

bhUserName
	null

hyResult
	-6.920000076293945

id
	88904

ltrTypeId
	"tm_0_48"
	
ltrTypeName
	"特码A49"
	
prorate_0
	0.9

prorate_1
	0.05

prorate_2
	0

prorate_3
	0
	
prorate_4
	0.05
	
rate
	"42.3"
	
role
	1
	
statUserId
	"xcf2"
	
statUserName
	"新财富"
	
victory
	false
 * @author kyle
 *
 */
class BetController extends Controller
{
	/**
	 * notTeAutoCloseTime	20:58 正码自动关盘
		resultDate	2011-02-10 开奖日期
		resultTime	21:00 开奖时间
		subTeAutoCloseTime	21:00 附特自动关盘
		teAutoCloseTime	21:03 特码自动关盘
		term	3  期数 
		{"ltr":{"codes":["11","13","22","12","08","18","01"],"id":2011002,"notTeAutoCloseTime":"20:58","resultDate":"2011-02-11","resultTime":"21:00","settling":false,"status":2,"subTeAutoCloseTime":"21:00","teAutoCloseTime":"21:03"}}
	 * @return unknown_type
	 */
	public function actionAddltr()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$sql="insert into tbl_ltr(term,notTeAutoCloseTime,resultDate,resultTime,subTeAutoCloseTime,teAutoCloseTime) values (:term,:notTeAutoCloseTime,:resultDate,:resultTime,:subTeAutoCloseTime,:teAutoCloseTime)";
		$conn=Yii::app()->db;
		$command=$conn->createCommand($sql);
		/*$site=Yii::app()->user->site;
		$termRec=$conn->createCommand("select status from tbl_ltr where term='{$site['term']}'")->queryRow();
		if($termRec['status']!=2){
			echo '{"msg":"当前期次还未结账","success":false}';
			Yii::app()->end(); 
		}*/
		$term=$_POST['term'];
		if($term!=null){
			try{
				$term=(date('Y')).str_pad($term, 3, "0", STR_PAD_LEFT);
				$command->bindParam(":term",$term,PDO::PARAM_STR);
				$command->bindParam(":notTeAutoCloseTime",$_POST['notTeAutoCloseTime'],PDO::PARAM_STR);
				$command->bindParam(":resultDate",$_POST['resultDate'],PDO::PARAM_STR);
				$command->bindParam(":resultTime",$_POST['resultTime'],PDO::PARAM_STR);
				$command->bindParam(":subTeAutoCloseTime",$_POST['subTeAutoCloseTime'],PDO::PARAM_STR);
				$command->bindParam(":teAutoCloseTime",$_POST['teAutoCloseTime'],PDO::PARAM_STR);
				$command->execute();
			
				$conn->createCommand("call resetLtrType()")->execute();
				//Yii::app()->db->createCommand($sql)->execute();
				//special: 本命特肖 txbx 特肖本命 txfbx  type='tx'   , sxbx 本命生肖 sxfbx 非本命  type='sx', ws0 0尾  wsf0 非零尾  type='ws'
				//$conn->createCommand("update tbl_adjrate set rate00=0,rate01=0,rate02=0,rate10=0,rate11=0,rate12=0")->execute();
				$conn->createCommand("update tbl_site set term='$term'")->execute();
				Yii::app()->cache->set('term',$term);
				Yii::app()->cache->set('status',0);
				Yii::app()->cache->set('tmstatus',0);
				Yii::app()->cache->set('ftmstatus',0);
				echo '{"msg":"","success":true}';
			}catch (Exception $e) {
				echo '{"msg":"期次已存在或填写错误","success":false}';
			}
		}
		Yii::app()->end(); 
	}
	
	/**
	 * btcs	tm,tmds,tmdx,tmhsds,tmsb,tmwsdx,tmqs
	 * or type= 0 1 
	 * or   btc	tm
			idx	6
	 * open	false
	 * @return unknown_type
	 */
	public function actionOpenclose()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$status=CommonUtil::getCachedTermStatus();
		if($status['status']==2){
			echo '{"msg":"已经结账，还没有新建期次,无法更改","success":false}';
			Yii::app()->end(); 
		}
		
		$conn=Yii::app()->db;
		$ltrRec=$conn->createCommand("select resultDate,notTeAutoCloseTime,teAutoCloseTime from tbl_ltr where term='".$this->term."'")->queryRow();
		$notTeAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['notTeAutoCloseTime'])-time();
		$teAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['teAutoCloseTime'])-time();
		
		$isopen=$_POST['open']=='true'?1:0;
		/*if(isset($_POST['btcs'])){
			$where= " `btc` in ('".str_replace(",","','",$_POST['btcs'])."')";
			$conn->createCommand("update tbl_ltr_type set open=:open where ".$where)->execute(array(":open"=>$isopen));
		}elseif(isset($_POST['type'])){
			$type=$_POST['type']==1?'tmstatus':$_POST['type']==3?'ftmstatus':'status';			
			if($type=='status'){
				$n=$conn->createCommand("update tbl_ltr set status=$isopen,tmstatus=$isopen+1,ftmstatus=$isopen+1 where term='{$this->term}'")->execute();
				Yii::app()->cache->set('status',$isopen);
				Yii::app()->cache->set('tmstatus',$isopen+1);
				Yii::app()->cache->set('ftmstatus',$isopen+1);
			}else{
				$isopen=$isopen+1;
				$n=$conn->createCommand("update tbl_ltr set $type=$isopen where term='{$this->term}'")->execute();
				Yii::app()->cache->set($type,$isopen);
				if($isopen==2){
					Yii::app()->cache->set('status',1);
					$n=$conn->createCommand("update tbl_ltr set status=1 where term='{$this->term}'")->execute();
				}
			}

		}*/
		
		if(isset($_POST['btcs'])){
			$where= " `btc` in ('".str_replace(",","','",$_POST['btcs'])."')";
			$conn->createCommand("update tbl_ltr_type set open=:open where ".$where)->execute(array(":open"=>$isopen));
		}elseif(isset($_POST['type'])){
			$where=($_POST['type']=='1')?" btc in ('tm','tmds','tmdx','tmhsds','tmsb','tmwsdx','tmqs','tx','bbds','bbdx','mx')":($_POST['type']=='3'?" btc not in ('tm','tmds','tmdx','tmhsds','tmsb','tmwsdx','tmqs','tx','bbds','bbdx','mx')":' 1=1');
			//echo $_POST['type'].' '.$isopen.' '.$where;
			$conn->createCommand("update tbl_ltr_type set open=:open where ".$where)->execute(array(":open"=>$isopen));
		}elseif(isset($_POST['btc'])){
			$where='';
			if($_POST['btc']=='tm'||$_POST['btc']=='zm'){
				$where= " `id`='{$_POST['btc']}_0_{$_POST['idx']}' or `id`='{$_POST['btc']}_1_{$_POST['idx']}'";
			}else{
				$where= " `id`='{$_POST['btc']}_{$_POST['idx']}'";
			}
			$conn->createCommand("update tbl_ltr_type set open=:open where ".$where)->execute(array(":open"=>$isopen));
		}
		
		if($isopen==1){
			Yii::app()->cache->set('status',1);
			$n=$conn->createCommand("update tbl_ltr set status=1 where term='{$this->term}'")->execute();
		}
		
		echo '{"msg":"","success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * btcs	tm,tmds,tmdx,tmhsds,tmsb,tmwsdx,tmqs,open	false
	 * @return unknown_type
	 */
	public function actionInputCode()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$codes=split(',',$_POST['codes']);
		$code[0]=$codes[0]==''?null:str_pad($codes[0], 2, "0", STR_PAD_LEFT);
		$code[1]=$codes[1]==''?null:str_pad($codes[1], 2, "0", STR_PAD_LEFT);
		$code[2]=$codes[2]==''?null:str_pad($codes[2], 2, "0", STR_PAD_LEFT);
		$code[3]=$codes[3]==''?null:str_pad($codes[3], 2, "0", STR_PAD_LEFT);
		$code[4]=$codes[4]==''?null:str_pad($codes[4], 2, "0", STR_PAD_LEFT);
		$code[5]=$codes[5]==''?null:str_pad($codes[5], 2, "0", STR_PAD_LEFT);
		$code[6]=$codes[6]==''?null:str_pad($codes[6], 2, "0", STR_PAD_LEFT);
		
		$tmp=array();
		$msg='';
		foreach($codes as $v){
			if($v=='') continue;
			if(intval($v)<0||intval($v)>49){
				$msg='号码必须在0和49之间';
				break;
			}
			if(isset($tmp[$v])){
				$msg='号码不能重复';
				break;
			}else{
				$tmp[$v]=0;
			}
		}
		
		if($msg!=''){
			echo CJSON::encode(array("codes"=>$code,"success"=>false,"msg"=>$msg));
			Yii::app()->end(); 
		}
		
		$command=Yii::app()->db->createCommand("replace into tbl_ltr_codes values (:term,:zm1,:zm2,:zm3,:zm4,:zm5,:zm6,:tm)");
		$command->bindParam(":term",$this->term,PDO::PARAM_STR);
		$command->bindParam(":zm1",str_pad($codes[0], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->bindParam(":zm2",str_pad($codes[1], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->bindParam(":zm3",str_pad($codes[2], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->bindParam(":zm4",str_pad($codes[3], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->bindParam(":zm5",str_pad($codes[4], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->bindParam(":zm6",str_pad($codes[5], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->bindParam(":tm",str_pad($codes[6], 2, "0", STR_PAD_LEFT),PDO::PARAM_STR);
		$command->execute();
		//{"codes":["02","03","04","05",null,null,null],"success":true}
		echo CJSON::encode(array("codes"=>$code,"success"=>true));
		Yii::app()->end(); 
	}

	/**
	 * rate_21,22,33	620
		rate_21,22,43	620
		rate_21,33,43	620
		rate_22,33,43	620
		sum_lm_1	6
		
		rate_30,31,32	21,90
		sum_lm_0	3
		
		need update tbl_stat,tbl_ltr_type[win,bet]  A B C ? 实货虚货?
		
		bhuser	bx999
	 * @return unknown_type
	 */
    public function actionTz(){
    	$this->layout=false;
		header('Content-type: application/json');
		
		//$posts=CommonUtil::getBetRateSum($_POST);
    	/*$openStatus=0;
				if($termStatus['status']!=2){
					if(strpos("'tm','tmds','tmdx','tmhsds','tmsb','tmwsdx','tmqs','tx','bbds','bbdx','mx'","'{$btc['btc']}'")!==false){
						if($teAutoRemainTime>0){
							$openStatus=intval($btc['defaultopen']);
						}
					}else{
						if($notTeAutoRemainTime>0){
							$openStatus=intval($btc['defaultopen']);
						}
					}
				}*/
		$msg='';
		$conn=Yii::app()->db;
		$userid=Yii::app()->user->id;
		$roleid=Yii::app()->user->role;
		$lei=isset($_POST['lei'])?intval($_POST['lei']):Yii::app()->user->getState('lei');
		$vNull=NULL;
		$findRateCmd=$conn->createCommand("select t.*,(SELECT IF(r.open=0,0,t.open) FROM tbl_default_rate r WHERE r.id=t.type OR (r.id='txbx' AND t.type='tx' AND t.id='tx_1') OR (r.id='txfbx' AND t.type='tx' AND t.id<>'tx_1') OR (r.id='sxbx' AND t.type='sx' AND t.id='sx_1') OR (r.id='sxfbx' AND t.type='sx' AND t.id<>'sx_1') OR (r.id='ws0' AND t.type='ws' AND t.id='ws_0') OR (r.id='wsf0' AND t.type='ws' AND t.id<>'ws_0')) AS defaultopen from tbl_ltr_type t where t.`id`=:id");
		
		$ltrRec=$conn->createCommand("select resultDate,notTeAutoCloseTime,teAutoCloseTime from tbl_ltr where term='".$this->term."'")->queryRow();
		$notTeAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['notTeAutoCloseTime'])-time();
		$teAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['teAutoCloseTime'])-time();
		
		$openStatus=0;
		$termStatus=CommonUtil::getCachedTermStatus();
		$typeRecs=array();
		$posts=array();
		if($termStatus['status']!=2){
			$rates=array();
	 		$sums=array();
	 		$head='';
	 		$isMulti=false;
	 		$totleSum=0;
			foreach($_POST as $key=>$value){
				$head = substr($key,0,4);
				if($head=='sum_'){
					if($value!=''){
						$value=intval($value);
						if($value<=0){
							continue;
						}
						$sKey=substr($key,4);
						$totleSum+=$value;
						$typeRec=$findRateCmd->queryRow(true,array(":id"=>$sKey));
						if($typeRec===null||$typeRec===false){
							continue;
						}
						$typeRecs[$sKey]=$typeRec;
						if(strpos("'tm','tmds','tmdx','tmhsds','tmsb','tmwsdx','tmqs','tx','bbds','bbdx','mx'","'{$typeRec['btc']}'")!==false){
							if($teAutoRemainTime>0){
								$openStatus=intval($typeRec['defaultopen']);
							}
						}else{
							if($notTeAutoRemainTime>0){
								$openStatus=intval($typeRec['defaultopen']);
							}
						}
						if($openStatus==0){
							$msg="{$typeRec['name']} 已关盘";
							break;
						}else{
							$openStatus=0;
						}
						$sums[$sKey]=$value;
					}
				}elseif($head=='rate'){
					$rates[substr($key,5)]=$value;
					$isMulti=$isMulti||(strpos($key, ',')!==false);
				}
			}
			$isMulti=$isMulti&&(count($sums)==1);
			$posts=array('rates'=>$rates,'sums'=>$sums,'isMulti'=>$isMulti,'totleSum'=>$totleSum);
		}else{
			echo CJSON::encode(array("msg"=>"已结账,不能下注","success"=>false));
			Yii::app()->end(); 
		}
		
		if($posts['totleSum']>Yii::app()->user->creditSum - Yii::app()->user->usedSum){
			echo CJSON::encode(array("msg"=>"信用额度不够","success"=>false));
			Yii::app()->end(); 
		}
		
		$autoBhMap=array('tm'=>0,
						'zm'=>1,'zhds'=>1,'zhdx'=>1,
						'zt'=>2,
						'tmds'=>3,'tmdx'=>3,'tmhsds'=>3,'tmwsdx'=>3,'tmqs'=>3,
						'ztds'=>3,'ztdx'=>3,'zthsds'=>3,'ztwsdx'=>3,'ztqs'=>3,
						'tmsb'=>4,'ztsb'=>4,
						'lm_0'=>5,'lm_1'=>6,'lm_2'=>7,'lm_3'=>8,'lm_4'=>9,
						'tx'=>10,'sx'=>11,'ws'=>12, 'bbds'=>13,'bbdx'=>13,'mx'=>14,
						'sxl'=>15,'wsl'=>15,'bz'=>16,'dzy'=>17,'qsb'=>18,
						'zx_0'=>19,'zx_1'=>19,'zx_2'=>19,'zx_3'=>19,'zx_4'=>20,'zx_5'=>20,
						'qm_0'=>21,'qm_1'=>21,'qm_2'=>21,'qm_3'=>21,'qm_4'=>21,'qm_5'=>21,'qm_6'=>21,'qm_7'=>21);		
		
		if($roleid>0){
			$sumLimitCmd=$conn->createCommand("select termLimit,betLimit,termUsed from tbl_sumlimit where idx=:idx and userid=:userid");
			$updatesumLimitCmd=$conn->createCommand("update tbl_sumlimit set termUsed=termUsed+:termUsed where idx=:idx and userid=:userid");
			$sumLimitMap=array();
			foreach($posts['sums'] as $key=>$value){
				$typeRec=$typeRecs[$key];
				$rates=array();
				$termLimit=-1;
				$betLimit=-1;
				$termUsed=-1;
				$btcid=$typeRec['btc'];
				$abhIdx=($btcid=='lm'||$btcid=='zx')?$autoBhMap[$key]:((substr($btcid,0,2)=='zt')?$autoBhMap[preg_replace('/[\d\_]/i','',$btcid)]:$autoBhMap[$btcid]);
				if($roleid==5){
					if(!isset($sumLimitMap[$abhIdx])){
						$sumLimitRec=$sumLimitCmd->queryRow(true,array(":idx"=>$abhIdx,":userid"=>$userid));
						if($sumLimitRec!=false){
							$termLimit=floatval($sumLimitRec['termLimit']);
							$betLimit=floatval($sumLimitRec['betLimit']);
							$termUsed=floatval($sumLimitRec['termUsed']);
							$sumLimitMap[$abhIdx]=$sumLimitRec;
						}
					}else{
						$termLimit=$sumLimitMap[$abhIdx]['termLimit'];
						$betLimit=$sumLimitMap[$abhIdx]['betLimit'];
						$termUsed=$sumLimitMap[$abhIdx]['termUsed'];
					}
				}
				if(CommonUtil::isCb($typeRec['btc'])){
					$cbCodeMap=array('lm_0'=>3,'lm_1'=>3,'lm_2'=>2,'lm_3'=>2,'lm_4'=>2,'dzy_0'=>3,'dzy_1'=>4,'dzy_2'=>5,'dzy_3'=>6,'mx_0'=>6,'bz_0'=>5,'sxl_0'=>2,'sxl_1'=>2,'sxl_2'=>3,'sxl_3'=>3,'sxl_4'=>4,'sxl_5'=>4,'wsl_0'=>2,'wsl_1'=>2,'wsl_2'=>3,'wsl_3'=>3,'wsl_4'=>4,'wsl_5'=>4);
					$cdCodeNum=array('lm'=>49,'dzy'=>49,'mx'=>12,'bz'=>49,'sxl'=>12,'wsl'=>10);
					foreach($posts['rates'] as $rName=>$rValue){
						$cbCodes=split(',',$rName);
						//$a == "lm" || $a == "bz" || $a == "mx" || $a == "sxl" || $a == "wsl" || $a == "dzy";
						//Ltr.lmNames = [ "三中二", "三全中", "二全中", "二中特", "特串" ];
						/*Ltr.dzyNames = [ "三中一", "四中一", "五中一", "六中一" ];
							mx : [ "六肖" ],
						bz : [ "五不中" ],
						sxl : [ "二肖连-中", "二肖连-不中", "三肖连-中", "三肖连-不中", "四肖连-中", "四肖连-不中" ],
						wsl : [ "二尾连-中", "二尾连-不中", "三尾连-中", "三尾连-不中", "四尾连-中", "四尾连-不中" ],
						*/
						if(count($cbCodes)!=$cbCodeMap[$key]||count(array_unique($cbCodes))!=count($cbCodes)){
							$msg="{$typeRec['name']} 下注项目有误";
							break;
						}
						$isCbCode=true;
						foreach($cbCodes as $cbCode){
							if(!is_numeric($cbCode)||strpos($cbCode,'.')!==false||intval($cbCode)>=$cdCodeNum[$typeRec['btc']]){
								$isCbCode=false;
								break;
							}
						}
						if(!$isCbCode){
							$msg="{$typeRec['name']} 下注项目有误";
							break;
						}
						
						$rates=split(',',$rValue);
						if(floatval($typeRec['rate0'.$lei])!=floatval($rates[0])){
							$msg="{$typeRec['name']} 赔率已变动";
							break;
						}
						if(count($rates)==2){
							if(floatval($typeRec['rate1'.$lei])!=floatval($rates[1])){
								$msg="{$typeRec['name']} 赔率已变动";
								break;
							}
						}
						
						if($betLimit!=-1){
							if($value>$betLimit){
								$msg="{$typeRec['name']} 单注金额不能超过 $betLimit";
								break;
							}
						}
						
						if($termLimit!=-1){
							if($value>$termLimit-$termUsed){
								$msg="{$typeRec['name']} 本期单场金额不能超过 $termLimit";
								break;
							}
							$termUsed=$termUsed+$value;
							$sumLimitMap[$abhIdx]['termUsed']=$termUsed;
						}
					}
				}else{
					if(isset($posts['rates'][$key])){
						$rates=split(',',$posts['rates'][$key]);
						if(floatval($typeRec['rate0'.$lei])!=floatval($rates[0])){
							$msg="{$typeRec['name']} 赔率已变动";
							break;
						}
						if(count($rates)==2){
							if(floatval($typeRec['rate1'.$lei])!=floatval($rates[1])){
								$msg="{$typeRec['name']} 赔率已变动";
								break;
							}
						}
						if($betLimit!=-1){
							if($value>$betLimit){
								$msg="{$typeRec['name']} 单注金额不能超过 $betLimit";
								break;
							}
						}
						if($termLimit!=-1){
							if($value>$termLimit-$termUsed){
								$msg="{$typeRec['name']} 本期单场金额不能超过 $termLimit";
								break;
							}
							$termUsed=$termUsed+$value;
							$sumLimitMap[$abhIdx]['termUsed']=$termUsed;
						}
					}
				}
			}
		}
		
    	if($msg!=''){
			echo CJSON::encode(array("msg"=>$msg,"success"=>false));
			Yii::app()->end(); 
		}
		
		
		$checkAutoBhCmd=$conn->createCommand("select id,sumLimit from tbl_autobh where auto=1 and idx=:idx and userid=:userid");
		$checkAutoBhTypeCmd=$conn->createCommand("select usedSum from tbl_abh_type where abhid=:abhid and ltrtypecode=:betCode");
		$checkAutoBhTypeUpdateCmd=$conn->createCommand("replace into tbl_abh_type(abhid,ltrtypecode,usedSum) values (:abhid,:ltrtypecode,:usedSum)");
		$sql="insert into tbl_bet(betType,betCode,rebate,betUserId,betUserName,betSum,betTime,bhUserId,bhUserName,ltrTypeId,ltrTypeName,ltrBtcId,ltrType,prorate_0,prorate_1,prorate_2,prorate_3,prorate_4,statUser_0,statUser_1,statUser_2,statUser_3,statUser_4,rate,rate0,rate1,role,termid,lei)"
		."values (:betType,:betCode,:rebate,:betUserId,:betUserName,:betSum,sysdate(),:bhUserId,:bhUserName,:ltrTypeId,:ltrTypeName,:ltrBtcId,:ltrType,:prorate_0,:prorate_1,:prorate_2,:prorate_3,:prorate_4,:statUser_0,:statUser_1,:statUser_2,:statUser_3,:statUser_4,:rate,:rate0,:rate1,:role,:termid,:lei)";		
		$command=$conn->createCommand($sql);
		$updateUserCmd=$conn->createCommand("update tbl_user set usedSum=usedSum+:betSum where id=:id");
		//$prorates=$conn->createCommand("select `prorate_0`,`prorate_1`,`prorate_2`,`prorate_3`,`prorate_4` from tbl_user_prorate where userid=:userid")->queryRow(true,array(":userid"=>Yii::app()->user->id));
		$prorateUserCmd=$conn->createCommand("select parentProrate,parentId,role from tbl_user where id=:id");
		$prorateUser=array("parentId"=>$userid);
		$prorateUsers=array();
		$bhuser=array();
		if(isset($_POST['bhuser'])){
			$bhuser=$conn->createCommand("select id,name from tbl_bhuser where id='{$_POST['bhuser']}'")->queryRow();
		}
		if($roleid < 5){
			$bhRemainCmd=$conn->createCommand("select sum(if(betUserId='$userid',-betSum,betSum*prorate_$roleid)) from tbl_bet where statUser_$roleid='$userid' and ltrTypeId=:ltrTypeId and termid='{$this->term}'");
		}
		//0 parentProrate 0,parentId 0, 1 股东 parentProrate 公司,parentId 公司, 2 小股东 parentProrate  parentId 2
		while($prorateUser['parentId']!=''){
			$prorateUser=$prorateUserCmd->queryRow(true,array(":id"=>$prorateUser['parentId']));
			if($prorateUser['parentId']!=''){
				//echo $prorateUsers['role']-1;
				if(count($prorateUsers)==$roleid-1){
					$totalProrate=0;
					foreach($prorateUsers as $tmp_prorateUser){
						$totalProrate+=$tmp_prorateUser['parentProrate'];
					}
					$prorateUser['parentProrate']=1-$totalProrate;
				}
				array_unshift($prorateUsers,$prorateUser);
			}
		}
		$siteInfo=$conn->createCommand("select * from tbl_site")->queryRow();
		$brs=array();
		$i=0;
		$rebates=array();
		//must check sum
		foreach($posts['sums'] as $key=>$value){
			//if($posts['isMulti']){
				if($value==''){
					continue;
				}
				//$typeRec=$findRateCmd->queryRow(true,array(":id"=>$key));
				$typeRec=$typeRecs[$key];
				if($typeRec===null||$typeRec===false){
					continue;
				}
				if(substr($key,0,2)!='zx'){//zx_0...5
					$rebateKey=substr($key,0,strrpos($key,'_'));
					//zt_2_hsds_0 zt_2_23 -> zt_2_hsds zt_2 -> zthsds zt  
					if(substr($rebateKey,0,2)=='zt'){
						$rebateKey=preg_replace('/[\d\_]/i','',$rebateKey);
					}
				}else{
					if($key=='zx_4'||$key=='zx_5'){
						$rebateKey='zxds';
					}else{
						$rebateKey='zx';
					}
				}
				if(!isset($rebates[$rebateKey])){
					if(!isset($_POST['bhuser'])){
						$rebateRec=$conn->createCommand("select rebate$lei from tbl_default_rebate where LOCATE('$rebateKey,',concat(btcs,','))>0")->queryRow(false);
					}else{
						$rebateRec=$conn->createCommand("select rebate0 from tbl_default_rebate_bh where LOCATE('$rebateKey,',concat(btcs,','))>0 and userid='{$bhuser['id']}'")->queryRow(false);
					}
					$rebates[$rebateKey]=$rebateRec[0];
				}
				$rates=array();
				if($roleid==5||$roleid==0){
					if($posts['isMulti']===true){
						$rates=$posts['rates'];
					}else{
						$rates[$key]=$posts['rates'][$key];
					}
				}else{
					if($typeRec['rate1'.$lei]>0){
						$rates=$typeRec['rate0'.$lei].','.$typeRec['rate1'.$lei];
					}else{
						$rates[$key]=$typeRec['rate0'.$lei];
					}
				}
				$abhIdx=($btcid=='lm'||$btcid=='zx')?$autoBhMap[$key]:((substr($btcid,0,2)=='zt')?$autoBhMap[preg_replace('/[\d\_]/i','',$btcid)]:$autoBhMap[$btcid]);
			foreach($rates as $rName=>$rValue){
				$typeName=$typeRec['name'];
				$code=0;
				$btcid = '';
				if(strpos($key,'qm_')!==false ||strpos($key,'zt_')!==false  ){
					$btcid = substr($key,0,strrpos($key,'_'));
				}else{
					$btcid = substr($key,0,strpos($key,'_'));
				}
				if($posts['isMulti']===true){
					$typeName=$typeName.'('.$this->getItemCodeName($btcid,$rName).')';
					$code=$rName;
				}else{
					$code=substr($rName,strrpos($rName,'_')+1);
				}
				$rValue=split(',',$rValue);
				$rateName=$rValue[0];
				if(isset($rValue[1])&&$rValue[1]>0){
					$rateName.='/'.$rValue[1];
				}
				
				
				if($roleid < 5){//补货
					$bhRemain=$bhRemainCmd->queryRow(false,array(':ltrTypeId'=>$key));
					//print_r($bhRemain);
					if($bhRemain!==false&&$bhRemain[0]<$value){
						array_push($brs,array($i++,$typeName,$value,$rateName,0,"失败"));
						$msg.="$typeName 补货金额超过了当前下注金额;\n";
						continue;
					}
						$command->bindParam(":betType",$typeName,PDO::PARAM_STR);
							$command->bindParam(":betCode",$code,PDO::PARAM_INT);
							$command->bindParam(":rebate",$rebates[$rebateKey],PDO::PARAM_INT);
							$command->bindParam(":betUserId",$userid,PDO::PARAM_STR);
							$command->bindParam(":betUserName",Yii::app()->user->getState('username'),PDO::PARAM_STR);
							$command->bindParam(":betSum",$value,PDO::PARAM_INT);
							if(isset($_POST['bhuser'])){
								$command->bindParam(":bhUserId",$bhuser['id'],PDO::PARAM_STR);
								$command->bindParam(":bhUserName",$bhuser['name'],PDO::PARAM_STR);
							}else{
								$command->bindParam(":bhUserId",$vNull,PDO::PARAM_NULL);
								$command->bindParam(":bhUserName",$vNull,PDO::PARAM_NULL);
							}
							//$command->bindParam(":hyResult",0,PDO::PARAM_INT);
							$command->bindParam(":ltrTypeId",$key,PDO::PARAM_STR);
							$command->bindParam(":ltrTypeName",$typeRec['name'],PDO::PARAM_STR);
							$command->bindParam(":ltrBtcId",$btcid,PDO::PARAM_STR);
							$command->bindParam(":ltrType",$typeRec['typeid'],PDO::PARAM_INT);
							$bhProp0=1;
							$bhProp1=0;
							$bhStatUserNull=NULL;
							$command->bindParam(":prorate_0",$bhProp0,PDO::PARAM_INT);//公司占成
							$command->bindParam(":prorate_1",$bhProp1,PDO::PARAM_INT);//
							$command->bindParam(":prorate_2",$bhProp1,PDO::PARAM_INT);
							$command->bindParam(":prorate_3",$bhProp1,PDO::PARAM_INT);
							$command->bindParam(":prorate_4",$bhProp1,PDO::PARAM_INT);
							for($bhI=0;$bhI<$roleid;$bhI++){
								$command->bindParam(":statUser_$bhI",$prorateUsers[$bhI]['parentId'],PDO::PARAM_STR);
							}
							$command->bindParam(":statUser_$roleid",$userid,PDO::PARAM_STR);
							for($bhI=$roleid+1;$bhI<=4;$bhI++){
								$command->bindParam(":statUser_$bhI",$bhStatUserNull,PDO::PARAM_NULL);
							}
							$command->bindParam(":rate",$rateName,PDO::PARAM_STR);
							$command->bindParam(":rate0",$rValue[0],PDO::PARAM_INT);
							$command->bindParam(":rate1",$rValue[1],PDO::PARAM_INT);
							$command->bindParam(":role",$roleid,PDO::PARAM_INT);
							$command->bindParam(":termid",$this->term,PDO::PARAM_STR);
							$command->bindParam(":lei",$lei,PDO::PARAM_INT);
							$command->execute();
							array_push($brs,array($i++,$typeName,$value,$rateName,0,"成功"));
							continue;
				}
				
				foreach ($prorateUsers as $prorateRole=>$prorateUser){
					$checkAutoBhRec=$checkAutoBhCmd->queryRow(true,array(":idx"=>$abhIdx,":userid"=>$prorateUser['parentId']));
					if($checkAutoBhRec!=false){
						//echo 'has begin autobh\n';
						$sumLimit=floatval($checkAutoBhRec['sumLimit']);
						$checkAutoBhTypeRec=$checkAutoBhTypeCmd->queryRow(true,array(":abhid"=>$checkAutoBhRec['id'],":betCode"=>$code));
						$abhUsedSum=0;
						if($checkAutoBhTypeRec!=false){
							$abhUsedSum=$checkAutoBhTypeRec['usedSum'];
						}
						$leftSum=$sumLimit-$abhUsedSum;//10-0
						$bhBetSum=0;
						$prorBetSum=$value*$prorateUser['parentProrate'];//1000*0.2=200
						//echo "leftSum:$leftSum,prorBetSum:$prorBetSum\n";
						if($leftSum<$prorBetSum){
							//echo 'has insert autobh';
							//bh
							$bhBetSum=$prorBetSum-$leftSum;
							$command->bindParam(":betType",$typeName,PDO::PARAM_STR);
							$command->bindParam(":betCode",$code,PDO::PARAM_INT);
							$command->bindParam(":rebate",$rebates[$rebateKey],PDO::PARAM_INT);
							$command->bindParam(":betUserId",$prorateUser['parentId'],PDO::PARAM_STR);
							$command->bindParam(":betUserName",Yii::app()->user->getState('username'),PDO::PARAM_STR);
							$command->bindParam(":betSum",$bhBetSum,PDO::PARAM_INT);
							$command->bindParam(":bhUserId",$vNull,PDO::PARAM_NULL);
							$command->bindParam(":bhUserName",$vNull,PDO::PARAM_NULL);
							//$command->bindParam(":hyResult",0,PDO::PARAM_INT);
							$command->bindParam(":ltrTypeId",$key,PDO::PARAM_STR);
							$command->bindParam(":ltrTypeName",$typeRec['name'],PDO::PARAM_STR);
							$command->bindParam(":ltrBtcId",$btcid,PDO::PARAM_STR);
							$command->bindParam(":ltrType",$typeRec['typeid'],PDO::PARAM_INT);
							$bhProp0=1;
							$bhProp1=0;
							$bhStatUserNull=NULL;
							$command->bindParam(":prorate_0",$bhProp0,PDO::PARAM_INT);//公司占成
							$command->bindParam(":prorate_1",$bhProp1,PDO::PARAM_INT);//
							$command->bindParam(":prorate_2",$bhProp1,PDO::PARAM_INT);
							$command->bindParam(":prorate_3",$bhProp1,PDO::PARAM_INT);
							$command->bindParam(":prorate_4",$bhProp1,PDO::PARAM_INT);
							for($bhI=0;$bhI<=$prorateRole;$bhI++){
								$command->bindParam(":statUser_$bhI",$prorateUsers[$bhI]['parentId'],PDO::PARAM_STR);
							}
							for($bhI=$prorateRole+1;$bhI<=4;$bhI++){
								$command->bindParam(":statUser_$bhI",$bhStatUserNull,PDO::PARAM_NULL);
							}
							$command->bindParam(":rate",$rateName,PDO::PARAM_STR);
							$command->bindParam(":rate0",$rValue[0],PDO::PARAM_INT);
							$command->bindParam(":rate1",$rValue[1],PDO::PARAM_INT);
							$command->bindParam(":role",$prorateRole,PDO::PARAM_INT);
							$command->bindParam(":termid",$this->term,PDO::PARAM_STR);
							$command->bindParam(":lei",Yii::app()->user->getState('lei'),PDO::PARAM_INT);
							$command->execute();
						}
						// 50  10  bet 40 
						$checkAutoBhTypeUpdateCmd->execute(array(":abhid"=>$checkAutoBhRec['id'],":ltrtypecode"=>$code,":usedSum"=>$abhUsedSum+min($leftSum,$prorBetSum)));
					}
				}
				
				$command->bindParam(":betType",$typeName,PDO::PARAM_STR);
				$command->bindParam(":betCode",$code,PDO::PARAM_INT);
				$command->bindParam(":rebate",$rebates[$rebateKey],PDO::PARAM_INT);
				$command->bindParam(":betUserId",$userid,PDO::PARAM_STR);
				$command->bindParam(":betUserName",Yii::app()->user->getState('username'),PDO::PARAM_STR);
				$command->bindParam(":betSum",$value,PDO::PARAM_INT);
				$command->bindParam(":bhUserId",$vNull,PDO::PARAM_NULL);
				$command->bindParam(":bhUserName",$vNull,PDO::PARAM_NULL);
				//$command->bindParam(":hyResult",0,PDO::PARAM_INT);
				$command->bindParam(":ltrTypeId",$key,PDO::PARAM_STR);
				$command->bindParam(":ltrTypeName",$typeRec['name'],PDO::PARAM_STR);
				$command->bindParam(":ltrBtcId",$btcid,PDO::PARAM_STR);
				$command->bindParam(":ltrType",$typeRec['typeid'],PDO::PARAM_INT);
				$command->bindParam(":prorate_0",$prorateUsers[0]['parentProrate'],PDO::PARAM_INT);//公司占成
				$command->bindParam(":prorate_1",$prorateUsers[1]['parentProrate'],PDO::PARAM_INT);//
				$command->bindParam(":prorate_2",$prorateUsers[2]['parentProrate'],PDO::PARAM_INT);
				$command->bindParam(":prorate_3",$prorateUsers[3]['parentProrate'],PDO::PARAM_INT);
				$command->bindParam(":prorate_4",$prorateUsers[4]['parentProrate'],PDO::PARAM_INT);
				$command->bindParam(":statUser_0",$prorateUsers[0]['parentId'],PDO::PARAM_STR);
				$command->bindParam(":statUser_1",$prorateUsers[1]['parentId'],PDO::PARAM_STR);
				$command->bindParam(":statUser_2",$prorateUsers[2]['parentId'],PDO::PARAM_STR);
				$command->bindParam(":statUser_3",$prorateUsers[3]['parentId'],PDO::PARAM_STR);
				$command->bindParam(":statUser_4",$prorateUsers[4]['parentId'],PDO::PARAM_STR);
				$command->bindParam(":rate",$rateName,PDO::PARAM_STR);
				$command->bindParam(":rate0",$rValue[0],PDO::PARAM_INT);
				$command->bindParam(":rate1",$rValue[1],PDO::PARAM_INT);
				$command->bindParam(":role",Yii::app()->user->getState('role'),PDO::PARAM_INT);
				$command->bindParam(":termid",$this->term,PDO::PARAM_STR);
				$command->bindParam(":lei",Yii::app()->user->getState('lei'),PDO::PARAM_INT);
				$command->execute();
				
				Yii::app()->user->setState('usedSum',Yii::app()->user->usedSum+$value);
				$updateUserCmd->execute(array(":betSum"=>$value,":id"=>$userid));
				$updatesumLimitCmd->execute(array(":idx"=>$abhIdx,":userid"=>$userid,":termUsed"=>$value));
				/*
				  if (h.get("result") == 7) {
						a[a.length] = [ h.get("rcId"), h.get("betType"),
								h.get("betSum"), h.get("rate"), h.get("newRate"), true ]
					}
				  [ "id", "betType", "betSum", "rate", "result", "cause", "rcId", "newRate" ]
				 */
				array_push($brs,array($i++,$typeName,$value,$rateName,0,"成功"));
			}
		}
		echo CJSON::encode(array("brs"=>$brs,"success"=>$msg=='',"msg"=>$msg));
		//echo '{"brs":[[0,"特码A02",20.0,"42.38",0,"成功"],[1,"特码A03",30.0,"42.38",0,"成功"]],"success":true}';
		Yii::app()->end(); 
    }
    
    /**
     * btc	tm
		balanSum	100
     * @return unknown_type
     */
    public function actionBalanBh(){
    	$brs=array();
    	$this->layout=false;
		header('Content-type: application/json');
		
		echo CJSON::encode(array("brs"=>$brs,"success"=>true));
		//echo '{"brs":[[0,"特码A02",20.0,"42.38",0,"成功"],[1,"特码A03",30.0,"42.38",0,"成功"]],"success":true}';
		Yii::app()->end(); 
    }
    
    /**
     * notTeAutoCloseTime	23:56
		resultDate	2011-03-31
		resultTime	21:00
		subTeAutoCloseTime	23:57
		teAutoCloseTime	23:59
     * @return unknown_type
     */
    public function actionModifyCloseTime(){
    	$this->layout=false;
		header('Content-type: application/json');
    	$command=Yii::app()->db->createCommand("update tbl_ltr set notTeAutoCloseTime=:notTeAutoCloseTime,resultDate=:resultDate,resultTime=:resultTime,teAutoCloseTime=:teAutoCloseTime where term=:term");
    	$command->bindParam(":notTeAutoCloseTime",$_POST['notTeAutoCloseTime'],PDO::PARAM_STR);
    	$command->bindParam(":resultDate",$_POST['resultDate'],PDO::PARAM_STR);
    	$command->bindParam(":resultTime",$_POST['resultTime'],PDO::PARAM_STR);
    	$command->bindParam(":teAutoCloseTime",$_POST['teAutoCloseTime'],PDO::PARAM_STR);
    	$command->bindParam(":term",$this->term,PDO::PARAM_STR);
		$command->execute();
    	echo '{"success":true}';
    	Yii::app()->end(); 
    }
    
    /**
     * 结账
     * @return unknown_type
     */
    public function actionSettle(){
		$this->layout=false;
		header('Content-type: application/json');
		//$site=Yii::app()->user->site;
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
			echo CJSON::encode(array("ltr"=>$ltrRec,"msg"=>$msg,"success"=>false));
			Yii::app()->end();
		}
		
		$codes=$conn->createCommand("select * from tbl_ltr_codes where term=:term")->queryRow(true,array(":term"=>$this->term));
		
		foreach($codes as $code){
			if($code==null||strlen($code)<2){
				CommonUtil::metaData($ltrRec,array('','bool','int'));
				echo CJSON::encode(array("ltr"=>$ltrRec,"msg"=>"有号码为空","success"=>false));
				Yii::app()->end();
				break;
			}
		}
		
		//CommonUtil::runBackGround("/bet/runBackSettle.do");
		$betService=new BetService();
    	echo $betService->settle($this->term);

		//{"ltr":{"id":2011013,"settling":false,"status":2,"codes":["01","02","03","04","05","06","49"]},"msg":"已结帐，无法重复结账","success":false}
		$ltr=array("id"=>$this->term,"settling"=>true,"status"=>1);
		echo CJSON::encode(array("ltr"=>$ltr,"success"=>true));
		Yii::app()->end();
    }
    
    public function actionRunBackSettle(){
    	ini_set('max_execution_time',600);
    	set_time_limit(0);
    	$this->layout=false;
		header('Content-type: text/html');
		
    	if(Yii::app()->user->role==0){
    		$betService=new BetService();
    		echo $betService->settle($this->term);
    	}
    	Yii::app()->end();
    }
    
    public function getItemCodeName($c,$f) {

	 $itemCodeNames=array();
	 if($c == "lm"||$c == "dzy"||$c == "bz"|| $c == "wbz"){
	 	for($i=1;$i<=49;$i++){
       	 $itemCodeNames[]=str_pad($i, 2, "0", STR_PAD_LEFT); 
	 	}
	 }else if($c == "wsl"){
		$itemCodeNames=array( "0尾", "1尾", "2尾", "3尾", "4尾", "5尾", "6尾", "7尾", "8尾", "9尾" );
	 }else{
        $itemCodeNames=array( "鼠", "牛", "虎", "兔", "龙", "蛇", "马", "羊", "猴", "鸡", "狗", "猪");
	 }
	 $itemCodes=split(',',$f);
	 $s='';
	 for($i=0;$i<count($itemCodes);$i++){
		$s.=",".$itemCodeNames[$itemCodes[$i]];
	 }
	 return substr($s,1);
}
	
}