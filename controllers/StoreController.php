<?php
namespace app\controllers;

use app\models\Store;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\MethodNotAllowedHttpException;

class StoreController extends BaseController
{
    public $modelClass = Store::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'data'];
        unset($actions['create']);

        return $actions;
    }

    public function data(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Store::find()->where(['user_id' => Yii::$app->user->id])
        ]);
    }
    public function actionCreate()
    {
        if ($this->request->isPost && $data = $this->request->post()){
            $store = new Store();
            return $store->saved($data);
        } else{
            throw new MethodNotAllowedHttpException();
        }
    }
}