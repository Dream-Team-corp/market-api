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
 * @property StoreInfo[] $storeInfos
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_name' => 'Store Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[StoreInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreInfos()
    {
        return $this->hasMany(StoreInfo::class, ['store_id' => 'id']);
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
