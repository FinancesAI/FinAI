<?php /** @noinspection PlainPhpUnregisteredKeyInspection */

/** @noinspection PlainPhpUnregisteredKeyInspection */

/** @noinspection PlainPhpUnregisteredKeyInspection */

/** @noinspection PlainPhpUnregisteredKeyInspection */

namespace app\models;

use Carbon\Carbon;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $fullname
 * @property string $email
 * @property string $password
 * @property string $auth
 * @property integer $role
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $activation_time
 * @property integer $activation_date_time
 * @property integer $last_login_time
 * @property integer $provider_id
 * @property integer $chat_enable
 * @property string $avatar
 *
 * @property Loan[] $loans
 */
class User extends ActiveRecord implements IdentityInterface {

    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVATED = 0;

    const ROLE_ADMIN = 1;
    const ROLE_BANK = 2;

    /**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'user';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[ [ 'email', 'fullname', 'password' ], 'required' ],
			[ [ 'password', 'auth', 'fullname', 'lang', 'access_token' ], 'string' ],
			[ [ 'role', 'status', 'create_time', 'update_time', 'provider_id', 'chat_enable' ], 'integer' ],
			[ [ 'email' ], 'string', 'max' => 100 ],
			[ [ 'email' ], 'unique' ],
            [ ['activationDateTime'], 'string'],
            ['avatar', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
		];
	}

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression(time()),
            ],

            'uploadImageBehavior' => [
                'class' => \mohorev\file\UploadImageBehavior::class,
                'attribute' => 'avatar',
                'scenarios' => ['insert', 'update'],
//                'deleteOriginalFile' => true,
//                'placeholder' => '@app/modules/user/assets/images/userpic.jpg',
                'path' => '@webroot/upload/avatar/{id}',
                'url' => '@web/upload/avatar/{id}',
                'thumbs' => [
                    'thumb' => ['width' => 200, 'quality' => 90],
                    'chat' => ['width' => 60, 'quality' => 90],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/user', 'ID'),
            'fullname' => Yii::t('app/user', 'Fullname'),
            'email' => Yii::t('app/user', 'Email'),
            'password' => Yii::t('app/user', 'Password'),
            'auth' => Yii::t('app/user', 'Auth'),
            'role' => Yii::t('app/user', 'Role'),
            'status' => Yii::t('app/user', 'Status'),
            'create_time' => Yii::t('app/user', 'Create Time'),
            'update_time' => Yii::t('app/user', 'Update Time'),
            'activation_time' => Yii::t('app/user', 'Activation Time'),
            'last_login_time' => Yii::t('app/user', 'Last login'),
            'activationDateTime' => Yii::t('app/user', 'Activation Date Time'),
            'provider_id' => Yii::t('app/user', 'Provider id'),
            'lang' => Yii::t('app/user', 'Language'),
            'chat_enable' => Yii::t('app/user', 'Chat Enable'),
            'avatar' => Yii::t('app/user', 'Upload avatar'),
        ];
    }

    /**
     * @return false|string
     */
    public function getActivationDateTime()
    {
        return $this->activation_time ? date('d.m.Y H:i', $this->activation_time) : '';
    }

    /**
     * @param $date
     * @return void
     */
    public function setActivationDateTime($date): void
    {
        $this->activation_time = $date ? strtotime($date) : null;
    }

	public function generatePassword( $password ) {
		return crypt( $password, Yii::$app->params["salt"] );
	}

	public function validatePassword( $password ) {
		return ( $this->password == crypt( $password, Yii::$app->params["salt"] ) );
	}

	public function isAdmin() {
		return $this->role === self::ROLE_ADMIN;
	}

	public function isBank() {
		return $this->role === self::ROLE_BANK;
	}

	public function getRoles() {
		return [
			"0" => Yii::t( "app/user", "User" ),
			"1" => Yii::t( "app/user", "Admin" ),
			"2" => Yii::t( "app/user", "Bank" ),
		];
	}

    /**
     * @return array
     * @noinspection PlainPhpUnregisteredKeyInspection
     */
    public function getStatuses(): array
    {
        return [
            "0" => Yii::t( "app/user", "Deactivated" ),
            "1" => Yii::t( "app/user", "Active" ),
        ];
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isDeactivated(): bool
    {
        return $this->status === self::STATUS_DEACTIVATED;
    }

	/**
	 * (non-PHPdoc)
	 * @see \yii\db\BaseActiveRecord::beforeSave($insert)
	 */
	public function beforeSave( $insert ) {
		if ( $this->isNewRecord ) {
			$this->password = $this->generatePassword( $this->password );

            if ($this->status == 1 && !$this->activation_time) {
                $this->touch('activation_time');
            }
		} else {
			if ( $this->password !== $this->getOldAttribute( "password" ) ) {
				$this->password = $this->generatePassword( $this->password );
			}

            if ($this->getOldAttribute("status") == 0 && $this->status == 1 && !$this->activation_time) {
                $this->touch('activation_time');
            }

            if ($this->getOldAttribute("status") == 1 && $this->status == 0 ) {
                $this->activation_time = null;
            }
		}

		return parent::beforeSave( $insert );
	}

//	public function delete() {
//		throw new NotSupportedException( "Can't delete user" );
//	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity( $id ) {
		return static::findOne( [ 'id' => $id ] );
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken( $token, $type = null ) {
		return static::findOne( [ 'access_token' => $token ] );
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey() {
		return $this->auth;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey( $authKey ) {
		return $this->getAuthKey() === $authKey;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLoans() {
		return $this->hasMany( Loan::className(), [ 'user_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCalendars() {
		return $this->hasMany( UserCalendar::className(), [ 'user_id' => 'id' ] );
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }

    /**
     * @param int $width
     * @param int $height
     * @param $options
     * @return string
     */
    public function getAvatarUrl(int $width = 200, int $height = 200, $options = []): string
    {
        if (isset($this->avatar)) {
            return $this->getThumbUploadUrl('avatar', 'chat');
        }

        $query = [
            'name' => $this->fullname,
            'background' => 'e0e0e0',
            'color' => '000',
            'bold'=> true,
            'size'=>'512'
        ];

        return 'https://ui-avatars.com/api/?' . http_build_query(array_merge($query, $options));
    }

    /**
     * @param $time
     * @return void
     */
    public function updateOnline($time)
    {
        $this->last_login_time = $time;
        $this->save();
    }

    /**
     * @return bool
     */
    public function getIsOnline(): bool
    {
//        if ($this->isAdmin()) {
//            return false;
//        }

        return isset($this->last_login_time) && time() - $this->last_login_time <= Yii::$app->params['onlineThreshold'];
    }

    /**
     * @return string
     */
    public function getLastTimeOnline(): string
    {
        if (!isset($this->last_login_time)) {
            return Yii::t('app/user', 'never');
        }

        $date = Carbon::createFromTimestamp($this->last_login_time);

        $difference = $date->diffInDays();

        if ($difference == 0 || $difference == 1) {
            return $date->format('H:i');
        } elseif ($difference > 1 && $difference <= 7) {
            return Yii::t('app/user', 'this week');
        } elseif ($difference > 7 && $difference <= 30) {
            return Yii::t('app/user', 'this month');
        }

        return Yii::t('app/user', 'long time ago');
    }
}
