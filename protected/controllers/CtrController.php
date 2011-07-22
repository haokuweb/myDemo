<?php

class CtrController extends Controller
{
	/**
	 * post:
	 * btcs:
	 * tm,tmds,tmdx,tmhsds,tmsb,tmwsdx,tmqs 
	 * | zm,zhds,zhdx 
	 * | lm 
	 * | tx 
	 * |bbds,bbdx 
	 * | mx 六肖
	 *  | sx,ws 
	 *  sxl 
	 *  wsl 
	 *  bz 五不中 
	 *   zt_0,zt_0_ds,zt_0_dx,zt_0_hsds,zt_0_sb,zt_0_wsdx,zt_0_qs正特1  
	 *   zt_1,zt_1_ds,zt_1_dx,zt_1_hsds,zt_1_sb,zt_1_wsdx,zt_1_qs
		lei	0 盘 ,0-A, 1-B
		marChangeFlag	0
		onliner	qq133
		pan	2
	get:
	  marChangeFlag  第一次请求或 有改动，则为 -1
      oc
      btcs:{open, rate, bet }
	 * @return unknown_type
	 */
	public function actionRefreshJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		

		$refresh=array();
		//$site=Yii::app()->user->getState('site');
		$huo=$_POST['huo'];
		$lei=$_POST['lei'];//盘
		$roleid=Yii::app()->user->role;
		$userid=Yii::app()->user->id;
		$conn=Yii::app()->db;
		if($_POST['marChangeFlag']==-1){
			$refresh['marChangeFlag']=0;
			$refresh['marMsg']=implode(',',$conn->createCommand("select message from tbl_marquee where showMar=1 order by updatedTime desc")->queryColumn());
		}
		$conn->createCommand("delete from tbl_online_user where lasttime<sysdate()-INTERVAL 10 MINUTE")->execute();
		//$conn->createCommand("update tbl_online_user set lasttime=sysdate() where sessionid='".session_id()."'")->execute();
		if(Yii::app()->user->level>=0){
			$onlineN=$conn->createCommand("select count(*) from  tbl_online_user where sessionid='".session_id()."'")->queryRow(false);
			if($onlineN[0]==0){
				echo "this.location.href='/main/login.do'";
		    	Yii::app()->end();
			}else{
				$conn->createCommand("update tbl_online_user set lasttime=sysdate() where sessionid='".session_id()."'")->execute();
			}
		}
		$onlinerRec=$conn->createCommand("select count(*) from tbl_online_user o left join tbl_user u on o.userid=u.id where u.parent_{$roleid}='$userid' or o.userid='$userid'")->queryRow(false);
		$refresh['oc']=$onlinerRec[0];
		
		if($huo==2){
			$huo=0;
			$roleid=$roleid-1;
			$userid=Yii::app()->user->parentId;
		}
		
