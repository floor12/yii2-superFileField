<?php

namespace floor12\superfilefield;

use yii\rest\ActiveController;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\BadRequestHttpException;
use Yii;
use yii\web\Response;

class SuperfilefieldController extends ActiveController
{
    public $modelClass = 'floor12\superfilefield\File';


    public function behaviors()
    {
        return [

//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                    'create' => ['post'],
//                    'crop' => ['post'],
//                ],
//            ],
            'format' => [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/xml' => Response::FORMAT_XML,
                    'application/json' => Response::FORMAT_JSON,
                ],
                'languages' => [
                    'en',
                    'ru',
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($class, $field, $object_id)
    {
        $classname = $class;
        $object = $classname::findOne($object_id);
        return $object->superFiles[$field];
    }

    public function actionDelete()
    {
        $id = \Yii::$app->request->getBodyParam('id');
        $file = File::findOne($id);
        if (!$file)
            throw new NotFoundHttpException('This file not found.');
        if (!$file->delete())
            throw new BadRequestHttpException('Cant delete this file');


    }

    public function actionCrop()
    {
        $id = (int)\Yii::$app->request->getBodyParam('id');
        $width = (int)\Yii::$app->request->getBodyParam('width');
        $height = (int)\Yii::$app->request->getBodyParam('height');
        $top = (int)\Yii::$app->request->getBodyParam('top');
        $left = (int)\Yii::$app->request->getBodyParam('left');

        if (!$id || !$height || !$width)
            throw new BadRequestHttpException;

        $file = File::findOne($id);
        if (!$file)
            throw new NotFoundHttpException('This file is not found.');

        if ($file->type != File::TYPE_IMAGE)
            throw new BadRequestHttpException('This file is not an image.');

        return ['filename' => $file->crop($width, $height, $top, $left)];
    }

    public
    function actionCreate()
    {
        $ret = [];
        $files = UploadedFile::getInstancesByName('file');
        $className = Yii::$app->request->post('class');
        if ((sizeof($files) > 0) && ($className)) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            foreach ($files as $file) {
                $ret[] = $model = File::createFromInstance($file, $className, \Yii::$app->request->post('field'));
            }
        } else {
            throw new BadRequestHttpException();
        }
        if (sizeof($ret) == 1)
            return $ret[0];
        return $ret;
    }

    public function actionUpdate()
    {
        $id = \Yii::$app->request->getBodyParam('id');
        $title = \Yii::$app->request->getBodyParam('title');

        if (!$id || !$title)
            throw new BadRequestHttpException;
        $file = File::findOne($id);
        if (!$file)
            throw new NotFoundHttpException('This file not found.');
        $file->title = $title;
        if (!$file->save())
            throw new BadRequestHttpException('Cant update file');
    }

    public function actionRotate()
    {
        $id = \Yii::$app->request->getBodyParam('id');
        $direction = \Yii::$app->request->getBodyParam('direction');

        if (!$id || !$direction)
            throw new BadRequestHttpException;
        $file = File::findOne($id);
        if (!$file)
            throw new NotFoundHttpException('This file not found.');
        if ($file->type != File::TYPE_IMAGE)
            throw new NotFoundHttpException('Its not an image.');
        $file->rotate($direction);
    }

    public
    function actions()
    {
        return [

        ];
    }

}
