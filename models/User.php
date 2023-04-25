<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $password
 * @property string $phone_number
 * @property string $address
 * @property string|null $auth_key
 * @property int|null $user_role
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const ROLE_ADMIN = 10; # User type admin
    const ROLE_SELLER = 0; #User type worker

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['first_name', 'last_name', 'username', 'password', 'phone_number', 'address'], 'required'],
            [['user_role', 'status', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name', 'username', 'password', 'phone_number', 'address'], 'string', 'max' => 150],
            [['auth_key'], 'string', 'max' => 250],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'username' => 'Username',
            'password' => 'Password',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'auth_key' => 'Auth Key',
            'user_role' => 'User Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * findAdminByUsername
     *
     * @param mixed $username
     * @return User|null
     */
    public static function findByUsername($username): ?User
    {
        return self::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }


    /**
     * @param $username
     * @return User|null
     */
    public static function findSellerByUsername($username): ?User
    {
        return self::findOne(['username' => $username, 'user_role' => self::ROLE_SELLER, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = UserToken::findOne(['token' => $token]);
        if (!empty($user)) {
            return self::findOne(['id' => $user->user_id, 'status' => self::STATUS_ACTIVE]);
        } else {
            throw new UnauthorizedHttpException("Sizning yuborgan mavjud emas!");
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function fields()
    {
        return [
            'id',
            'first_name',
            'last_name',
            'username',
            'phone_number',
            'address',
            'status' => function () {
                return $this->statusLabel;
            },
            'user_role' => function () {
                return $this->user_role === self::ROLE_ADMIN ? "admin" : "worker";
            },
            'created_at',
            'updated_at'
        ];
    }
    public function getStatusLabel()
    {
        if ($this->status === self::STATUS_ACTIVE) {
            return "active";
        } else if ($this->status === self::STATUS_DELETED) {
            return "deleted";
        }
        return "inactive";
    }
    public function saved()
    {
        $login = new LoginForm();
        $login->username = $this->username;
        $login->password = $this->password;
        $this->setPassword($this->password);
        $this->auth_key = Yii::$app->security->generateRandomString(32);
        if ($this->save()) {
            if ($data = $login->login()) {
                return $data;
            }
        }
        return false;
    }
}