		//$refresh['key']=session_id();
		$xuHuo=array("1","1-prorate_4-prorate_3-prorate_2","1-prorate_4-prorate_3","1-prorate_4","1");
		$sumHuo=$huo==1?"*({$xuHuo[$roleid]})":"*prorate_$roleid";
		$whereStatUser=" and statUser_$roleid='".$userid."' and betUserId<>'".$userid."'";
		$ltrRec=$conn->createCommand("select resultDate,notTeAutoCloseTime,teAutoCloseTime from tbl_ltr where term='".$this->term."'")->queryRow();
		$notTeAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['notTeAutoCloseTime'])-time();
		$teAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['teAutoCloseTime'])-time();
		if(isset($_POST['btcs'])){
			$pan=$_POST['pan'];//A 0, B 1,AB 2,AB+ 3 种类
			$btcs=array();
			$termStatus=CommonUtil::getCachedTermStatus();
			$sbs=array(0, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2 );
			$jqs=array(11,7,10,6,9,1);
			$betCmd=$conn->createCommand("select ltrBtcId,ltrTypeId,sum(betSum".$sumHuo.") as bet,sum(-hyResult".$sumHuo.") as win,sum(betSum{$sumHuo}*(rate0+rebate/100-1)) as winSum,sum(betSum{$sumHuo}*(1-rebate/100)) as noWinSum,betCode,prorate_$roleid as prorate,rate0,rate1,rebate from tbl_bet where termid='".$this->term."' and `ltrBtcId` in ('".str_replace(",","','",$_POST['btcs'])."') $whereStatUser group by ltrTypeId order by ltrTypeId");
			$selfBhCmd=$conn->createCommand("select ltrBtcId,ltrTypeId,sum(betSum) as bet,sum(-hyResult) as win,sum(betSum*(rate0+rebate/100-1)) as winSum,sum(betSum*(1-rebate/100)) as noWinSum,rate0,rate1,rebate from tbl_bet where termid='".$this->term."' and `ltrBtcId` in ('".str_replace(",","','",$_POST['btcs'])."') and betUserId='".$userid."' group by ltrTypeId order by ltrTypeId");
			$btcsRec=$conn->createCommand("select t.*,(SELECT IF(r.open=0,0,t.open) FROM tbl_default_rate r WHERE r.id=t.type OR (r.id='txbx' AND t.type='tx' AND t.id='tx_1') OR (r.id='txfbx' AND t.type='tx' AND t.id<>'tx_1') OR (r.id='sxbx' AND t.type='sx' AND t.id='sx_1') OR (r.id='sxfbx' AND t.type='sx' AND t.id<>'sx_1') OR (r.id='ws0' AND t.type='ws' AND t.id='ws_0') OR (r.id='wsf0' AND t.type='ws' AND t.id<>'ws_0')) AS defaultopen from tbl_ltr_type t where t.`btc` in ('".str_replace(",","','",$_POST['btcs'])."') order by btc,REPLACE(RIGHT(id, 2),'_','0')")->queryAll();
			foreach($btcsRec as $btc){
				if($btc['btc']=='tm'||$btc['btc']=='zm'){
					//if($pan==2||$pan==3) $pan=0;
					// AB AB+ 都显示特码A赔率 
					
					//避免特码A B 重合,要么A赔率，要么B赔率
					if($btc['btc'].'_'.($pan==1?1:0)!=substr($btc['id'],0,strrpos($btc['id'],'_'))){
						continue;
					}
				}
				$rate=floatval($btc['rate0'.$lei]);
				$rate1=floatval($btc['rate1'.$lei]);
				if($rate1>0){
					$rate=array($rate,$rate1);
				}
				//$openStatus=(strpos($btc['btc'],'tm')===false||strpos($btc['btc'],'tm')>0)?$termStatus['ftmstatus']:$termStatus['tmstatus'];('tm','tmds','tmdx','tmhsds','tmsb','tmwsdx','tmqs','tx','bbds','bbdx','mx')
				$openStatus=0;
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
				}
				$btcs[$btc['btc']]['open'][]=$openStatus;
				//$termStatus['status']==1?($openStatus==0?intval($btc['open']):($openStatus-1)):0;
				$btcs[$btc['btc']]['rate'][]=$rate;
				
				//tbl_stat,tbl_ltr_type[win,bet]  A B C ? 实货虚货?
				
				$btcs[$btc['btc']]['bet'][]=0;
				$btcs[$btc['btc']]['win'][]=0;
				$btcs[$btc['btc']]['bh'][]=0;
				
				//$btcs[$btc['btc']]['adjRate'][]=$btc['adjRate'];
				//$btcs[$btc['btc']]['name'][]=$btc['adjRate'];
				//$btcs[$btc['btc']]['items'][]=$btc['adjRate'];
				//$btcs[$btc['btc']]['cbIdx']=
			}
			
			
			$adjRateReader=$conn->createCommand("select * from tbl_adjrate where `btc` in ('".str_replace(",","','",$_POST['btcs'])."') order by btId,nameIdx")->query();
			while(($adjRate=$adjRateReader->read())!==false) {
				if(isset($_POST['cbIdx'])&&$_POST['cbIdx']==$adjRate['idx']){		
					$rate=floatval($adjRate['rate0'.$lei]);
					$rate1=floatval($adjRate['rate1'.$lei]);
					if(is_array($btcs[$adjRate['btc']]['rate'][$adjRate['idx']])){
						$rate=array($rate,$rate1);
					}
					$btcs[$adjRate['btc']]['adjRates'][]=$rate;
				}
				if(!isset($btcs[$adjRate['btc']]['name'])){
					$btcs[$adjRate['btc']]['name']=array();
				}
				if(!isset($btcs[$adjRate['btc']]['name'][$adjRate['nameIdx']])){
					$btcs[$adjRate['btc']]['name'][$adjRate['nameIdx']]=0;
				}
				//$btcs[$adjRate['btc']]['name'][$adjRate['nameIdx']]+=$adjRate['bet'];
			}
			
			$selfBhReader=$selfBhCmd->query();
			$btcResultMap=array('tx'=>12,'bbds'=>6,'bbdx'=>6,'zt_0'=>49,'zt_1'=>49,'zt_2'=>49,'zt_3'=>49,'zt_4'=>49,'zt_5'=>49,'qsb'=>4);

			$abBtc=array('tmds,tmdx,tmhsds,tmsb,tmwsdx,tmqs');
			
			while(($selftBh=$selfBhReader->read())!==false) {
					
					if(($selftBh['ltrBtcId']=='tm'||$selftBh['ltrBtcId']=='zm')&&($pan==0||$pan==1)&&substr($selftBh['ltrTypeId'],0,4)!=$selftBh['ltrBtcId'].'_'.$pan){
						continue;
					}
					$betCbIdx=substr($selftBh['ltrTypeId'],strrpos($selftBh['ltrTypeId'],'_')+1);
					
					$betResult=$selftBh['bet'];//-$btcs[$bet['ltrBtcId']]['bh'][$betCbIdx];
					
					$winSum=$selftBh['winSum'];//$betResult*$selftBh['rate0']-(1-$selftBh['rebate']/100)*$betResult;
					$noWinSum=$selftBh['noWinSum'];//(1-$selftBh['rebate']/100)*$betResult;
					if($selftBh['ltrBtcId']=='tm'){
		
							for($i=0;$i<49;$i++){
								if($i==$betCbIdx){
									$btcs[$selftBh['ltrBtcId']]['win'][$betCbIdx]+=$winSum;
								}else{
									$btcs[$selftBh['ltrBtcId']]['win'][$i]-=$noWinSum;
								}
							}
							//echo "result:".($selftBh['ltrTypeId'])." ".$betResult."\n";
						
					}elseif(isset($btcResultMap[$selftBh['ltrBtcId']])){
								for($i=0;$i<$btcResultMap[$selftBh['ltrBtcId']];$i++){
									if($i==$betCbIdx){
										$btcs[$selftBh['ltrBtcId']]['win'][$betCbIdx]+=$winSum;
									}else{
										$btcs[$selftBh['ltrBtcId']]['win'][$i]-=$noWinSum;
									}
								}
							
					}elseif($selftBh['ltrBtcId']=='zx'){
								if($betCbIdx==4){
									$btcs[$selftBh['ltrBtcId']]['win'][4]+=$winSum;
									$btcs[$selftBh['ltrBtcId']]['win'][5]-=$noWinSum;
								}elseif($betCbIdx==5){
									$btcs[$selftBh['ltrBtcId']]['win'][5]+=$winSum;
									$btcs[$selftBh['ltrBtcId']]['win'][4]-=$noWinSum;
								}else{
									for($i=0;$i<4;$i++){
										if($i==$betCbIdx){
											$btcs[$selftBh['ltrBtcId']]['win'][$betCbIdx]+=$winSum;
										}else{
											$btcs[$selftBh['ltrBtcId']]['win'][$i]-=$noWinSum;
										}
									}
								}
							
					}elseif(substr($selftBh['ltrBtcId'],0,3)=='qm_'){
								$qm_i=substr($selftBh['ltrBtcId'],3,1);
								for($i=0;$i<=7;$i++){
										if($i==$qm_i){
											$btcs["qm_$i"]['win'][$betCbIdx]+=$winSum;
										}else{
											$btcs["qm_$i"]['win'][$betCbIdx]-=$noWinSum;
										}
								}
							
					}elseif($pan==3){
						//特单：原来特码的盈亏+ 特单下注额*(1-退水)- 特单下注额*特单赔率
						//非特单号码：原来特码的盈亏+ 特单下注额*(1-退水)
						//tmds,tmdx,tmhsds,tmsb,tmwsdx,tmqs  zt_0_ds
						$abBtc=substr($selftBh['ltrBtcId'],0,2);
						if($abBtc=='zt'){
							$abBtc=substr($selftBh['ltrBtcId'],0,strrpos($selftBh['ltrBtcId'],'_'));//zt_0
						}
						if($selftBh['ltrBtcId']=='tmds'||substr($selftBh['ltrBtcId'],-3)=='_ds'){
							//$betCbIdx=0 单
							for($i=0;$i<24;$i++){
								$btcs[$abBtc]['win'][2*$i+$betCbIdx]+=$winSum;
								$btcs[$abBtc]['win'][2*$i+1-$betCbIdx]-=$noWinSum;
								$btcs[$abBtc]['bet'][2*$i+$betCbIdx]-=$betResult/24;
							}
						}elseif($selftBh['ltrBtcId']=='tmdx'||substr($selftBh['ltrBtcId'],-3)=='_dx'){
							for($i=0;$i<24;$i++){
								$btcs[$abBtc]['win'][$i+(1-$betCbIdx)*24]+=$winSum;
								$btcs[$abBtc]['win'][$i+$betCbIdx*24]-=$noWinSum;
								$btcs[$abBtc]['bet'][$i+(1-$betCbIdx)*24]-=$betResult/24;
							}
						}elseif($selftBh['ltrBtcId']=='tmhsds'||substr($selftBh['ltrBtcId'],-3)=='sds'){
							for($i=0;$i<48;$i++){
								$r=$i%10+floor($i/10);
								$btcs[$abBtc]['win'][$i]+=($r%2==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]-=($r%2==$betCbIdx)?$betResult/24:0;
							}
						}elseif($selftBh['ltrBtcId']=='tmwsdx'||substr($selftBh['ltrBtcId'],-3)=='sdx'){
							for($i=0;$i<48;$i++){
								$r=$i%10;
								$btcs[$abBtc]['win'][$i]+=(($r>3?0:1)==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]-=(($r>3?0:1)==$betCbIdx)?$betResult/24:0;
							}
						}elseif($selftBh['ltrBtcId']=='tmsb'||substr($selftBh['ltrBtcId'],-3)=='_sb'){
							for($i=0;$i<49;$i++){
								$r=$sbs[$i];
								$btcs[$abBtc]['win'][$i]+=($r==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]-=($r==$betCbIdx)?$betResult/12:0;
							}
						}elseif($selftBh['ltrBtcId']=='tmqs'||substr($selftBh['ltrBtcId'],-3)=='_qs'){
							for($i=0;$i<48;$i++){
								$r=in_array($i,$jqs)?0:1;
								$btcs[$abBtc]['win'][$i]+=($r==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]-=($r==$betCbIdx)?$betResult/24:0;
							}
						}
					}
					
					$btcs[$selftBh['ltrBtcId']]['bh'][$betCbIdx]+=floatval($selftBh['bet']);
					$btcs[$selftBh['ltrBtcId']]['bet'][$betCbIdx]-=floatval($selftBh['bet']);
				}
			
			$betReader=$betCmd->query();
			
			while(($bet=$betReader->read())!==false) {
				$betCbIdx=substr($bet['ltrTypeId'],strrpos($bet['ltrTypeId'],'_')+1);
				/*if($bet['ltrBtcId']=='tm'||$bet['ltrBtcId']=='zm'){
					// AB AB+ 都显示特码A赔率 
					if($pan==0||$pan==1){
						if(substr($bet['ltrTypeId'],0,4)!=$bet['ltrBtcId'].'_'.$pan){
							//echo substr($bet['ltrBtcId'],0,4);
							//echo $bet['ltrBtcId'].'_'.$pan;
							continue;
						}
						$btcs[$bet['ltrBtcId']]['bet'][$betCbIdx]+=floatval($bet['bet']);
						$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]+=floatval($bet['win']);
					}else{
						$btcs[$bet['ltrBtcId']]['bet'][$betCbIdx]+=floatval($bet['bet']);
						$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]+=floatval($bet['win']);
					}
				}else{
					$btcs[$bet['ltrBtcId']]['bet'][$betCbIdx]+=floatval($bet['bet']);
					$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]+=floatval($bet['win']);
					
					if($_POST['btcs']=='lm'||$_POST['btcs']=='dzy'||$_POST['btcs']=='mx'||$_POST['btcs']=='wsl'||$_POST['btcs']=='sxl'||$_POST['btcs']=='bz'){
						if(intval($_POST['cbIdx'])==-1 || $_POST['cbIdx']==$betCbIdx){
							$betCodes=split(',',$bet['betCode']);
							//$betCodes is nameIdx
							$codeLen=count($betCodes);
							foreach($betCodes as $betCode){
								$btcs[$bet['ltrBtcId']]['name'][$betCode]+=floatval($bet['bet']/$codeLen);
							}
						}
					}
				}*/
				$betResult=$bet['bet'];//-$btcs[$bet['ltrBtcId']]['bh'][$betCbIdx];
				$winSum=$bet['winSum'];//$betResult*$bet['rate0']-(1-$bet['rebate']/100)*$betResult;
				$noWinSum=$bet['noWinSum'];//(1-$bet['rebate']/100)*$betResult;
				//add for win
				if(($bet['ltrBtcId']=='tm'||$bet['ltrBtcId']=='zm')&&($pan==0||$pan==1)&&substr($bet['ltrTypeId'],0,4)!=$bet['ltrBtcId'].'_'.$pan){
						continue;
				}
				if($bet['ltrBtcId']=='tm'||$bet['ltrBtcId']=='zx'||substr($bet['ltrBtcId'],0,3)=='qm_'||isset($btcResultMap[$bet['ltrBtcId']])){
					if($bet['ltrBtcId']=='tm'){

							for($i=0;$i<49;$i++){
								if($i==$betCbIdx){
									$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]-=$winSum;
								}else{
									$btcs[$bet['ltrBtcId']]['win'][$i]+=$noWinSum;
								}
							}
					}elseif($bet['ltrBtcId']=='zx'){
								if($betCbIdx==4){
									$btcs[$bet['ltrBtcId']]['win'][4]-=$winSum;
									$btcs[$bet['ltrBtcId']]['win'][5]+=$noWinSum;
								}elseif($betCbIdx==5){
									$btcs[$bet['ltrBtcId']]['win'][5]-=$winSum;
									$btcs[$bet['ltrBtcId']]['win'][4]+=$noWinSum;
								}else{
									for($i=0;$i<4;$i++){
										if($i==$betCbIdx){
											$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]-=$winSum;
										}else{
											$btcs[$bet['ltrBtcId']]['win'][$i]+=$noWinSum;
										}
									}
								}
							
					}elseif(substr($bet['ltrBtcId'],0,3)=='qm_'){
								$qm_i=substr($bet['ltrBtcId'],3,1);
								for($i=0;$i<=7;$i++){
										if($i==$qm_i){
											$btcs["qm_$i"]['win'][$betCbIdx]-=$winSum;
										}else{
											$btcs["qm_$i"]['win'][$betCbIdx]+=$noWinSum;
										}
								}
							
					}else{
							//if(isset($btcResultMap[$bet['ltrBtcId']])){
								for($i=0;$i<$btcResultMap[$bet['ltrBtcId']];$i++){
									if($i==$betCbIdx){
										$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]-=$winSum;
									}else{
										$btcs[$bet['ltrBtcId']]['win'][$i]+=$noWinSum;
									}
								}
							//}
					}
					$btcs[$bet['ltrBtcId']]['bet'][$betCbIdx]+=floatval($bet['bet']);
				}elseif($pan==3){
						//特单：原来特码的盈亏+ 特单下注额*(1-退水)- 特单下注额*特单赔率
						//非特单号码：原来特码的盈亏+ 特单下注额*(1-退水)
						//tmds,tmdx,tmhsds,tmsb,tmwsdx,tmqs  zt_0_ds
						$abBtc=substr($bet['ltrBtcId'],0,2);
						if($abBtc=='zt'){
							$abBtc=substr($bet['ltrBtcId'],0,strrpos($bet['ltrBtcId'],'_'));//zt_0
						}
						if($bet['ltrBtcId']=='tmds'||substr($bet['ltrBtcId'],-3)=='_ds'){
							//$betCbIdx=0 单
							for($i=0;$i<24;$i++){
								$btcs[$abBtc]['win'][2*$i+$betCbIdx]-=$winSum;
								$btcs[$abBtc]['win'][2*$i+1-$betCbIdx]+=$noWinSum;
								$btcs[$abBtc]['bet'][2*$i+$betCbIdx]+=$betResult/24;
							}
						}elseif($bet['ltrBtcId']=='tmdx'||substr($bet['ltrBtcId'],-3)=='_dx'){
							for($i=0;$i<24;$i++){
								$btcs[$abBtc]['win'][$i+(1-$betCbIdx)*24]-=$winSum;
								$btcs[$abBtc]['win'][$i+$betCbIdx*24]+=$noWinSum;
								$btcs[$abBtc]['bet'][$i+(1-$betCbIdx)*24]+=$betResult/24;
							}
						}elseif($bet['ltrBtcId']=='tmhsds'||substr($bet['ltrBtcId'],-3)=='sds'){
							for($i=0;$i<48;$i++){
								$r=$i%10+floor($i/10);
								$btcs[$abBtc]['win'][$i]-=($r%2==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]+=($r%2==$betCbIdx)?$betResult/24:0;
							}
						}elseif($bet['ltrBtcId']=='tmwsdx'||substr($bet['ltrBtcId'],-3)=='sdx'){
							for($i=0;$i<48;$i++){
								$r=$i%10;
								$btcs[$abBtc]['win'][$i]-=(($r>3?0:1)==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]+=(($r>3?0:1)==$betCbIdx)?$betResult/24:0;
							}
						}elseif($bet['ltrBtcId']=='tmsb'||substr($bet['ltrBtcId'],-3)=='_sb'){
							for($i=0;$i<49;$i++){
								$r=$sbs[$i];
								$btcs[$abBtc]['win'][$i]-=($r==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]+=($r==$betCbIdx)?$betResult/12:0;
							}
						}elseif($bet['ltrBtcId']=='tmqs'||substr($bet['ltrBtcId'],-3)=='_qs'){
							for($i=0;$i<48;$i++){
								$r=CommonUtil::getSxCode($i+1);
								$r=in_array($i,$jqs)?0:1;
								$btcs[$abBtc]['win'][$i]-=($r==$betCbIdx)?$winSum:-$noWinSum;
								$btcs[$abBtc]['bet'][$i]+=($r==$betCbIdx)?$betResult/24:0;
							}
						}
						$btcs[$bet['ltrBtcId']]['bet'][$betCbIdx]+=floatval($bet['bet']);
						$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]+=floatval($bet['win']);
				}else{
					$btcs[$bet['ltrBtcId']]['bet'][$betCbIdx]+=floatval($bet['bet']);
					$btcs[$bet['ltrBtcId']]['win'][$betCbIdx]+=floatval($bet['win']);
				}
			}
			
			
			//[ "id","cbType","names", "sum","flag" ]  "items":[[],[],[[48,0,"牛,虎,蛇,马,羊,狗",9.0,48]]]
			if($_POST['btcs']=='lm'||$_POST['btcs']=='dzy'||$_POST['btcs']=='mx'||$_POST['btcs']=='wsl'||$_POST['btcs']=='sxl'||$_POST['btcs']=='bz'){
				$items=array();
				$cbIdx=intval($_POST['cbIdx']);
				$items[]=array();
				$items[]=array();
				//$items[]=$conn->createCommand("select id,SUBSTRING(ltrTypeId,instr(ltrTypeId,'_')+1),betType,betSum,SUBSTRING(ltrTypeId,instr(ltrTypeId,'_')+1) from tbl_bet where `ltrBtcId` ='{$_POST['btcs']}' order by ltrTypeId")->queryAll(false);
				$itemWhere="";
				if($cbIdx!=-1){
					$itemWhere="and `ltrTypeId` ='".$_POST['btcs']."_".$cbIdx."'";
					/*}else{
						$itemWhere="and `ltrBtcId` ='".$_POST['btcs']."'";
					}*/
					$btcLen=strlen($_POST['btcs'])+1+1;
					$itemRec=$conn->createCommand("select id,substring(ltrTypeId,$btcLen),betCode,sum(betSum$sumHuo) as betSum,id from tbl_bet where termid='{$this->term}' $itemWhere $whereStatUser group by ltrTypeId,betCode order by betSum desc,REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(',',betCode,','),',1,',',01,'),',2,',',02,'),',3,',',03,'),',4,',',04,') asc")->queryAll(false);
							/*for($itemI=0;$itemI<count($itemRec);$itemI++){
								$itemCodes=split(',',$itemRec[$itemI][2]);
								for($itemJ=0;$itemJ<count($itemCodes);$itemJ++){
									$itemCodes[$itemJ]=str_pad($itemCodes[$itemJ]+1, 2, "0", STR_PAD_LEFT);
								}
								$itemRec[$itemI][2]=implode(',',$itemCodes);
							}*/
					CommonUtil::metaData($itemRec,array('int','int','','float','int'));
					if(isset($_POST['itemIds'])&&strlen($_POST['itemIds'])>0){
						if(count($itemRec)==count(split(',',$_POST['itemIds']))){
							$items[]=array();
						}else{
							$items[]=$itemRec;
						}
					}else{
						$items[]=$itemRec;
					}
					
					foreach($itemRec as $itemI){
						$betCodes=split(',',$itemI[2]);
								//$betCodes is nameIdx
								$codeLen=count($betCodes);
								foreach($betCodes as $betCode){
									$btcs[$_POST['btcs']]['name'][$betCode]+=floatval($itemI[3]/$codeLen);
									//$btcs[$_POST['btcs']]['namev'][]="$betCode,$itemI[3],".floatval($itemI[3]/$codeLen);
								}
					}
				
				}else{
					$items[]=array();
				}
				$btcs[$_POST['btcs']]['items']=$items;
				$btcs[$_POST['btcs']]["minFlag"]=-1;
				$btcs[$_POST['btcs']]["maxFlag"]=0;
				if($cbIdx!=-1){
					$btcs[$_POST['btcs']]["cbIdx"]=$cbIdx;
				}
				//if(!isset($btcs[$_POST['btcs']]['namev'])){
				//	$btcs[$_POST['btcs']]['namev']=array();
				//}
			}
			
			$refresh['btcs']=$btcs;
		}
		if(Yii::app()->user->getState('countDown')===NULL){
			Yii::app()->user->setState('countDown',true);
		}
		if(Yii::app()->user->getState('countDown')==true){
			$refresh['remainTimes']=array($notTeAutoRemainTime,$teAutoRemainTime);
		}
		$refresh['autoCloseTimes']=array($ltrRec['notTeAutoCloseTime'],$ltrRec['teAutoCloseTime'],$ltrRec['teAutoCloseTime']);
		$refresh['term']=$this->term;
		
		$code=$conn->createCommand("select zm1,zm2,zm3,zm4,zm5,zm6,tm from tbl_ltr_codes where term='{$this->term}'")->queryRow(false);
		$refresh['code']=$code;
		
		$ztTypes='';
		for($i=0;$i<6;$i++){
			$ztTypes.=",zt_{$i},zt_{$i}_ds,zt_{$i}_dx,zt_{$i}_hsds,zt_{$i}_wsdx,zt_{$i}_qs,zt_{$i}_sb";
		}
		$ztTypes=substr($ztTypes,1);
		$statTypes=array('tm,tmds,tmdx,tmhsds,tmwsdx,tmqs,tmsb','zm,zhds,zhdx','lm','tx','bbds,bbdx','mx','sx,ws','sxl,wsl','bz',$ztTypes,'dzy','qsb','zx','qm_0,qm_1,qm_2,qm_3,qm_4,qm_5,qm_6,qm_7');
		foreach($statTypes as $statType){
			$statRec=$conn->createCommand("select sum(if(betuserid='".$userid."',".($huo==1?"0":"-betSum").",betSum".$sumHuo.")) as bet from tbl_bet where termid='".$this->term."' and `ltrBtcId` in ('".str_replace(",","','",$statType)."') and statUser_$roleid='".$userid."'")->queryRow(false);
			$refresh['stat'][]=$statRec[0];
		} 
		
		echo CJSON::encode($refresh);
		Yii::app()->end(); 
		
	}
	
	/**
	 * tbl_rate_adjust_para
	 * @return unknown_type
	 */
	public function actionRateAdjustParaJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$sql="select `idx`,`step`,`sum` from tbl_rate_adjust_para order by idx";
        $records=Yii::app()->db->createCommand($sql)->queryAll(true);
		$result=array("data"=>$records,"success"=>true);
		echo CJSON::encode($result);
		//echo '{"data":[{"idx":0,"step":0.5,"sum":10000},{"idx":1,"step":1.0,"sum":20000},{"idx":2,"step":1.0,"sum":3000},{"idx":3,"step":0.01,"sum":50000},{"idx":4,"step":0.02,"sum":20000},{"idx":5,"step":0.5,"sum":30000},{"idx":6,"step":0.5,"sum":100000},{"idx":7,"step":0.5,"sum":100000},{"idx":8,"step":0.5,"sum":200000},{"idx":9,"step":0.5,"sum":100000},{"idx":10,"step":0.5,"sum":10000},{"idx":11,"step":0.01,"sum":23000},{"idx":12,"step":0.02,"sum":20000},{"idx":13,"step":0.5,"sum":5000},{"idx":14,"step":0.01,"sum":30000},{"idx":15,"step":0.05,"sum":50000},{"idx":16,"step":0.01,"sum":20000}],"success":true}';
		
		Yii::app()->end(); 
	}
	
	public function actionSetBmsx()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$sql="select `idx`,`step`,`sum` from tbl_rate_adjust_para order by idx";
        $records=Yii::app()->db->createCommand($sql)->queryAll(true);
		$result=array("data"=>$records,"success"=>true);
		echo CJSON::encode($result);
		//echo '{"data":[{"idx":0,"step":0.5,"sum":10000},{"idx":1,"step":1.0,"sum":20000},{"idx":2,"step":1.0,"sum":3000},{"idx":3,"step":0.01,"sum":50000},{"idx":4,"step":0.02,"sum":20000},{"idx":5,"step":0.5,"sum":30000},{"idx":6,"step":0.5,"sum":100000},{"idx":7,"step":0.5,"sum":100000},{"idx":8,"step":0.5,"sum":200000},{"idx":9,"step":0.5,"sum":100000},{"idx":10,"step":0.5,"sum":10000},{"idx":11,"step":0.01,"sum":23000},{"idx":12,"step":0.02,"sum":20000},{"idx":13,"step":0.5,"sum":5000},{"idx":14,"step":0.01,"sum":30000},{"idx":15,"step":0.05,"sum":50000},{"idx":16,"step":0.01,"sum":20000}],"success":true}';
		
		Yii::app()->end(); 
	}
	
	public function actionModifyRateAdjustPara(){
		$this->layout=false;
		header('Content-type: application/json');
		$sql="update tbl_rate_adjust_para set `step`=:step,`sum`=:sum where idx=:idx";
		$command=Yii::app()->db->createCommand($sql);
		$idxs=split(',',$_POST['idxs']);
		$steps=split(',',$_POST['steps']);
		$sums=split(',',$_POST['sums']);
		$i=0;
		$len=count($idxs);
		for(;$i<$len;$i++){
			$command->bindParam(":step",$steps[$i],PDO::PARAM_INT);
			$command->bindParam(":sum",$sums[$i],PDO::PARAM_INT);
			$command->bindParam(":idx",$idxs[$i],PDO::PARAM_INT);
			$command->execute();
		}
		echo '{"msg":"","success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * btc	tm
		idx	0
		leis	0,1,2
		pans	0,1
		step	0.8 
		
	 or:lei	0
		leis	0,1,2
		pan	0
		pans	0,1
		rate	1
		
	btc	lm
	idx	0
	lei	1
	leis	0,1,2
	pans	0,1
	rate	20,90
	 * @return unknown_type
	 */
	public function actionChangeRate(){
		$this->layout=false;
		header('Content-type: application/json');
		$rate=array();
		$step=0;
		$btc=$_POST['btc'];
		$pans=split(',',$_POST['pans']);
		$leis=split(',',$_POST['leis']);
		$idxs=split(',',$_POST['idx']);
		foreach($idxs as $idx){
		$sql="update tbl_ltr_type set ";
		foreach($leis as $lei){
			$sql.="`rate0$lei`=`rate0$lei`+:step,";
		}
		$sql=substr($sql,0,strlen($sql)-1);
		
		if(isset($_POST['step'])){
			$step=$_POST['step'];
			//$sql.="`rate00`=`rate00`+:step,`rate01`=`rate01`+:step,`rate02`=`rate02`+:step";
		}elseif(isset($_POST['rate'])){
			$rate=split(',',$_POST['rate']);
			$rateLei=$leis[0];
			$oldRateSql="select rate0$rateLei,rate1$rateLei from tbl_ltr_type";
			//$sql.="`rate00`=`rate00`+:rate0-rate0$rateLei,`rate01`=rate01+:rate0-rate0$rateLei,`rate02`=rate02+:rate0-rate0$rateLei";
			if(count($rate)>1){
				//$sql.=",`rate10`=`rate10`+:rate1-rate1$rateLei,`rate11`=rate11+:rate1-rate1$rateLei,`rate12`=rate12+:rate1-rate1$rateLei";
				foreach($leis as $lei){
					$sql.=",`rate1$lei`=`rate1$lei`+:step";
				}
			}
			$whereSql=" where id=:id";
			$paramId=array(":id"=>$btc."_".$idx);
			if($btc=='tm'||$btc=='zm'){
				$paramId=array(":id"=>$btc."_".$_POST['pan']."_".$idx);
			}
			$oldRateRec=Yii::app()->db->createCommand($oldRateSql.$whereSql)->queryRow(false,$paramId);
			$step=$rate[0]-$oldRateRec[0];
			if(count($rate)>1){
				$step=array($step,$rate[1]-$oldRateRec[1]);
			}
		}
		$whereSql=" where id=:id";
		if($btc=='tm'||$btc=='zm'){
			if(count($pans)>1)
				$whereSql.=" or id=:idB";
		}
		//echo $sql.$whereSql;
		//print_r($step);
		$command=Yii::app()->db->createCommand($sql.$whereSql);
		if(is_array($step)){
			$command->bindParam(":step",$step[0],PDO::PARAM_INT);
			$command->bindParam(":step1",$step[1],PDO::PARAM_INT);
		}else{
			$command->bindParam(":step",$step,PDO::PARAM_INT);
		}
		if($btc=='tm'||$btc=='zm'){
			$id1=$btc."_{$pans[0]}_".$idx;
			//echo $id1;
			$command->bindParam(":id",$id1,PDO::PARAM_STR);			
			if(count($pans)>1){
				$id2=$btc."_{$pans[1]}_".$idx;
				$command->bindParam(":idB",$id2,PDO::PARAM_STR);
			}
		}else{
			$id1=$btc."_".$idx;
			$command->bindParam(":id",$id1,PDO::PARAM_STR);
		}
		$command->execute();
		}

		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	btId	lm_0
	btc	lm
	idx	0
	nameIdx	0
	rate	1,0
	
	btId	lm_1
	btc	lm
	idx	1
	nameIdx	0
	rate	0
	*/
	public function actionTrimRate(){
		$this->layout=false;
		header('Content-type: application/json');
		$rate=split(',',$_POST['rate']);
		$update='';
		for($i=0;$i<count($rate);$i++){
			$update.=",rate{$i}0={$rate[$i]},rate{$i}1={$rate[$i]},rate{$i}2={$rate[$i]}";
		}
		Yii::app()->db->createCommand("update tbl_adjrate set ".substr($update,1)." where btId='{$_POST['btId']}' and nameIdx={$_POST['nameIdx']}")->execute();
		echo '{"success":true,"btc":"'.$_POST['btc'].'","idx":'.$_POST['idx'].',"nameIdx":'.$_POST['nameIdx'].'}';
	}

}