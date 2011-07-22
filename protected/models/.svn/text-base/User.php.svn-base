<?php

/**
 * This is the model class for table "tbl_user".
 *
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $name
 * @property integer $creditSum
 * @property string $maxProrate
 * @property integer $partner
 * @property integer $bh
 * @property integer $leis
 * @property integer $parentId
 * @property integer $isLeaf
 * @property integer $role
 * @property integer $status
 * @property integer $count2
 * @property string $createdTime
 * @property string $lastLoginTime
 * @property integer $lastLoginIP
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('creditSum, bh, leis, parentId, role, lastLoginIP', 'required'),
			array('creditSum, partner, bh, leis, parentId, isLeaf, role, status, count2, lastLoginIP', 'numerical', 'integerOnly'=>true),
			array('username, password, email, name', 'length', 'max'=>128),
			array('maxProrate', 'length', 'max'=>3),
			array('createdTime, lastLoginTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, email, name, creditSum, maxProrate, partner, bh, leis, parentId, isLeaf, role, status, count2, createdTime, lastLoginTime, lastLoginIP', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'name' => 'Name',
			'creditSum' => 'Credit Sum',
			'maxProrate' => 'Max Prorate',
			'partner' => 'Partner',
			'bh' => 'Bh',
			'leis' => 'Leis',
			'parentId' => 'Parent',
			'isLeaf' => 'Is Leaf',
			'role' => 'Role',
			'status' => 'Status',
			'count2' => 'Count2',
			'createdTime' => 'Created Time',
			'lastLoginTime' => 'Last Login Time',
			'lastLoginIP' => 'Last Login Ip',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('creditSum',$this->creditSum);
		$criteria->compare('maxProrate',$this->maxProrate,true);
		$criteria->compare('partner',$this->partner);
		$criteria->compare('bh',$this->bh);
		$criteria->compare('leis',$this->leis);
		$criteria->compare('parentId',$this->parentId);
		$criteria->compare('isLeaf',$this->isLeaf);
		$criteria->compare('role',$this->role);
		$criteria->compare('status',$this->status);
		$criteria->compare('count2',$this->count2);
		$criteria->compare('createdTime',$this->createdTime,true);
		$criteria->compare('lastLoginTime',$this->lastLoginTime,true);
		$criteria->compare('lastLoginIP',$this->lastLoginIP);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}