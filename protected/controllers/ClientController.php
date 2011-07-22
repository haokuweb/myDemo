<?php

class ClientController extends Controller
{
public function actionRefreshJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$refresh=array();
		$conn=Yii::app()->db;
		$onlineN=$conn->createCommand("select count(*) from  tbl_online_user where sessionid='".session_id()."'")->queryRow(false);
		if($onlineN[0]==0){
			echo "this.location.href='/main/login.do'";
			Yii::app()->user->logout();
	    	Yii::app()->end();
		}else{
			$conn->createCommand("update tbl_online_user set lasttime=sysdate() where sessionid='".session_id()."'")->execute();
		}
		if($_POST['marChangeFlag']==-1){
			$refresh['marChangeFlag']=0;
			$refresh['marMsg']=implode(',',$conn->createCommand("select message from tbl_marquee where showMar=1 order by updatedTime desc")->queryColumn());
		}
		$ltrRec=$conn->createCommand("select resultDate,notTeAutoCloseTime,teAutoCloseTime from tbl_ltr where term='".$this->term."'")->queryRow();
		$notTeAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['notTeAutoCloseTime'])-time();
		$teAutoRemainTime=strtotime($ltrRec['resultDate'].' '.$ltrRec['teAutoCloseTime'])-time();
		if(isset($_POST['btcs'])){
			$btcs=array();
			$termStatus=CommonUtil::getCachedTermStatus();
			$btcsRec=$conn->createCommand("select t.*,(SELECT IF(r.open=0,0,t.open) FROM tbl_default_rate r WHERE r.id=t.type OR (r.id='txbx' AND t.type='tx' AND t.id='tx_1') OR (r.id='txfbx' AND t.type='tx' AND t.id<>'tx_1') OR (r.id='sxbx' AND t.type='sx' AND t.id='sx_1') OR (r.id='sxfbx' AND t.type='sx' AND t.id<>'sx_1') OR (r.id='ws0' AND t.type='ws' AND t.id='ws_0') OR (r.id='wsf0' AND t.type='ws' AND t.id<>'ws_0')) AS defaultopen from tbl_ltr_type t where t.`btc` in ('".str_replace(",","','",$_POST['btcs'])."') order by btc,REPLACE(RIGHT(id, 2),'_','0')")->queryAll();
			
			foreach($btcsRec as $btc){
				if($btc['btc']=='tm'||$btc['btc']=='zm'){
					if($btc['btc'].'_'.$_POST['pan']!=$btc['type']){
						continue;
					}
				}
				$rate=floatval($btc['rate0'.Yii::app()->user->lei]);
				$rate1=floatval($btc['rate1'.Yii::app()->user->lei]);
				if($rate1>0){
					$rate=array($rate,$rate1);
				}
				//$openStatus=(strpos($btc['btc'],'tm')===false||strpos($btc['btc'],'tm')>0)?$termStatus['ftmstatus']:$termStatus['tmstatus'];
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
				$btcs[$btc['btc']]['bet'][]=floatval($btc['bet']);
				$btcs[$btc['btc']]['bh'][]=floatval($btc['bh']);
				//$btcs[$btc['btc']]['adjRate'][]=$btc['adjRate'];
				//$btcs[$btc['btc']]['name'][]=$btc['adjRate'];
				//$btcs[$btc['btc']]['items'][]=$btc['adjRate'];
				//$btcs[$btc['btc']]['cbIdx']=
			}
			
			$adjRateRec=$conn->createCommand("select * from tbl_adjrate where `btc` in ('".str_replace(",","','",$_POST['btcs'])."') order by btId,nameIdx")->queryAll();
			foreach($adjRateRec as $adjRate){
				$rate=floatval($adjRate['rate0'.Yii::app()->user->lei]);
				$rate1=floatval($adjRate['rate1'.Yii::app()->user->lei]);
				if(is_array($btcs[$adjRate['btc']]['rate'][$adjRate['idx']])){
					$rate=array($rate,$rate1);
				}
				$btcs[$adjRate['btc']]['adjRate'][$adjRate['idx']][]=$rate;
			}
			
			
			$refresh['btcs']=$btcs;
		}
		$refresh['remainTimes']=array($notTeAutoRemainTime,$teAutoRemainTime);
		$refresh['term']=$this->term;
		$refresh['creditSum']=Yii::app()->user->creditSum;
		if($this->term != Yii::app()->user->term){
			$refresh['usedSum']=0;
			Yii::app()->user->setState('usedSum',0);
			Yii::app()->user->setState('term',$this->term);
		}else{
			$refresh['usedSum']=Yii::app()->user->usedSum;
		}
		echo CJSON::encode($refresh);
		Yii::app()->end(); 
		
	}

}