<?php
/**
 * Created by PhpStorm.
 * User: 123456
 * Date: 2015/5/20
 * Time: 11:07
 */


namespace Seter\Model;


class User extends \Seter\Base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_mcuser}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userlogin', 'userpassword'], 'required'],
            [['states', 'signin_time', 'update_time', 'create_time'], 'integer'],
            [['userlogin', 'userpassword', 'group', 'username', 'usertel', 'weixin', 'weibo'], 'string', 'max' => 32],
            [['QQ'], 'string', 'max' => 16],
            [['ip'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userlogin' => 'Userlogin',
            'userpassword' => 'Userpassword',
            'group' => 'Group',
            'username' => 'Username',
            'usertel' => 'Usertel',
            'QQ' => 'Qq',
            'weixin' => 'Weixin',
            'weibo' => 'Weibo',
            'ip' => 'Ip',
            'states' => 'States',
            'signin_time' => 'Signin Time',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
        ];
    }
}





