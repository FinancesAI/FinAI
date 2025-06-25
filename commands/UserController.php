<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

use app\models\User;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UserController extends Controller
{
    /**
     * This command creates new user
     * @param string $email email.
     * @param string $password password.
     * @param string $fullname fullname.
     * @param string $role role.
     */
    public function actionIndex($email, $password, $fullname, $role, $auth = '')
    {
		$user = new User();
		$user->password = $password;
		$user->email = $email;
		$user->fullname = $fullname;
		$user->role = $role;
		$user->auth = $auth;
		
		if ($user->save()) {
			echo "User saved\r\n";
		} else {
			echo "User not saved\r\n";
			var_dump($user->getErrors());
		}
    }
}
