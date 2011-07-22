<?php

class MainController extends Controller
{
	//public $layout='//layouts/default';
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
				'minLength'=>4,
				'maxLength'=>4,
				//'width'=>60,
				//'height'=>30,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: /main/page/FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	public function actionIndex()
	{
		$m=array();
		$conn=Yii::app()->db;
		$m['msg']=implode('<br />',$conn->createCommand("select message from tbl_marquee where showLogon=1 order by updatedTime desc")->queryColumn());
		$m['site']=$conn->createCommand("select * from tbl_site")->queryRow();
		$m['user']=$conn->createCommand("select * from tbl_user where id='".Yii::app()->user->id."'")->queryRow();
		if(Yii::app()->user->role==$this->roles['hy']){
			$this->render('client',$m);
		}else{
			$this->render('index',$m);
		}
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		$this->layout=false;
		header('Content-type: text/html; charset=utf-8');
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest){
	    		echo $error['message'];
	    	}else{
	        	$msg='404，你懂的. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />'
	        		.'Error Code: '.$error['code'].'<br /> Error Message: '.$error['message'].' <br /><br /> <a href="/">点击返回主页</a>';
	    		$this->renderText($msg);//echo $msg
	    	}
	    }
	    //Yii::app()->end();
	}
	
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			$model=new Login;
			//echo CActiveForm::validate($model);
			$model->attributes=$_POST['Login'];
			//echo "ajax login:";
			//var_dump($_POST['Login']);
			if($model->validate() && $model->login()){
				//.(Yii::app()->user->checkAccess('addAdmin')).'&id='.(Yii::app()->user->id).
				$rUrl="/";
				if(Yii::app()->user->role==$this->roles['hy']){
					$rUrl="/main/confirm.do";
				}
				echo '{"targetUrl":"'.$rUrl.'","success":true}';//Yii::app()->user->returnUrl
			}else{
				echo '{"msg":"';
				//implode(",",$model->getErrors());
				foreach($model->getErrors() as $errors){
					echo '<br />'.implode(",",$errors);
				}
				
				echo '","success":false}';
			}
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['Login']))
		{
			$model=new Login;
			$model->attributes=$_POST['Login'];
			//var_dump($_POST['Login']);
			//$model->getError();
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()){
				$this->redirect("/");//Yii::app()->user->returnUrl
			}
						
		}
		// display the login form
		$this->render('login');
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->db->createCommand("delete from tbl_online_user where sessionid='".session_id()."'")->execute();
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionConfirm()
	{
	    $this->render('confirm');	
	}
	
	public function actionModifyDefaultRate()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$sql="update tbl_default_rate set `rate00`=:rate00,`rate01`=:rate01,`rate02`=:rate02,`rate10`=:rate10,`rate11`=:rate11,`rate12`=:rate12,`open`=:open where id=:id";
		$command=Yii::app()->db->createCommand($sql);
		$i=0;
		foreach(split(',',$_POST['ids']) as $id){
			$data=split(',',$_POST[$id]);
			$command->bindParam(":rate00",$data[0],PDO::PARAM_INT);
			$command->bindParam(":rate01",$data[1],PDO::PARAM_INT);
			$command->bindParam(":rate02",$data[2],PDO::PARAM_INT);
			$command->bindParam(":rate10",$data[3],PDO::PARAM_INT);
			$command->bindParam(":rate11",$data[4],PDO::PARAM_INT);
			$command->bindParam(":rate12",$data[5],PDO::PARAM_INT);
			$command->bindParam(":open",$data[6],PDO::PARAM_INT);
			$command->bindParam(":id",$id,PDO::PARAM_STR);
			$i+= $command->execute();
		}
		echo '{"msg":"update rows: '.$i.'","success":true}';
		Yii::app()->end(); 
	}
	
	//{"items":[[89387,"xcf6",5,"特码A02",500.0,"42.38",0.9,"14:04:52","04-25 22:34:11",1]],"success":true}
	// "id", "betUser", "role", "betType","betSum", "rate", "prorate", "betTime","cancelTime", "valid" | term
	public function actionCancelItemsJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$items=array();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(isset($_POST['itemId'])&&$_POST['itemId']!=''){
				$items=Yii::app()->db->createCommand("select id,betUserId,role,betType,betSum,rate,prorate_0,substring(betTime,12),'',0 from tbl_bet where id='{$_POST['itemId']}' and termid='{$this->term}'")->queryAll(false);
			}
		}else{
			$items=Yii::app()->db->createCommand("select id,betUserId,role,betType,betSum,rate,prorate_0,substring(betTime,12),substring(cancelTime,6),1 from tbl_bet_cancel where termid='{$this->term}'")->queryAll(false);
		}
		//echo '{"items":[],"success":true}';
		echo CJSON::encode(array('items'=>$items,"success"=>true));
		Yii::app()->end(); 
	}
	
	/**
	 * itemId	89387
		status	0  恢复
	 * @return unknown_type
	 */
	public function actionChangeItem(){
		$this->layout=false;
		header('Content-type: application/json');
		if($_POST['status']==1){//注销
			Yii::app()->db->createCommand("insert into tbl_bet_cancel(`id`,`betType`,`betCode`,`rebate`,`betUserId`,`betUserName`,`betSum`,`betTime`,`bhUserId`,`bhUserName`,`hyResult`,`ltrTypeId`,`ltrTypeName`,`ltrBtcId`,`ltrType`,`prorate_0`,`prorate_1`,`prorate_2`,`prorate_3`,`prorate_4`,`statUser_0`,`statUser_1`,`statUser_2`,`statUser_3`,`statUser_4`,`rate`,`rate0`,`rate1`,`role`,`statUserId`,`statUserName`,`victory`,`termid`,`lei`,`cancelTime`) select `id`,`betType`,`betCode`,`rebate`,`betUserId`,`betUserName`,`betSum`,`betTime`,`bhUserId`,`bhUserName`,`hyResult`,`ltrTypeId`,`ltrTypeName`,`ltrBtcId`,`ltrType`,`prorate_0`,`prorate_1`,`prorate_2`,`prorate_3`,`prorate_4`,`statUser_0`,`statUser_1`,`statUser_2`,`statUser_3`,`statUser_4`,`rate`,`rate0`,`rate1`,`role`,`statUserId`,`statUserName`,`victory`,`termid`,`lei`,sysdate() from tbl_bet where id='{$_POST['itemId']}'")->execute();
			Yii::app()->db->createCommand("delete from tbl_bet where id='{$_POST['itemId']}'")->execute();
		}else{//恢复
			Yii::app()->db->createCommand("insert into tbl_bet(`id`,`betType`,`betCode`,`rebate`,`betUserId`,`betUserName`,`betSum`,`betTime`,`bhUserId`,`bhUserName`,`hyResult`,`ltrTypeId`,`ltrTypeName`,`ltrBtcId`,`ltrType`,`prorate_0`,`prorate_1`,`prorate_2`,`prorate_3`,`prorate_4`,`statUser_0`,`statUser_1`,`statUser_2`,`statUser_3`,`statUser_4`,`rate`,`rate0`,`rate1`,`role`,`statUserId`,`statUserName`,`victory`,`termid`,`lei`) select `id`,`betType`,`betCode`,`rebate`,`betUserId`,`betUserName`,`betSum`,`betTime`,`bhUserId`,`bhUserName`,`hyResult`,`ltrTypeId`,`ltrTypeName`,`ltrBtcId`,`ltrType`,`prorate_0`,`prorate_1`,`prorate_2`,`prorate_3`,`prorate_4`,`statUser_0`,`statUser_1`,`statUser_2`,`statUser_3`,`statUser_4`,`rate`,`rate0`,`rate1`,`role`,`statUserId`,`statUserName`,`victory`,`termid`,`lei` from tbl_bet_cancel where id='{$_POST['itemId']}'")->execute();
			Yii::app()->db->createCommand("delete from tbl_bet_cancel where id='{$_POST['itemId']}'")->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionCurltrJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		//$site=Yii::app()->user->site;
		$ltrRec=$conn->createCommand("select * from tbl_ltr where term='".$this->term."'")->queryRow();
		$code=$conn->createCommand("select zm1,zm2,zm3,zm4,zm5,zm6,tm from tbl_ltr_codes where term='{$this->term}'")->queryRow(false);
		if($code==false){
			$code=array(null,null,null,null,null,null,null);
		}
		$ltr['codes']=$code;
		$ltr['id']=$this->term;
		$ltr['notTeAutoCloseTime']=$ltrRec['notTeAutoCloseTime'];
		$ltr['subTeAutoCloseTime']=$ltrRec['teAutoCloseTime'];
		$ltr['teAutoCloseTime']=$ltrRec['teAutoCloseTime'];
		$ltr['resultDate']=$ltrRec['resultDate'];
		$ltr['resultTime']=$ltrRec['resultTime'];
		$ltr['status']=intval($ltrRec['status']);
		$ltr['settling']=$ltrRec['settling']==1?true:false;
		echo CJSON::encode(array('ltr'=>$ltr));
		Yii::app()->end(); 
	}
	
	//defaultTrate.json or defaultRate.json?
	/**
	 * 
	 * @return fields:['id','name','rate00','rate01','rate02','rate10','rate11','rate12']:
	 */
	public function actionDefaultRateJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$sql="select `id`,`name`,`rate00`,`rate01`,`rate02`,`rate10`,`rate11`,`rate12`,open from tbl_default_rate order by sid";
        $records=Yii::app()->db->createCommand($sql)->queryAll(false);
		$result=array("data"=>$records,"success"=>true);
		echo CJSON::encode($result);
		Yii::app()->end(); 
	}
	
	/**
	 * 
	 * @return fields : [ "id", "iddate" ]
	 */
	public function actionItemTermJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$termRec=Yii::app()->db->createCommand("select term,concat(term,'  (',DATE_FORMAT(resultDate,'%m月%d日'),')') as resultdate from tbl_ltr where status=2 order by term desc")->queryAll(false);
		echo CJSON::encode(array("data"=>array_merge(array(array("0","----选择期次----")),$termRec)));
		Yii::app()->end(); 
	}
	
	/**
	 * 
	 * @return  fields:['id','message','grants','showLogon','showMar']
	 */
	public function actionMarqueesJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$marqueeRec=Yii::app()->db->createCommand("select id,message,grants,showLogon,showMar from tbl_marquee where showLogon=1 order by updatedTime desc")->queryAll(false);
		CommonUtil::metaData($marqueeRec,array('int','','int','bool','bool'));
		echo CJSON::encode(array("data"=>$marqueeRec));
		//echo '{"data":[[1,"::::: 欢迎光临【XXXX】:::::2010年/第123期香港六合彩于10月25日21:00分开奖,当天下午17:30前开盘, 请各会员在下注前阅读规则说明。下注后查看注单明细。开奖后一切投注均视为无效注单。若有任何异动，以香港赛马会公告为准！！！  备用网址:222.cd123.us",63,true,true],[3,"sdfasdfasdfasdfasdfasdf",35,false,true]]}';
		Yii::app()->end(); 
	}
	
	/**
	 * grants	1
		grants	2
		grants	4
		grants	8
		grants	16
		grants	32
		marId	1
		message
		showLogon	on
		showMar	on
	 * @return unknown_type
	 */
	public function actionModifyMarquee(){
		$this->layout=false;
		header('Content-type: application/json');
		
		$cmd=Yii::app()->db->createCommand("update tbl_marquee set message=:message,grants=:grants,showLogon=:showLogon,showMar=:showMar,updatedTime=sysdate()  where id=:id");
		if(isset($_POST['marId'])){
			$showLogon=$_POST['showLogon']=="on"?1:0;
			$showMar=$_POST['showMar']=="on"?1:0;
			$cmd->bindParam(":message",$_POST['message'],PDO::PARAM_STR);
			$cmd->bindParam(":grants",array_sum($_POST['grants']),PDO::PARAM_INT);
			$cmd->bindParam(":showLogon",$showLogon,PDO::PARAM_INT);
			$cmd->bindParam(":showMar",$showMar,PDO::PARAM_INT);
			$cmd->bindParam(":id",$_POST['marId'],PDO::PARAM_INT);
			$cmd->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionAddMarquee(){
		$this->layout=false;
		header('Content-type: application/json');
		
		$cmd=Yii::app()->db->createCommand("insert into tbl_marquee(message,grants,showLogon,showMar,updatedTime) values (:message,:grants,:showLogon,:showMar,sysdate())");
		if(isset($_POST['marId'])){
			$cmd->bindParam(":message",$_POST['message'],PDO::PARAM_STR);
			$cmd->bindParam(":grants",array_sum($_POST['grants']),PDO::PARAM_INT);
			$cmd->bindParam(":showLogon",$_POST['showLogon']=="on"?1:0,PDO::PARAM_INT);
			$cmd->bindParam(":showMar",$_POST['showMar']=="on"?1:0,PDO::PARAM_INT);
			$cmd->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * 
	 * @return fields : [ "id", "name", "role", "logonTime" ]
	 */
	public function actionOnlinersJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$role=Yii::app()->user->role;
		$userid=Yii::app()->user->id;
		$onlineRec=Yii::app()->db->createCommand("select o.id,o.username,o.role,substring(logintime,12),o.level from tbl_online_user o left join tbl_user u on o.userid=u.id where u.parent_{$role}='$userid' or o.userid='$userid' order by logintime desc")->queryAll(false);
		CommonUtil::metaData($onlineRec,array('','','int','','int'));
		echo CJSON::encode(array("data"=>$onlineRec,"count"=>count($onlineRec)));
		Yii::app()->end(); 
	}
	
	/**
	 * ltrType	-1
		reportType	1
		term	2011009
		
		ltrType	-1
		reportType	1
		term	0
		
		ltrType	-1
		reportType	0 onliner.role == 5 ? 1 : 0  分类帐，总账	
		term	0
		
		parent	1
		statRole	2
		ltrType	-1
		term	20110228-20110322
		statUser	ad1
		reportType	0
		
		大股东 注数 会员下注         各级补货 实际金额 公司占成   公司赚佣   会员结果       代理结果      总代理结果    股东结果 大股东结果  公司盈亏 
		ad(ad) 15 11,490.00 2,138.58 7,883.59 69%      0.00  12,599.15 12,599.15 12,599.15  12,599.15 11,951.20 -11,951.20 

		"tz":[
	{"(prorate=acceptSums[role]/hySum)acceptSums":[7883.585,3606.415,0.0,0.0,0.0,0.0],
	"betSum":13628.5848472538,"bhUserId":null,"bhUserName":null,"count":15,
	(盈亏)"gains":[-11951.199,-647.951,0.0,0.0,0.0,12599.15],
	(会员下注)"hySum":11490.0,"id":16,"ltrType":-1,"ltrTypeId":null,"ltrTypeName":null,
	(结果 )"results":[0.0,11951.199,12599.15,12599.15,12599.15,12599.15],
	"role":1,(佣赚)"squSums":[0.0,148.5,0.0,0.0,0.0,0.0],"statUserId":"ad","statUserName":"ad",
	(实际 )"upSums":[0.0,7883.585,11490.0,11490.0,11490.0,11490.0]
	},
	各级补货：betSum - hySum
	
	分类账：
	{"acceptSums":[900.0,50.0,0.0,0.0,50.0,0.0],
	"betSum":1000.0,"bhUserId":null,"bhUserName":null,"count":1,
	"gains":[0.0,0.0,0.0,0.0,0.0,0.0],
	"hySum":1000.0,"id":46,"ltrType":0,"ltrTypeId":"tm","ltrTypeName":"特码",
	"results":[0.0,0.0,0.0,0.0,0.0,0.0],
	"role":0,"squSums":[0.0,0.0,0.0,0.0,0.0,0.0],"statUserId":"qq133","statUserName":"admin",
	"upSums":[0.0,900.0,950.0,950.0,950.0,1000.0]}
	
	hySum : 1108.0   sum(betSum)
	pror_0...4:  0.9 0.05 0 0 0.05 0
	get acceptSums : hySum*pro
	get upSums 实际: upSums[5]=hySum;i=4..0  upSums[i]= upSums[i+1] - acceptSums[i]
	get results: results[5]= sum(hyReuslt) i=4..0  results[i]= results[i+1] - results[5]*pror[i]
	[21157.18, *0.05=1057.859 ] 
	get gains:  gains[5]= results[5]   i=4..0 gains[i] = -results[5]* pror[i]
	results=hyResult
	实际金额 upSums=
	实际金额=会员下注减去本角色以下的角色的占成的金额
	1.计算顺序是先算会员结果
	会员结果就是各个注单的结果的和
	2.然后计算代理盈亏
	代理盈亏=-(会员结果*代理占成)
	3.算代理结果
	代理结果=会员结果+代理盈亏
	4.算总代理盈亏
	总代理盈亏=-(会员结果*总代理占成)
	5.算总代理结果
	总代理结果=代理结果+总代理盈亏
	
		
	 * @return unknown_type
	 */
	public function actionReportJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$reportType=intval($_POST['reportType']);
		$ltrType=intval($_POST['ltrType']);
		
		$hyTz=array();
		$ltrTypesId=array('tm','tmsm','tmsb','zm','zmsm','zt','ztsm','ztsb','lm','tx','bb','mx','sx','ws','sxl','wsl','bz','dzy','qsb','zx','qm');
		$ltrTypesName=array("特码","特码两面","特码色波","正码","正码两面","正特","正特两面","正特色波","连码","特肖","半波","六肖","生肖","尾数","生肖连","尾数连","五不中","多中一","七色波","总肖","七码");
		$conn=Yii::app()->db;
		//"betSum":1000.0,"bhUserId":null,"bhUserName":null,"count":1,
		//"hySum":1000.0,"id":46,"ltrType":0,"ltrTypeId":"tm","ltrTypeName":"特码",
		//"role":0,"statUserId":"qq133","statUserName":"admin",
		$groupBy="ltrBtcId";
		$term=$_POST['term'];
		if(strpos($term,'-')!==false){
			//20110228-20110322
			$term=split('-',$term);
			$term=" and betTime>STR_TO_DATE('{$term[0]}','%Y%m%d') and betTime<STR_TO_DATE('{$term[1]}','%Y%m%d')";
		}else{
			if($term==0){
				$term=$this->term;
			}
			$term=" and termid='$term'";
		}
		if($ltrType!=-1){
			$term.=" and ltrType=$ltrType";
		}
		$statUser="";
		$statRole=5;
		//statUser	xcf3  role 2 statRole	1   statuser:xcf3 role 2    rec role: 3, xcf4
		//statUser	xcf3  role 2 parent 1  statRole 2  statuser:xcf2  role 1  rec role: 2, xcf3
		if(isset($_POST['statUser'])){
			$statUser=$_POST['statUser'];
			$statRole=$_POST['statRole']+1;
			if(isset($_POST['parent'])){
				$parentRec=$conn->createCommand("select parentId from tbl_user where id='$statUser'")->queryRow();
				$statUser=$parentRec['parentId'];//xcf2 role: 1
				$statRole=$statRole-2;
			}
		}else{
			$statUser=Yii::app()->user->id;
			$statRole=Yii::app()->user->role;
		}
		
		if($reportType==0){
			if(($statRole+1)<5){
				$userWhere=" and statUser_$statRole='$statUser' and statUser_".($statRole+1)." is not null";
				//统计下一级的用户
				$groupBy="statUser_".($statRole+1)."";
				$statUserId="statUser_".($statRole+1);
			}else{
				$userWhere=" and statUser_$statRole='$statUser'";
				$groupBy="betUserId";
				$statUserId="betUserId";
			}
		}else{
			$groupBy="ltrType";
			$statUserId="'$statUser'";
			//if($statRole!=5){
			$userWhere=" and statUser_$statRole='$statUser'";
			/*}else{
				$userWhere=" and betUserId='$statUser'";
			}*/
		}

		//SUM(hyResult-hyResult*prorate_4)+sum(buhuo_result_4) AS results_4
		$selectSql="SELECT COUNT(id) AS count,SUM(betSum) AS hySum,$statUserId as statUserId,$statUserId as statUserName,bhUserId,bhUserName,ltrType,
			SUM(betSum*prorate_0) AS acceptSums_0,
			SUM(betSum*prorate_1) AS acceptSums_1,
			SUM(betSum*prorate_2) AS acceptSums_2,
			SUM(betSum*prorate_3) AS acceptSums_3,
			SUM(betSum*prorate_4) AS acceptSums_4,
			0 AS acceptSums_5,
			SUM(betSum) AS upSums_5, 
			SUM(betSum-betSum*prorate_4) AS upSums_4,
			SUM(betSum-betSum*prorate_4-betSum*prorate_3) AS upSums_3, 
			SUM(betSum-betSum*prorate_4-betSum*prorate_3-betSum*prorate_2) AS upSums_2,  
			SUM(betSum-betSum*prorate_4-betSum*prorate_3-betSum*prorate_2-betSum*prorate_1) AS upSums_1,  
			0 AS upSums_0, 
			SUM(hyResult) AS results_5, 
			SUM(hyResult-hyResult*prorate_4) AS results_4, 
			SUM(hyResult-hyResult*prorate_4-hyResult*prorate_3) AS results_3, 
			SUM(hyResult-hyResult*prorate_4-hyResult*prorate_3-hyResult*prorate_2) AS results_2,  
			SUM(hyResult-hyResult*prorate_4-hyResult*prorate_3-hyResult*prorate_2-hyResult*prorate_1) AS results_1,  
			0 AS results_0, 
			SUM(hyResult) AS gains_5,
			SUM(-hyResult*prorate_4) AS gains_4,
			SUM(-hyResult*prorate_3) AS gains_3,
			SUM(-hyResult*prorate_2) AS gains_2,
			SUM(-hyResult*prorate_1) AS gains_1,
			SUM(-hyResult*prorate_0) AS gains_0
			FROM tbl_bet WHERE 1=1 $term";
		
		$i=0;
			$result=array();
			$result['statUser']=$statUser;
			$result['reportType']=$reportType;
			$result['term']=$_POST['term'];
			$result['ltrType']=$ltrType;
			$result['statRole']=intval($statRole);
			$result['bh']=array();
			$result['tz']=array();
			$result['success']=true;
		if($statRole!=5){
		
			$dataReader=$conn->createCommand("$selectSql $userWhere and role=5 GROUP BY $groupBy")->query();
			while(($row=$dataReader->read())!==false) {
				$bhSum=0;
				$bhResult=0;
				$bhWhere='';
				$bhResults=array(0,0,0,0,0);
				if($reportType==0){
					//xcf4 -> xcf6  no possable
					if(($statRole+1)<=4){
						$bhWhere="statUser_".($statRole+1)."='{$row['statUserId']}'";
					}else{
						$bhWhere="1=2";
					}
				}else{
					//xcf4 xcf5 no possable bh
					if(($statRole+1)<=5){
						$bhWhere="ltrType=".$row['ltrType']." and statUser_$statRole='$statUser'";
					}else{
						$bhWhere="1=2";
					}
				}
				$bhSql="select role,sum(betSum) as betSum,sum(hyResult) as hyResult from tbl_bet where $bhWhere $term and role<>5 group by role";	
				//echo "$bhSql\n";
				$bhRec=$conn->createCommand($bhSql)->queryAll();
				
				$rowRole=$reportType==0?($statRole+1):$statRole;

				if($bhRec!=false){
					foreach($bhRec as $bh){
						$bhResults[$bh['role']]=$bh['hyResult'];
						$bhSum+=floatval($bh['betSum']);
						$bhResult+=floatval($bh['hyResult']);
					}
				}
				$bhResults[$rowRole]=-$bhResult;
				
				$hyTz['betSum']=floatval($row['hySum'])+$bhSum;
				$hyTz['bhUserId']=$row['bhUserId'];
				$hyTz['bhUserName']=$row['bhUserName'];
				$hyTz['count']=intval($row['count']);
				$hyTz['hySum']=floatval($row['hySum']);
				$hyTz['id']=++$i;
				//$hyTz['ltrTypeId']=$row['ltrTypeId'];
				//$hyTz['ltrTypeName']=$row['ltrTypeName'];
				if($reportType==0){
					$hyTz['ltrType']=$ltrType;
					$hyTz['ltrTypeId']=null;
					$hyTz['ltrTypeName']=null;
					$hyTz['role']=$statRole+1;
				}else{
					$hyTz['ltrType']=intval($row['ltrType']);
					$hyTz['ltrTypeId']=$ltrTypesId[$row['ltrType']];
					$hyTz['ltrTypeName']=$ltrTypesName[$row['ltrType']];
					$hyTz['role']=$statRole;
				}
				$hyTz['statUserId']=$row['statUserId'];
				$hyTz['statUserName']=$row['statUserName'];
				$hyTz['acceptSums']=array(floatval($row['acceptSums_0']),floatval($row['acceptSums_1']),floatval($row['acceptSums_2']),floatval($row['acceptSums_3']),floatval($row['acceptSums_4']),floatval($row['acceptSums_5']));
				$hyTz['upSums']=array(floatval($row['upSums_0']),floatval($row['upSums_1']),floatval($row['upSums_2']),floatval($row['upSums_3']),floatval($row['upSums_4']),floatval($row['upSums_5']));
				$hyTz['results']=array(floatval($row['results_0'])+$bhResults[0],floatval($row['results_1'])+$bhResults[1],floatval($row['results_2'])+$bhResults[2],floatval($row['results_3'])+$bhResults[3],floatval($row['results_4'])+$bhResults[4],floatval($row['results_5']));
				$hyTz['gains']=array(floatval($row['gains_0'])+$bhResults[0],floatval($row['gains_1'])+$bhResults[1],floatval($row['gains_2'])+$bhResults[2],floatval($row['gains_3'])+$bhResults[3],floatval($row['gains_4'])+$bhResults[4],floatval($row['gains_5']));
				$hyTz['squSums']=array(0,0,0,0,0,0);
				
				$result['tz'][]=$hyTz;
				/*if($reportType==0){
					if(($statRole+1)!=5){
						$result['tz'][]=$hyTz;
					}else{
						$result['bh'][]=$hyTz;
					}
				}else{
				if($statRole!=5){
						$result['tz'][]=$hyTz;
					}else{
						$result['bh'][]=$hyTz;
					}
				}*/
			}
		
		}
		
		$userWhere=" and betUserId='$statUser'";
		
		if($reportType==0){
			$groupBy="betUserId";
		}else{
			$groupBy="ltrType";
		}
		
		$dataReader=$conn->createCommand("$selectSql $userWhere group by $groupBy")->query();
		
		while(($row=$dataReader->read())!==false) {
				
				$hyTz['betSum']=floatval($row['hySum']);
				$hyTz['bhUserId']=$row['bhUserId'];
				$hyTz['bhUserName']=$row['bhUserName'];
				$hyTz['count']=intval($row['count']);
				$hyTz['hySum']=floatval($row['hySum']);
				$hyTz['id']=++$i;
				//$hyTz['ltrTypeId']=$row['ltrTypeId'];
				//$hyTz['ltrTypeName']=$row['ltrTypeName'];
				if($reportType==0){
					$hyTz['ltrType']=$ltrType;
					$hyTz['ltrTypeId']=null;
					$hyTz['ltrTypeName']=null;
					$hyTz['role']=$statRole+1;
				}else{
					$hyTz['ltrType']=intval($row['ltrType']);
					$hyTz['ltrTypeId']=$ltrTypesId[$row['ltrType']];
					$hyTz['ltrTypeName']=$ltrTypesName[$row['ltrType']];
					$hyTz['role']=$statRole;
				}
				$hyTz['statUserId']=$row['statUserId'];
				$hyTz['statUserName']=$row['statUserName'];
				$hyTz['acceptSums']=array(floatval($row['acceptSums_0']),floatval($row['acceptSums_1']),floatval($row['acceptSums_2']),floatval($row['acceptSums_3']),floatval($row['acceptSums_4']),floatval($row['acceptSums_5']));
				$hyTz['upSums']=array(floatval($row['upSums_0']),floatval($row['upSums_1']),floatval($row['upSums_2']),floatval($row['upSums_3']),floatval($row['upSums_4']),floatval($row['upSums_5']));
				$hyTz['results']=array(floatval($row['results_0']),floatval($row['results_1']),floatval($row['results_2']),floatval($row['results_3']),floatval($row['results_4']),floatval($row['results_5']));
				$hyTz['gains']=array(floatval($row['gains_0']),floatval($row['gains_1']),floatval($row['gains_2']),floatval($row['gains_3']),floatval($row['gains_4']),floatval($row['gains_5']));
				$hyTz['squSums']=array(0,0,0,0,0,0);
				
				$result['bh'][]=$hyTz;
			}
		
		/*echo '{"statUser":"qq133","reportType":0,
		"tz":[{"acceptSums":[828.0,46.0,0.0,0.0,46.0,0.0],"betSum":920.0,"bhUserId":null,"bhUserName":null,"count":7,
			"gains":[-29739.777,-1652.21,0.0,0.0,-1652.21,33044.198],"hySum":920.0,"id":3,"ltrType":-1,"ltrTypeId":null,"ltrTypeName":null,
			"results":[0.0,29739.777,31391.988,31391.988,31391.988,33044.198],"role":1,"squSums":[0.0,0.0,0.0,0.0,0.0,0.0],"statUserId":"xcf2","statUserName":"新财富","upSums":[0.0,828.0,874.0,874.0,874.0,920.0]}]
		,"term":"0","ltrType":-1,"statRole":0,"bh":[],"success":true}';*/
		echo CJSON::encode($result);
		Yii::app()->end(); 
	}
	
	/**
	 * bet	1
		limit	20
		start	0
		statUser	xcf6
		term	0
		
		or 
		
		statRole	0
		ltrType	-1
		term	0
		reportType	0
		statUser	xcf2
		start	0
		limit	20
		
		statRole	0
		ltrType	3
		term	2011009
		reportType	0
		statUser	xcf2
		start	0
		limit	20
		
		statRole	0
		ltrType	-1
		term	20110201-20110324
		reportType	0
		statUser	ad
		start	0
		limit	20
		
		statRole:0
		ltrType:-1
		term:0
		reportType:0
		statUser:xcf1
		start:0
		limit:20
		
		statRole	0
		ltrType	-1
		term	20110228-20110322
		reportType	0
		bet	2
		statUser	ad
		start	0
		limit	20

	 * @return count, fields : [ {name : "id",type : "int"}, {name : "betTime"}, {name : "betUserId"}, {name : "betUserName"}, {name : "betType"}, {name : "betSum",type : "float"}, {name : "rate"}, {name : "hyResult",type : "float"}, {name : "victory",type : "bool"}, {name : "prorate_4",type : "float"}, {name : "prorate_3",type : "float"}, {name : "prorate_2",type : "float"}, {name : "prorate_1",type : "float"}, {name : "prorate_0",type : "float"} ]
	 */
	public function actionResultJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		$ltrTypes=array('tm','tmds,tmdx,tmhsds,tmwsdx,tmqs','tmsb','zm','zhds,zhdx','zt','ztds,ztdx,zthsds,ztwsdx,ztqs','ztsb','lm','tx','bbds,bbdx','mx','sx','ws','sxl','wsl','bz','dzy','qsb','zx','qm_0,qm_1,qm_2,qm_3,qm_4,qm_5,qm_6,qm_7');
		
		$users=$_POST['statUser'];
		$statUser=$conn->createCommand("select id,username,role from tbl_user where id=:id")->queryRow(true,array(":id"=>$users));	
		
		$sql="select betSum,betTime,betType,betUserId,betUserName,bhUserId,bhUserName,hyResult,id,ltrTypeId,ltrTypeName,prorate_0,prorate_1,prorate_2,prorate_3,prorate_4,rate,role,'$users' as statUserId,'{$statUser['username']}' as statUserName,victory from tbl_bet where 1=1";
		if(isset($_POST['ltrType'])&&$_POST['ltrType']>=0){
			//$sql.=" and ltrBtcId in ('".str_replace(",","','",$ltrTypes[$_POST['ltrType']])."')";
			$sql.=" and ltrType={$_POST['ltrType']}";
		}
		$term=$_POST['term'];
		if(strpos($term,'-')!==false){
			$term=split('-',$term);
			$sql.=" and betTime>STR_TO_DATE('{$term[0]}','%Y%m%d') and betTime<STR_TO_DATE('{$term[1]}','%Y%m%d')";
		}else{
			if($term==0){
				$term=$this->term;
			}
			$sql.=" and termid='$term'";
		}
		
		$reportBet=0;
		$userWhere='';
		if(isset($_POST['bet'])){
			$reportBet=intval($_POST['bet']);
		}
		$statRole=$statUser['role'];
		if($reportBet==1){
			$userWhere=" and betUserId='$users'";
		}elseif($reportBet==2||$reportBet==3){
			if($statRole<5){
				$userWhere=" and statUser_$statRole='$users' and role<5 and role>=$statRole";
			}else{
				$userWhere=" and 1=2";
			}
		}else{
			if($statRole<5){
				$userWhere=" and statUser_$statRole='$users' and role=5";
			}else{
				$userWhere=" and betUserId='$users'";
			}
		}
		
		$sql.=$userWhere;
		//echo $sql;
		/*$users=array($users);
		while(count($users)>0){
			$betUsers=$users;
			$users=$conn->createCommand("select id,username,parentId,isLeaf,role from tbl_user where parentId in ('".implode("','",$betUsers)."')")->queryColumn();
		}
		//$userRec=$userCmd->queryAll(true,array(":parentIds"=>"'".implode("','",$statUsers)."'"));
		$sql.=" and betUserId in ('".implode("','",$betUsers)."')";*/
		$limit=" limit {$_POST['start']},{$_POST['limit']}";
		$betRec=$conn->createCommand($sql.$limit)->queryAll();
		$count=$conn->createCommand("select count(*) from ".substr($sql,strpos($sql," from")+6))->queryRow(false);
		echo CJSON::encode(array("count"=>$count[0],"items"=>$betRec));
		//echo '{"count":7,"items":[{"betSum":8.0,"betTime":"2011-02-06 12:16:48","betType":"特码A49","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":-6.920000076293945,"id":88904,"ltrTypeId":"tm_0_48","ltrTypeName":"特码A49","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":false},{"betSum":8.0,"betTime":"2011-02-06 12:16:48","betType":"特码A32","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":-6.920000076293945,"id":88903,"ltrTypeId":"tm_0_31","ltrTypeName":"特码A32","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":false},{"betSum":80.0,"betTime":"2011-02-06 12:16:48","betType":"特码A29","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":-69.20000076293945,"id":88902,"ltrTypeId":"tm_0_28","ltrTypeName":"特码A29","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":false},{"betSum":8.0,"betTime":"2011-02-06 12:16:48","betType":"特码A19","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":-6.920000076293945,"id":88901,"ltrTypeId":"tm_0_18","ltrTypeName":"特码A19","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":false},{"betSum":8.0,"betTime":"2011-02-06 12:16:48","betType":"特码A16","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":-6.920000076293945,"id":88900,"ltrTypeId":"tm_0_15","ltrTypeName":"特码A16","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":false},{"betSum":8.0,"betTime":"2011-02-06 12:16:48","betType":"特码A12","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":-6.920000076293945,"id":88899,"ltrTypeId":"tm_0_11","ltrTypeName":"特码A12","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":false},{"betSum":800.0,"betTime":"2011-02-06 12:16:48","betType":"特码A01","betUserId":"xcf6","betUserName":"会员测试","bhUserId":null,"bhUserName":null,"hyResult":33147.998046875,"id":88898,"ltrTypeId":"tm_0_0","ltrTypeName":"特码A01","prorate_0":0.9,"prorate_1":0.05,"prorate_2":0.0,"prorate_3":0.0,"prorate_4":0.05,"rate":"42.3","role":1,"statUserId":"xcf2","statUserName":"新财富","victory":true}],"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * limit	10
		start	30
	 * @return fields:["term","resultTime", "code0","code1","code2","code3","code4","code5","code6","sx0","sx1","sx2","sx3","sx4","sx5","sx6","zh","ds","dx","hsds","zhds","zhdx"]
	 */
	public function actionResultCodeJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		$count=$conn->createCommand("SELECT count(*) from tbl_ltr where YEAR(resultDate)=YEAR(CURRENT_DATE)")->queryRow(false);
		$sql="SELECT SUBSTRING(l.term,5) as term,CONCAT(resultDate,' ',resultTime) as resultdate,zm1,zm2,zm3,zm4,zm5,zm6,tm as zm7 FROM tbl_ltr l LEFT JOIN tbl_ltr_codes c ON l.term=c.term WHERE YEAR(resultDate)=YEAR(CURRENT_DATE) ORDER BY l.term DESC";
		$dataReader=$conn->createCommand($sql)->query();
		$i=0;
		$sxNames = array("鼠", "牛", "虎", "兔", "龙", "蛇", "马", "羊", "猴", "鸡", "狗", "猪" );
		$bmsx=$conn->createCommand("SELECT bmsx from tbl_site")->queryRow();
		$bmsx=$bmsx['bmsx'];
		$items=array();
		//"虎","牛","鼠","猪","狗","鸡","猴",28,"单","小","单","双","小"
		//"sx0","sx1","sx2","sx3","sx4","sx5","sx6","zh","ds","dx","hsds","zhds","zhdx"
		while(($row=$dataReader->read())!==false) {
			$item=array();
			foreach($row as $key=>$val){
				$item[]=$val;
			}
			$zh=0;
			$tm=intval($row["zm7"]);
			$intCode=array();
		    $sxCode=array();
			for($i=1;$i<=7;$i++){
				if(strlen($row["zm$i"])==2){
					$code=intval($row["zm$i"]);
					$sxI=($bmsx - ($code-1) % 12 + 12) % 12;
					$item[]=$sxNames[$sxI];
					$zh+=$code;
					$intCode["zm$i"]=$code;
		    		$sxCode["zm$i"]=$sxI;
				}else{
					$item[]="";
				}
			}
			$item[]=$zh;
			if($zh==0||$tm==0){
				array_push($item,"--","--","--","--","--","--","--","--","--","--","--","--","--");
			}else{
				$item[]=$row["zm7"]%2==1?"单":"双";
				$item[]=$row["zm7"]>24?"大":"小";
				$item[]=($row["zm7"]%10+floor($row["zm7"]/10))%2==1?"单":"双";
				$item[]=$zh%2==1?"单":"双";
				$item[]=$zh>174?"大":"小";
				
				//,"tmwsdx","tmqs","qsb","zx","zxds","qmds","qmdx","qmhsds"
				$item[]=$tm%10>4?"大":"小";
				$jqCodes=array(11,7,10,6,9,1);
    			if($intCode['zm7']==49){
    				$item[]='-';
    			}else{
					$item[]=in_array($sxCode['zm7'],$jqCodes)?"家禽":"野兽";
    			}
				//qsb
				$sbs=array(0, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 0, 1, 2, 2, 0, 0, 1, 1, 2, 2, 0, 1, 1, 2, 2, 0, 0, 1, 1, 2 );
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
					$item[]='-';
				}else{
					$sb[$sbs[$intCode['zm7']-1]]+=1.5;
					$r=max($sb);
					for($i=0;$i<count($sb);$i++){
						if($r==$sb[$i]){
							$r=$i;
							break;
						}
					}
					$sbName=array('红','蓝','绿');
					$item[]=$sbName[$r];
				}
				
				$zx=array();
				foreach($intCode as $codeV){
					if($codeV!='49'){
						$zx[$codeV%12]=1;
					}
				}
				$zxN=count($zx);

				$item[]="{$zxN}肖";
				$item[]=($zxN%2==1)?"单":"双";
				
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
		
				$item[]="单{$qm[0]}双{$qm[1]}";
				$item[]="大{$qm[2]}小{$qm[3]}";
				$item[]="单{$qm[4]}双{$qm[5]}";
			}
			$items[]=$item;
		}
		echo CJSON::encode(array("count"=>$count[0],"items"=>$items));
		Yii::app()->end(); 
	}
	
	public function getSxIndex(){
		return (Yii::app()->user->site['bmsx'] - a % 12 + 12) % 12;
	}
	
	public function actionTest()
	{
		//$this->render('index');
		$arr=array('user'=>md5('123456'));
		$this->layout=false;
		//header('Content-type: application/json');
		header('Content-type: text/html');
		echo CJSON::encode($arr);
		echo '<br/>'.md5('admin123456').'<br/>';
		//date_default_timezone_set("prc"); 
		//echo strtotime("2011-03-19 18:09")-time();
		echo preg_replace('/[\d\_]/i','','zt_2_hsds');
		echo "<BR />";
		echo floor(13/10)."<BR />";
		$zx=array();
		$zx[2]=1;
		$zx[4]=1;
		echo 'zx'.(count($zx))."<BR />";
		print_r( array_count_values(array('3','2','','','2')));
		echo "<BR />";
		echo (1-"13.50"/100)*"45"+(1-"14.50"/100)*"110";
		echo "<BR />";
		
		/*for($i=0;$i<100;$i++){
			echo session_id().' ';
		}*/
		
		/*$memcache = new Memcache; 
		$memcache->connect('127.0.0.1', 11211);
		$memcache->set('mem_key', 'Hello Memcache!', 0, 180);
		$val = $memcache->get('mem_key');
		echo $val;*/
		
		/*$ltrTypes=array('tm','tmds,tmdx,tmhsds,tmwsdx,tmqs','tmsb','zm','zhds,zhdx','zt','ztds,ztdx,zthsds,ztwsdx,ztqs','ztsb_0,ztsb_1,ztsb_2','lm','tx','bbds,bbdx','mx','sx','ws','sxl','wsl','bz');
		for($i=0;$i<count($ltrTypes);$i++){
			$n=Yii::app()->db->createCommand("update tbl_ltr_type set typeid=$i where btc in ('".str_replace(",","','",$ltrTypes[$i])."') or type in ('".str_replace(",","','",$ltrTypes[$i])."')")->execute();
			echo "update $n , in {$ltrTypes[$i]} <br />";
		}*/
		echo Yii::app()->getRequest()->getBaseUrl(true);
		echo "<BR />";
		echo preg_replace('/http[s]?:\/\//i','',Yii::app()->getRequest()->getHostInfo());
		echo "<br />path:";
		echo Yii::app()->getBasePath();
		Yii::app()->end(); 
		echo "<BR />";
		
			$auth=Yii::app()->authManager;
			
			if(false){
			
			$auth->createOperation('addUser','addUser');
			$auth->createOperation('addAdmin','addAdmin');
			$auth->createOperation('monitor','monitor');
			 
			/*$bizRule='return Yii::app()->user->id==$params["post"]->authID;';
			$task=$auth->createTask('updateOwnPost','update a post by author himself',$bizRule);
			$task->addChild('monitor');*/
			
			$role=$auth->createRole('agent');
			$role->addChild('addUser');
			$role->addChild('monitor');
			
			$role=$auth->createRole('admin');
			$role->addChild('agent');
			$role->addChild('addAdmin');
			 
			$auth->assign('admin',1);
			
			}
			echo Yii::app()->user->checkAccess('addAdmin');
			echo "<BR />";
			echo Yii::app()->user->id;
			
		Yii::app()->end(); 
	}
	
	/**
	 * bet=1
	 * btc	tm
	idx	0
	limit	20
	pan	0
	start	0
	cbNames
	 *  "id", "betTime", "betType", "rate", "betSum", "prorate","prSum", "betUser", "bhUser" 
	 * @return unknown_type
	 */
	public function actionBetItemsJson(){
		//{"count":1,"items":[[89220,"21:24:39","特码A01","42.38",1000.0,0.6,600.0,"hy6",""]],"success":true}
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		$type=$_POST['btc'].'_'.$_POST['idx'];
		$where="ltrTypeid='$type'";
		if(isset($_POST['pan'])&&($_POST['btc']=='tm'||$_POST['btc']=='zm')){
			$pan=$_POST['pan'];
			if($pan>1){
				$type0=$_POST['btc'].'_0_'.$_POST['idx'];
				$type1=$_POST['btc'].'_1_'.$_POST['idx'];
				$where="(ltrTypeid='$type0' or ltrTypeid='$type1')";
			}else{
				$type=$_POST['btc'].'_'.$pan.'_'.$_POST['idx'];
				$where="ltrTypeid='$type'";
			}
		}
		if(isset($_POST['cbNames'])){
			$cbNames=$_POST['cbNames'];
			/*$itemCodes=split(',',$_POST['cbNames']);
				for($itemJ=0;$itemJ<count($itemCodes);$itemJ++){
						$itemCodes[$itemJ]=intval($itemCodes[$itemJ])-1;
				}
			$cbNames=implode(',',$itemCodes);*/
			$where.=" and betCode='$cbNames'";
		}
		$where.=" and termid='{$this->term}'";
		$role=Yii::app()->user->role;
		if(isset($_POST['bet'])&&$_POST['bet']==1){
			$where.=" and betUserId='".Yii::app()->user->id."'";
		}else{
			$where.=" and statUser_$role='".Yii::app()->user->id."' and betUserId<>'".Yii::app()->user->id."'";
		}
		$limit=" limit {$_POST['start']},{$_POST['limit']}";
		$sql="select id,SUBSTRING(betTime,12),betType,rate,betSum,prorate_$role,prorate_$role*betSum,betUserId,bhUserId from tbl_bet where $where";
		//echo $sql;
		$items=$conn->createCommand($sql.$limit)->queryAll(false);
		
		$sql="select count(*) from ".substr($sql,strpos($sql," from")+6);
		$count=$conn->createCommand($sql)->queryRow(false);
		CommonUtil::metaData($items,array('int','','','','float','float','float','',''));
		echo CJSON::encode(array("count"=>$count[0],"items"=>$items,"success"=>true)); 
		Yii::app()->end(); 
	}
	
	/**
	 * enabled	false
	 * @return unknown_type
	 */
	public function actionEnableCountDown(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->user->setState('countDown',$_POST['enabled']=='true');
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}