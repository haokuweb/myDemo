<?php

/**
 * This is the model class for table "tbl_marquee".
 *
 * The followings are the available columns in table 'tbl_marquee':
 * @property integer $id
 * @property integer $grants
 * @property string $message
 * @property integer $showLogon
 * @property integer $showMar
 * @property string $updatedTime
 */
class Marquee extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Marquee the static model class
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
		return 'tbl_marquee';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('grants', 'required'),
			array('grants, showLogon, showMar', 'numerical', 'integerOnly'=>true),
			array('message, updatedTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, grants, message, showLogon, showMar, updatedTime', 'safe', 'on'=>'search'),
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
			'grants' => 'Grants',
			'message' => 'Message',
			'showLogon' => 'Show Logon',
			'showMar' => 'Show Mar',
			'updatedTime' => 'Updated Time',
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
		$criteria->compare('grants',$this->grants);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('showLogon',$this->showLogon);
		$criteria->compare('showMar',$this->showMar);
		$criteria->compare('updatedTime',$this->updatedTime,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}