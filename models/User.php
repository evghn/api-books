<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $age
 * @property int|null $gender
 * @property string $password
 * @property string $token
 */
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender'], 'default', 'value' => null],
            [['name', 'email', 'age', 'password'], 'required'],
            [['age', 'gender'], 'integer'],
            ['email', 'email'],
            [['email'], 'unique'],
            [['name', 'email', 'password', 'token', 'role'], 'string', 'max' => 255],
            ['name', 'match', 'pattern' => "/^[A-Z]{1}[a-zA-Z\s\-]*$/"],
            ['age', 'integer', 'min' => 2, 'max' => 150],
            ['gender', 'integer', 'min' => 1, 'max' => 2],
            ['password', 'match', 'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])[a-zA-Z\d]{4,}$/'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'age' => 'Age',
            'gender' => 'Gender',
            'password' => 'Password',
            'token' => 'Token',
        ];
    }


    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserBookSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBookSettings()
    {
        return $this->hasMany(UserBookSetting::class, ['user_id' => 'id']);
    }


    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    
    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }


    public static function findByUsername($email)
    {
        return self::findOne(['email' => $email]);
    }


    public static function getAdmins()
    {
        return self::find()
            ->select('id')
            ->where(['role' => 'admin'])
            ->column()
            ;
    }

    public function getIsAdmin()
    {
        return $this->role == 'admin';
    }

}
