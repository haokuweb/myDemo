<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/default';
	
	public $roles=array('admin'=>0,'dgd'=>1,'gd'=>2,'zdl'=>3,'dl'=>4,'hy'=>5,'cps'=>6);
	
	public $term;
	
	public function filters()
    {
        return array(
            'siteAccess',
        );
    }
    
    /**
     *  •*: 任何用户，包括匿名和验证通过的用户。
	 *	•?: 匿名用户。
	 *	•@: 验证通过的用户。

     * @see core/web/CController#accessRules()
     */
    /*public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('login'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('*'),
                'users'=>array('@'),
            ),
            array('deny',
                'actions'=>array('index'),
                'users'=>array('?'),
            ),
        );
    }*/
    
	public function filterSiteAccess($filterChain)
	{
	    
		$actionId=$this->getAction()->getId();
	    if($actionId!='login'&&$actionId!='captcha'&&$actionId!='test'&&(Yii::app()->user->isGuest||CommonUtil::getUserStatus(Yii::app()->user->signid)!=0)){
	    	if($actionId=='refreshJson'){
	    		$this->layout=false;
				header('Content-type: application/json');
	    		echo "this.location.href='/main/login.do'";
	    		Yii::app()->end();
	    	}
	    	$this->redirect("/main/login.do");
	    }
		date_default_timezone_set("PRC");
		
		$this->term=CommonUtil::getCachedTerm();
	    //$auth=Yii::app()->authManager;
	    
	    //if($auth->getRoles(Yii::app()->user->id))
	    
	    $filterChain->run();
	}
    
	
}