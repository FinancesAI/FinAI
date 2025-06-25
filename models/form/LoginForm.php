<?php

namespace app\models\form;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\Log;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $email;
    public $password;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
    	$user = User::find()->where(["email" => $this->email])->one();
        if ($user) {
        	if ($user->validatePassword($this->password)) {
//        		if (!$user->isAdmin() && \Yii::$app->getRequest()->getUserIP() !== \Yii::$app->setting->get("user_login_ip")) {
 //       			Yii::$app->systemlog->create(Log::TYPE_LOGIN_FAIL_IP);
  //      			return false;
    //    		}

      //  		if ($user->id == 12 && \Yii::$app->getRequest()->getUserIP() !== \Yii::$app->setting->get("user_login_ip")) {
        //			Yii::$app->systemlog->create(Log::TYPE_LOGIN_FAIL_IP);
        //			return false;
        //		}



                $isLogged = Yii::$app->user->login($user, 0);

                if ($isLogged) {
                    Yii::$app->systemlog->create(Log::TYPE_LOGIN_SUCCESS);
                    $user->updateAttributes(['last_login_time' => time()]);
                    return $isLogged;
                }

        	}
        }
        Yii::$app->systemlog->create(Log::TYPE_LOGIN_FAIL);

        return false;
    }
}
