<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class Pager extends Model
{    
    public $count;
    public $page;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['page', 'count'], 'required'],
            [['page', 'count'], 'integer', 'min' => 1],
        ];
    }

    
}
