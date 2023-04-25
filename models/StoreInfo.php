<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_info".
 *
 * @property int $id
 * @property int|null $store_id
 * @property string $location
 * @property string $store_type
 * @property int $created_at
 *
 * @property Store $store
 */
class StoreInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'created_at'], 'integer'],
            [['location', 'store_type', 'created_at'], 'required'],
            [['location', 'store_type'], 'string', 'max' => 255],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'location' => 'Location',
            'store_type' => 'Store Type',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }
}
