<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "store".
 *
 * @property int $id
 * @property string|null $store_name
 * @property int $user_id
 * @property string $created_at
 *
 * @property StoreInfo $storeInfo
 * @property User $user
 */
class Store extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store';
    }


    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false
            ],
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_name'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['store_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'store_name' => 'Store Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    public function fields(): array
    {
        return [
            'id',
            'store_name',
            'store_info' => function () {
                return $this->getStoreInfos();
            },
            'created_at' => function () {
                return date('d-m-Y H:i:s', $this->created_at);
            },
        ];
    }


    public function getStoreInfos()
    {
        return StoreInfo::find()->where(['store_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function saved(array $data)
    {
        $store = new $this;
        $store_info = new StoreInfo();
        $store->store_name = $data['store_name'];
        if ($store->save()) {
            $store_info->store_id = $store->id;
            $store_info->store_type = $data['store_type'];
            $store_info->location = Yii::$app->user->identity->address;
            return $store_info->save() ? true : $store_info->errors;
        }
        return $store->errors;
    }
}
