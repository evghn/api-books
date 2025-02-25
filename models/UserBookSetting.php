<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_book_setting".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $font_family
 * @property int|null $font_size
 * @property string|null $text_color
 * @property string|null $background_color
 *
 * @property User $user
 */
class UserBookSetting extends \yii\db\ActiveRecord
{

    public $settings = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_book_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['font_family', 'font_size', 'text_color', 'background_color'], 'default', 'value' => null],
            // [['user_id'], 'required'],
            [['user_id', 'font_size'], 'integer'],
            [['font_family'], 'string', 'max' => 255],
            [['text_color', 'background_color'], 'string', 'max' => 7],
            [['text_color', 'background_color'], 'match', 'pattern' => '/^\#[A-F\d]{6}$/'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['settings'], 'validateSettings'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'font_family' => 'Font Family',
            'font_size' => 'Font Size',
            'text_color' => 'Text Color',
            'background_color' => 'Background Color',
        ];
    }

    public function validateSettings()
    {
        if (!$this->hasErrors()) {           
            if (!array_filter(Yii::$app->request->post())) {
                $this->addError('settings', 'The settings cannot be empty.');
                return;
            }

            $res = array_filter(array_keys(Yii::$app->request->post()), fn($val) => in_array($val, array_keys($this->attributes)));
            if (!$res) {
                $this->addError('settings', 'The settings cannot be empty.');
                return;
            }
        }
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
