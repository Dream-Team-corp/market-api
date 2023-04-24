<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\MethodNotAllowedHttpException;

class AuthController extends Controller
{
    public $defaultAction = 'login';

    /**
     * @throws MethodNotAllowedHttpException
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post(), '') && $user_data = $model->login()) {
                return $user_data;
            } else {
                return $model->errors;
            }
        } else {
            throw new MethodNotAllowedHttpException("Bu action  faqat POST turdagi so'rovlarni qabul qiladi!");
        }
    }

    public function actionSignup()
    {
        $model = new User();
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
                return $model->saved();
            } else {
                return $model->errors;
            }
        } else {
            throw new MethodNotAllowedHttpException("Bu action  faqat POST turdagi so'rovlarni qabul qiladi!");
        }
    }

}