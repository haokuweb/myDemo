<?php

class UserController extends Controller
{
	public function actionIndex()
	{
		//$this->render('index');
		$this->layout=false;
		//header('Content-type: application/json');
		header('Content-type: text/html');
			
		Yii::app()->end(); 
	}
	
	public function actionBhcomboJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$bhRec=Yii::app()->db->createCommand("select id,concat(id,'(',name,')') from tbl_bhuser where status=0")->queryAll(false);
		//echo '[["az999","az999(百纳)"],["bx999","bx999(宇泰)"],["kk","kk(tes)"],["bx12","bx12(888)"]]';
		echo CJSON::encode($bhRec);
		Yii::app()->end(); 
	}
	
	/**
	*idxs	0,1
		rebate0	13.50,3.50
		rebate1	14.5,4.5
		rebate2	15.50,5.50
		sub	true
		user	xcf1
	**/
	public function actionModifyRebate(){
		$this->layout=false;
		header('Content-type: application/json');
		$idxs=split(',',$_POST['idxs']);
		$rebate0=split(',',$_POST['rebate0']);
		$rebate1=split(',',$_POST['rebate1']);
		$rebate2=split(',',$_POST['rebate2']);
		for($i=0;$i<count($idxs);$i++){
			Yii::app()->db->createCommand("update tbl_default_rebate set rebate0={$rebate0[$i]},rebate1={$rebate1[$i]},rebate0={$rebate2[$i]} where idx={$idxs[$i]}")->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * dcs	100200
		dzs	50000
		idxs	21
		sub	false
		user	xcf1
	 * @return unknown_type
	 */
	public function actionModifyLimit(){
		$this->layout=false;
		header('Content-type: application/json');
		$idxs=split(',',$_POST['idxs']);
		$termLimit=split(',',$_POST['dcs']);
		$betLimit=split(',',$_POST['dzs']);
		$sub=$_POST['sub'];
		$userid=isset($_POST['user'])?$_POST['user']:Yii::app()->user->id;
		$whereUser='';
		if($sub=='true'){
			$userRec=Yii::app()->db->createCommand("select id,role from tbl_user where id=:parentId")->queryRow(true,array(":parentId"=>$userid));
			$role=$userRec['role'];
			$subUserIds=Yii::app()->db->createCommand("select id from tbl_user where parent_$role=:parentId")->queryColumn(array(":parentId"=>$userid));
			if($subUserIds==false){
				$subUserIds=array();
			}
			$subUserIds[]=$userid;
			$whereUser=" userid in ('".join("','",$subUserIds)."')";
		}else{
			$whereUser=" userid='$userid'";
		}
		for($i=0;$i<count($idxs);$i++){
			Yii::app()->db->createCommand("update tbl_sumlimit set termLimit={$termLimit[$i]},betLimit={$betLimit[$i]} where idx={$idxs[$i]} and $whereUser")->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionModifyBhStatus(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->db->createCommand("update tbl_bhuser set status={$_POST['status']} where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionModifySlaveStatus(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->db->createCommand("update tbl_subsuser set status={$_POST['status']} where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionListbhJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		//echo '{"data":[{"id":"az999","name":"百纳","status":0},{"id":"bx999","name":"宇泰","status":0},{"id":"kk","name":"tes","status":0},{"id":"bx12","name":"888","status":0}]}';
		$bhRec=Yii::app()->db->createCommand("select id,name,status from tbl_bhuser")->queryAll();
		//CommonUtil::metaData($bhRec,array('','','int'));
		echo CJSON::encode(array("data"=>$bhRec));
		Yii::app()->end(); 
	}
	
	//[ "idx", "rebate0", "rebate1", "rebate2" ]
	//Ltr.rebateTypeNames = [ "特码A", "特码B", "正码A", "正码B", "正特", "两面", "色波", "连码","特肖", "生肖", "尾数", "半波", "六肖", "连串", "五不中" ];
	public function actionRebateJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$rebates=Yii::app()->db->createCommand("select idx,rebate0,rebate1,rebate2 from tbl_default_rebate order by idx")->queryAll(false);
		echo  CJSON::encode(array("data"=>$rebates,"success"=>true));
		Yii::app()->end(); 
	}
	
	public function actionSumlimitJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$userid=isset($_POST['user'])?$_POST['user']:Yii::app()->user->id;
		//echo '{"data":[[0,220000,10000],[1,30000,10000],[2,10000,3000],[3,100000,50000],[4,100000,50000],[5,20000,2000],[6,10000,1000],[7,10000,2000],[8,10000,2000],[9,10000,2000],[10,30000,10000],[11,100000,50000],[12,100000,30000],[13,50000,10000],[14,100000,50000],[15,30000,5000],[16,100000,50000]],"success":true}';
		$sql="select idx,termLimit,betLimit from tbl_sumlimit where userid='$userid' order by idx";
        $records=Yii::app()->db->createCommand($sql)->queryAll(false);
        CommonUtil::metaData($records,array('int','int','int'));
		$result=array("data"=>$records,"success"=>true);
		echo CJSON::encode($result);
		Yii::app()->end(); 
	}
	
	/**
	*  role	1
		status	0
	 or:	
		parent	2233c or other
		role	4 代理  
		status	0
		
		[ "id", "name", "role", creditSum, "parentProrate", "maxProrate", "parentId",
								"parentName", "count2"股东数, "count3"总代数, "count4",代理数
								"count5"会员数, "partner", "bh", "leis", "status",
								"parentLeis", "parentMaxProrate",
								"maxChildProrate" ]
	**/
	public function actionUsersJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		$role=intval($_POST['role']);
		$parent=isset($_POST['parent'])?$_POST['parent']:Yii::app()->user->id;
		$parentRole=Yii::app()->user->role;
		if(isset($_POST['parent'])){
			$parentRole=$conn->createCommand("select role,id from tbl_user where id=:id")->queryRow(true,array(":id"=>$_POST['parent']));
			$parentRole=$parentRole['role'];
		}
		$parentRole=intval($parentRole);
		$whereStatus="";
		if($_POST['status']!='-1'){
			$whereStatus=" AND t.`status`={$_POST['status']}";
		}
		
		$sql="SELECT t.id,t.username,t.role,t.creditSum,t.parentProrate,t.maxProrate,t.parentId,p.username,".
			($role==5?"0 AS count2,0 AS count3,0 AS count4,0 AS count5,":"(SELECT COUNT(*) FROM tbl_user c2 WHERE c2.parent_$role=t.id AND c2.role=2) AS count2, 
			(SELECT COUNT(*) FROM tbl_user c3 WHERE c3.parent_$role=t.id AND c3.role=3) AS count3, 
			(SELECT COUNT(*) FROM tbl_user c4 WHERE c4.parent_$role=t.id AND c4.role=4) AS count4, 
			(SELECT COUNT(*) FROM tbl_user c5 WHERE c5.parent_$role=t.id AND c5.role=5) AS count5,") 
			."t.partner,t.bh,t.leis,t.`status`,
			p.leis AS parentLeis,p.maxProrate AS parentMaxProrate,
			(SELECT MAX(c.maxProrate) FROM tbl_user c WHERE c.parentId=t.id) AS maxChildProrate
			FROM tbl_user t LEFT JOIN tbl_user p ON p.id=t.parentId
			WHERE t.role=:role $whereStatus AND t.parent_$parentRole='$parent' order by t.id";
		
		$users=$conn->createCommand($sql)->queryAll(false,array(":role"=>$role));
		$this->metaData($users,array('','','int','int','float','float','','','int','int','int','int','bool','bool','int','int','int','float','float'));
		echo CJSON::encode(array("parentRole"=>$parentRole,"users"=>$users,"role"=>$role,"parent"=>$parent,"success"=>true));
		//echo '{"parentRole":0,"users":[["55664","cf",1,20000,0.0,0.9,"qq133","admin",0,0,0,0,true,true,3,0,7,1.0,0.0],["xcf888","新财富",1,50000,0.0,0.9,"qq133","admin",0,0,0,0,false,true,7,0,7,1.0,0.0]],"role":1,"parent":"qq133","success":true}';
		//{"parentRole":"0","users":[["xcf2","xcf2\u80a1\u4e1c","2","9000","0.20","0.75","xcf1","xcf1\u5927\u80a1\u4e1c","0","1","1","2","0","2","7","0","7","0.95","0.55"]],"role":"2","parent":"qq133","success":true}
		Yii::app()->end(); 
	}
	
	/**
	 * post: parent	qq133 , role	0
	 parent	qq133 , role	2
	 parent xcf4,   role	4
	 * fields : [ "id", "user", "name","role", "remainSum","maxProrate", "bh", "leis" ]
	 * @return unknown_type
	 */
	public function actionCombousersJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		
		$parents=$_POST['parent'];
		//$parentCmd=$conn->createCommand("select id,concat(id,'(',username,')') as userdesc,username,role,creditSum-usedSum as remainSum,maxProrate,bh,leis from tbl_user where role=:role and parentId in (:parents)");
		$sql="select id,concat(id,'(',username,')') as userdesc,username,role,remainSum,maxProrate,bh,leis from tbl_user where ";
		/*$i=Yii::app()->user->role;
		for(;$i<$_POST['role'];$i++){
			//var_dump(array(":role"=>$i+1,":parents"=>"'".implode("','",$ )."'"));
			//$parents=$parentCmd->queryColumn(array(":role"=>$i+1,":parents"=>"'".implode("','",$parents)."'"));
			$parents=$conn->createCommand($sql."role=".($i+1)." and parentId in ('".implode("','",$parents)."')")->queryColumn();
			//var_dump($parents);
		}
		$parents=$conn->createCommand($sql."role=".($i+1)." and parentId in ('".implode("','",$parents)."')")->queryAll(false);
		*/
		$parentRole=$conn->createCommand("select role,id from tbl_user where id=:id")->queryRow(true,array(":id"=>$parents));
		$parentRole=$parentRole['role'];

		$parents=$conn->createCommand($sql."role=".$_POST['role']." and (parent_$parentRole='$parents' or id='$parents')")->queryAll(false);
		$this->metaData($parents,array('','','','int','int','float','bool','int'));
		echo CJSON::encode(array("users"=>array_merge(array(array("","---请选择---",-1,0,0,false,0)),$parents),"success"=>true));
		//echo '{"users":[["","---请选择---",-1,0,0,false,0],["2233c","2233c(cf)","cf",3,20000,0.8,true,7]],"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 *    bh	1
		creditSum	3000
		leis	1
		leis	2
		leis	4
		maxProrate	0.8
		name	ttt
		parent	2233c
		parentProrate	0
		partner	0
		password	ttt
		userId	ttt
		
		-------
		
		creditSum	120
		leis	2
		name	131
		parent	xcf4
		parentProrate	0.35
		password	11
		userId	ddd	
	 * @return unknown_type
	 */
	public function actionAdd(){
		$this->layout=false;
		header('Content-type: application/json');
		//echo '{"user":["c1","c1",2,"1000","0.05","0.90","xcf1","xcf1\u5927\u80a1\u4e1c","0","0","0","0","0","1","7","0","7","0.95",null],"success":true}';
		//Yii::app()->end(); 
		$conn=Yii::app()->db;
		$success=false;
		$result=array();
		if(!isset($_POST['userId'])||$_POST['userId']==''){
			$result['msg']='用户帐号为空';
		}elseif(!isset($_POST['name'])||$_POST['name']==''){
			$result['msg']='用户名为空';
		}elseif(!isset($_POST['password'])||$_POST['password']==''){
			$result['msg']='用户密码为空';
		}elseif(!isset($_POST['creditSum'])||$_POST['creditSum']==''||$_POST['creditSum']==0){
			$result['msg']='信用额度为空';
		}elseif(!isset($_POST['leis'])||$_POST['leis']==''){
			$result['msg']='请选择盘权';
		}else{
			$id=$_POST['userId'];
			$username=$_POST['name'];
			$password=md5($_POST['password']);
			$creditSum=$_POST['creditSum'];
			$parentId=$_POST['parent'];
			$parentProrate=isset($_POST['parentProrate'])?$_POST['parentProrate']:0;
			$maxProrate=isset($_POST['maxProrate'])?$_POST['maxProrate']:0;
			$leis=is_array($_POST['leis'])?array_sum($_POST['leis']):$_POST['leis'];
			$lei=is_array($_POST['leis'])?0:intval($_POST['leis']/2);
			$partner=isset($_POST['partner'])?$_POST['partner']:0;
			$bh=isset($_POST['bh'])?$_POST['bh']:0;
			
			$parentRow=$conn->createCommand("select * from tbl_user where id=:id")->queryRow(true,array(":id"=>$parentId));
			$parentRow["parent_".$parentRow['role']]=$parentId;
			$sql="insert into tbl_user(id,password,username,maxProrate,parentProrate,creditSum,remainSum,lei,leis,parentId,parent_0,parent_1,parent_2,parent_3,parent_4,partner,bh,createdTime,role,isLeaf) values (:id,:password,:username,:maxProrate,:parentProrate,:creditSum,:remainSum,:lei,:leis,:parentId,:parent_0,:parent_1,:parent_2,:parent_3,:parent_4,:partner,:bh,sysdate(),:role,:isLeaf)";
			if(intval($parentRow['remainSum'])>=intval($creditSum)){
				try{
					$userRec=Yii::app()->db->createCommand("select count(*) from tbl_subsuser where id='$id'")->queryRow(false);
					if($userRec[0]==0){
						$command=Yii::app()->db->createCommand($sql);
						$row=$parentRow['role']+1;
						$isLeaf=$row==5?1:0;
						$command->bindParam(":id",$id,PDO::PARAM_STR);
						$command->bindParam(":password",$password,PDO::PARAM_STR);
						$command->bindParam(":username",$username,PDO::PARAM_STR);
						$command->bindParam(":maxProrate",$maxProrate,PDO::PARAM_STR);
						$command->bindParam(":parentProrate",$parentProrate,PDO::PARAM_STR);
						$command->bindParam(":creditSum",$creditSum,PDO::PARAM_INT);
						$command->bindParam(":remainSum",$creditSum,PDO::PARAM_INT);
						$command->bindParam(":lei",$lei,PDO::PARAM_INT);
						$command->bindParam(":leis",$leis,PDO::PARAM_INT);
						$command->bindParam(":parentId",$parentId,PDO::PARAM_STR);
						$command->bindParam(":parent_0",$parentRow["parent_0"],PDO::PARAM_STR);
						$command->bindParam(":parent_1",$parentRow["parent_1"],PDO::PARAM_STR);
						$command->bindParam(":parent_2",$parentRow["parent_2"],PDO::PARAM_STR);
						$command->bindParam(":parent_3",$parentRow["parent_3"],PDO::PARAM_STR);
						$command->bindParam(":parent_4",$parentRow["parent_4"],PDO::PARAM_STR);
						$command->bindParam(":partner",$partner,PDO::PARAM_STR);
						$command->bindParam(":bh",$bh,PDO::PARAM_INT);
						$command->bindParam(":role",$row,PDO::PARAM_INT);
						$command->bindParam("isLeaf",$isLeaf,PDO::PARAM_INT);
						$command->execute();
						Yii::app()->db->createCommand("update tbl_user set remainSum=remainSum-$creditSum where id=:id")->execute(array(":id"=>$parentId));
						Yii::app()->db->createCommand("insert into tbl_autobh(idx,auto,sumLimit,userid) select idx,auto,sumLimit,'$id' from tbl_autobh where userid='$parentId'")->execute();
						Yii::app()->db->createCommand("INSERT INTO tbl_sumlimit(userid,idx,termLimit,betLimit) SELECT '$id',idx,termLimit,betLimit FROM tbl_sumlimit s WHERE s.userid='$parentId'")->execute();
						$success=true;
						$result['user']=$conn->createCommand("SELECT t.id,t.username,t.role,t.creditSum,t.parentProrate,t.maxProrate,t.parentId,p.username,0 AS count2,0 AS count3,0 AS count4,0 AS count5,t.partner,t.bh,t.leis,t.`status`,
					p.leis AS parentLeis,p.maxProrate AS parentMaxProrate,
					(SELECT MAX(c.maxProrate) FROM tbl_user c WHERE c.parentId=t.id) AS maxChildProrate
					FROM tbl_user t LEFT JOIN tbl_user p ON p.id=t.parentId
					WHERE t.id='$id'")->queryRow(false);
						$result['user'][2]=intval($result['user'][2]);
						$result['user'][12]=$result['user'][12]=='1'?true:false;
						$result['user'][13]=$result['user'][13]=='1'?true:false;
					}else{
						$result['msg']='用户名已被子帐号或操盘手使用';
					}
				}catch (Exception $e) {
					$result['msg']='用户已存在或输入的用户信息不正确';
					$result['detailMsg']=$e->getMessage();
				}
			}else{
				$result['msg']='信用额度不够';
			}
		}
		$result['success']=$success;
		echo CJSON::encode($result);
		//{"user":{"id":"eee","username":"xcf1\u5927\u80a1\u4e1c","role":"2","creditSum":"10","parentProrate":"0.00","maxProrate":"0.95","parentId":"xcf1","count2":"0","count3":"0","count4":"0","count5":"0","partner":"0","bh":"1","leis":"7","status":"0","parentLeis":"7","parentMaxProrate":"0.95","maxChildProrate":null}}
		//echo '{"user":["ttt","ttt",4,3000,0.0,0.8,"2233c","cf",0,0,0,0,false,true,7,0,7,0.8,0.0],"success":false,"msg":"t"}';
		Yii::app()->end(); 
	}
	
	/**
	 * {"auto":true,"idx":0,"sumLimit":10}
	 * @return unknown_type
	 */
	public function actionAutobhJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$userid=isset($_POST['user'])?$_POST['user']:Yii::app()->user->id;
		$sql="select auto,idx,sumLimit from tbl_autobh where userid='$userid' order by idx";
        $records=Yii::app()->db->createCommand($sql)->queryAll();
        CommonUtil::metaData($records,array('auto'=>'bool','idx'=>'int','sumLimit'=>'int'));
		$result=array("data"=>$records,"success"=>true);
		echo CJSON::encode($result);
		Yii::app()->end();
	}
	
	/**
	 * user	az999
	 * @return unknown_type
	 */
	public function actionRebatebhJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$rebates=Yii::app()->db->createCommand("select idx,rebate0 as rebate from tbl_default_rebate_bh where userid='{$_POST['user']}' order by idx")->queryAll();
		CommonUtil::metaData($rebates,array("idx"=>'int',"rebate"=>'float'));
		echo  CJSON::encode(array("data"=>$rebates,"success"=>true));
		Yii::app()->end(); 
	}
	
	/**
	 * idxs	2,1,0
		rebates	1,11,1
		user	bx999
	 * @return unknown_type
	 */
	public function actionModifyBhRebate(){
		$this->layout=false;
		header('Content-type: application/json');
		$idxs=split(',',$_POST['idxs']);
		$rebates=split(',',$_POST['rebates']);
		for($i=0;$i<count($idxs);$i++){
			//echo "update tbl_default_rebate_bh set rebate0={$rebates[$i]} where idx={$idxs[$i]} and userid='{$_POST['user']}'";
			Yii::app()->db->createCommand("update tbl_default_rebate_bh set rebate0={$rebates[$i]} where idx={$idxs[$i]} and userid='{$_POST['user']}'")->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * 
	 * @return fields : [ "id", "name", "status" ]
	 */
	public function actionSubsJson()
	{
		$this->layout=false;
		header('Content-type: application/json');
		$subsRec=Yii::app()->db->createCommand("select id,name,status from tbl_subsuser where cp={$_POST['cp']} and userid='".Yii::app()->user->id."'")->queryAll(false);
		//echo '{"data":[["bx","222",0],["cf","cf",0],["q8","9",0],["hk6666","hk",0],["xcf","新财富",0],["k9","9",0],["caopan","caopan",0],["vv","tea",0],["11","1",0],["bx5","w",0]],"success":true}';
		echo CJSON::encode(array("data"=>$subsRec));
		Yii::app()->end(); 
	}
	
	/**
	 * public cp	1
		name	nnn
		password	ddd
		userId	ttt
	 * @return unknown_type
	 */
	public function actionAddSub(){
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		$success=false;
		$result=array();
		if(!isset($_POST['userId'])||$_POST['userId']==''){
			$result['msg']='用户帐号为空';
		}elseif(!isset($_POST['name'])||$_POST['name']==''){
			$result['msg']='用户名为空';
		}elseif(!isset($_POST['password'])||$_POST['password']==''){
			$result['msg']='用户密码为空';
		}else{
			$id=$_POST['userId'];
			$username=$_POST['name'];
			$password=md5($_POST['password']);
			$userid=Yii::app()->user->id;
			
			$sql="insert into tbl_subsuser(id,password,name,cp,userid) values (:id,:password,:username,:cp,:userid)";
			$userRec=Yii::app()->db->createCommand("select count(*) from tbl_user where id='$id'")->queryRow(false);
			if($userRec[0]==0){
				try{
					$command=Yii::app()->db->createCommand($sql);
					$command->bindParam(":id",$id,PDO::PARAM_STR);
					$command->bindParam(":password",$password,PDO::PARAM_STR);
					$command->bindParam(":username",$username,PDO::PARAM_STR);
					$command->bindParam(":cp",$_POST['cp'],PDO::PARAM_INT);
					$command->bindParam(":userid",$userid,PDO::PARAM_STR);
					$command->execute();
					$success=true;
				}catch (Exception $e) {
					$result['msg']='用户名已存在或输入的用户信息不正确';
					$result['detailMsg']=$e->getMessage();
				}
			}else{
				$result['msg']='用户名已存在';
			}
		}
		$result['success']=$success;
		echo CJSON::encode($result);
		Yii::app()->end(); 
	}
	
	public function actionAddBh(){
		$this->layout=false;
		header('Content-type: application/json');
		$conn=Yii::app()->db;
		$success=false;
		$result=array();
		if(!isset($_POST['userId'])||$_POST['userId']==''){
			$result['msg']='用户帐号为空';
		}elseif(!isset($_POST['name'])||$_POST['name']==''){
			$result['msg']='用户名为空';
		}else{
			$id=$_POST['userId'];
			$username=$_POST['name'];
			
			$sql="insert into tbl_bhuser(id,name) values (:id,:username)";
			if(true){
				try{
					$command=Yii::app()->db->createCommand($sql);
					$command->bindParam(":id",$id,PDO::PARAM_STR);
					$command->bindParam(":username",$username,PDO::PARAM_STR);
					$command->execute();
					
					Yii::app()->db->createCommand("insert into tbl_default_rebate_bh select idx,rebate0,rebate1,rebate2,btcs,'$id' from tbl_default_rebate_bh r where r.userid='0'")->execute();
					$success=true;
				}catch (Exception $e) {
					$result['msg']='用户已存在或输入的用户信息不正确';
					$result['detailMsg']=$e->getMessage();
				}
			}else{
				$result['msg']='';
			}
		}
		$result['success']=$success;
		echo CJSON::encode($result);
		Yii::app()->end(); 
	}
	
	/**
	 * userId	xcf1
		creditSum	10002 
	 * @return unknown_type
	 */
	public function actionModifyName(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->db->createCommand("update tbl_user set username='{$_POST['name']}' where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionModifyCreditSum(){
		$this->layout=false;
		header('Content-type: application/json');
		$creditSum=$_POST['creditSum'];
		$userRec=Yii::app()->db->createCommand("select parentId,creditSum,remainSum from tbl_user where id='{$_POST['userId']}'")->queryRow();
		$remainSum=Yii::app()->db->createCommand("select remainSum from tbl_user where id='{$userRec['parentId']}'")->queryRow();
		$remainSum=$remainSum['remainSum']+$userRec['creditSum'];
		if($creditSum>$remainSum){
			echo '{"msg":"超出上级可用信用额度","success":false}';
		}else{
			$remainSum=$userRec['remainSum']+$_POST['creditSum']-$userRec['creditSum'];
			Yii::app()->db->createCommand("update tbl_user set remainSum=$remainSum,creditSum={$_POST['creditSum']} where id='{$_POST['userId']}'")->execute();
			$remainSum=$remainSum['remainSum']+$_POST['creditSum']-$userRec['creditSum'];
			Yii::app()->db->createCommand("update tbl_user set remainSum=$remainSum where id='{$userRec['parentId']}'")->execute();
			echo '{"success":true}';
		}
		Yii::app()->end(); 
	}
	
	/**
	 * confirmPassword	123
		oldPassword	123
		password	123
	 * @return unknown_type
	 */
	public function actionModifySelfPassword(){
		$this->layout=false;
		header('Content-type: application/json');
		$password=$_POST['password'];
		$oldPassword=$_POST['oldPassword'];
		$level=Yii::app()->user->level;
		$userTbl=$level>0?'tbl_subsuser':'tbl_user';
		$userid=isset($_POST['userId'])?$_POST['userId']:Yii::app()->user->signid;
		$oldPasswordRec=Yii::app()->db->createCommand("select password from $userTbl where id='{$userid}'")->queryRow();
		if(md5($_POST['oldPassword'])==$oldPasswordRec['password']){
			$password=md5($password);
			Yii::app()->db->createCommand("update $userTbl set password='{$password}' where id='{$userid}'")->execute();
			echo '{"success":true,"msg":""}';
		}else{
			echo '{"success":false,"msg":"原密码不对"}';
		}
		Yii::app()->end(); 
	}
	
	public function actionModifyPassword(){
		$this->layout=false;
		header('Content-type: application/json');
		$password=md5($_POST['password']);
		Yii::app()->db->createCommand("update tbl_user set password='{$password}' where id='{$_POST['userId']}'")->execute();
		Yii::app()->db->createCommand("update tbl_subsuser set password='{$password}' where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionModifyStatus(){
		$this->layout=false;
		header('Content-type: application/json');
		$status=intval($_POST['status']);
		$conn=Yii::app()->db;
		if($status==2){
			$userRec=$conn->createCommand("select creditSum,parentId,role from tbl_user u where u.id='{$_POST['userId']}'")->queryRow();
			$conn->createCommand("update tbl_user p set remainSum=remainSum+{$userRec['creditSum']} where p.id='{$userRec['parentId']}'");
			$parentRoleWhere='';
			if($userRec['role']<5){
				$parentRoleWhere=" or parent_{$userRec['role']}='{$_POST['userId']}'";
			}
			$conn->createCommand("update tbl_user set status={$status} where id='{$_POST['userId']}'{$parentRoleWhere}")->execute();
			$conn->createCommand("update tbl_subsuser set status={$status} where userid='{$_POST['userId']}'")->execute();
			
			$changedUsers=$conn->createCommand("select id,status from tbl_user  where id='{$_POST['userId']}'{$parentRoleWhere}")->queryAll();
			foreach($changedUsers as $changedUser){
				Yii::app()->cache->set("u_status_{$changedUser['id']}",$status);
			}
			
			$changedUsers=$conn->createCommand("select id,status from tbl_subsuser  where userid='{$_POST['userId']}'")->queryAll();
			foreach($changedUsers as $changedUser){
				Yii::app()->cache->set("u_status_{$changedUser['id']}",$status);
			}
			
		}else{
			$conn->createCommand("update tbl_user set status={$status} where id='{$_POST['userId']}'")->execute();
			Yii::app()->cache->set("u_status_{$_POST['userId']}",$status);
		}
		Yii::app()->cache->set("u_status_{$_POST['userId']}",$status);
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	*partner	1
	 userId	xcf1
	**/
	public function actionModifyPartner(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->db->createCommand("update tbl_user set partner={$_POST['partner']} where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	*leis[]	1
	leis[]	4
	userId	xcf1
	*/
	public function actionModifyLeis(){
		$this->layout=false;
		header('Content-type: application/json');
		$leis=is_array($_POST['leis'])?array_sum($_POST['leis']):$_POST['leis'];
		$lei=is_array($_POST['leis'])?0:intval($_POST['leis']/2);
		Yii::app()->db->createCommand("update tbl_user set leis=$leis,lei=$lei where id='{$_POST['userId']}'")->execute();
		echo '{"success":true,"leis":'.$leis.'}';
		Yii::app()->end(); 
	}
	
	public function actionModifyBh(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->db->createCommand("update tbl_user set bh={$_POST['bh']} where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionModifyMaxProrate(){
		$this->layout=false;
		header('Content-type: application/json');
		$status=CommonUtil::getCachedTermStatus();
		if($status['status']!=2){
			echo '{"success":false,"msg":"还未结账，不能修改"}';
			Yii::app()->end(); 
		}
		Yii::app()->db->createCommand("update tbl_user set maxProrate={$_POST['maxProrate']} where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionModifyParentProrate(){
		$this->layout=false;
		header('Content-type: application/json');
		Yii::app()->db->createCommand("update tbl_user set parentProrate={$_POST['parentProrate']} where id='{$_POST['userId']}'")->execute();
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	/**
	 * autos	true
		idxs	0
		sums	100
	 * @return unknown_type
	 */
	public function actionModifyAbh(){
		$this->layout=false;
		header('Content-type: application/json');
		$idxs=split(',',$_POST['idxs']);
		$autos=split(',',$_POST['autos']);
		$sums=split(',',$_POST['sums']);
		$userid=isset($_POST['user'])?$_POST['user']:Yii::app()->user->id;
		for($i=0;$i<count($idxs);$i++){
			Yii::app()->db->createCommand("update tbl_autobh set sumLimit={$sums[$i]},auto=".($autos[$i]=='true'?1:0)." where userid='".$userid."' and idx={$idxs[$i]}")->execute();
		}
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function actionRemove(){
		$this->layout=false;
		header('Content-type: application/json');
		$userRec=Yii::app()->db->createCommand("SELECT id,role FROM  tbl_user  WHERE STATUS=2")->queryAll();
		$delUserCmd=Yii::app()->db->createCommand("Delete FROM  tbl_user  WHERE id=:id");
		$delUserAutoBhCmd=Yii::app()->db->createCommand("Delete FROM  tbl_autobh  WHERE userid=:id");
		$delUserSumLimitCmd=Yii::app()->db->createCommand("Delete FROM  tbl_sumlimit  WHERE userid=:id");
		foreach($userRec as $user){
			$whereUser="betUserId='{$user['id']}'";
			if($user['role']<5){
				$whereUser="(betUserId='{$user['id']}' or statUser_{$user['role']}='{$user['id']}')";
			}
			$whereUser.=" and termid='{$this->term}'";
			$betRec=Yii::app()->db->createCommand("SELECT count(*) from tbl_bet where $whereUser")->queryRow(false);
			if($betRec[0]==0){
				$delUserCmd->execute(array(":id"=>$user['id']));
				$delUserAutoBhCmd->execute(array(":id"=>$user['id']));
				$delUserSumLimitCmd->execute(array(":id"=>$user['id']));
			}
		}
		
		echo '{"success":true}';
		Yii::app()->end(); 
	}
	
	public function metaData(&$datas,array $meta,$multi=true){
	
		/*foreach($parents as $keys=>$parent){
			foreach($parent as $key=>$parentRow){
				if(is_numeric($parentRow)){
					if(strpos($parentRow,'.')===false){$parents[$keys][$key]=intval($parentRow);}
					else{$parents[$keys][$key]=floatval($parentRow);}
				}
			}
		}*/
		if($multi){
			$count=count($datas);
			for($i=0;$i<$count;$i++){
				$datas[$i]=$this->evalMetaData($datas[$i],$meta);
			}
			return $datas;
		}else{
			return $this->evalMetaData($datas,$meta);
		}
	}
	
	public function evalMetaData(array $datas,array $meta){
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
	

}
