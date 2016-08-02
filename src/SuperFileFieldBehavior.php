<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 23.06.2016
 * Time: 13:43
 */


namespace floor12\superfilefield;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\validators\Validator;


class SuperFileFieldBehavior extends Behavior
{

    public $superfilesArray;
    public $fields = [];


    public function superFileForm($field)
    {
        SuperFileFieldAsset::register(\Yii::$app->view);
        return \Yii::$app->view->renderFile('@vendor/floor12/yii2-super-file-field/views/fileForm.php', [
            'id' => $this->owner->id,
            'attributeName' => $this->fields[$field],
            'field' => $field,
            'classname' => str_replace('\\', '\\\\', $this->owner->className()),
        ]);
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'superfilesUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'superfilesUpdate'
        ];
    }

    public function superfilesUpdate()
    {
        $order = 0;
        if ($this->superfilesArray)
            foreach ($this->superfilesArray as $field) {
                if ($field) foreach ($field as $id) {
                    $file = File::findOne($id);
                    $file->object_id = $this->owner->id;
                    $file->ordering = $order;
                    $file->save();
                    $order++;
                    if (!$file->save()) {
                        print_r($file->getErrors());
                    }
                }
            }
    }

    public function attach($owner)
    {
        parent::attach($owner);
        $validators = $owner->validators;
        $validator = Validator::createValidator('safe', $owner, ['superfilesArray']);
        $validators->append($validator);
    }

    function __construct()
    {
        parent::__construct();
        self::checkImageDir();
    }

    public function checkImageDir()
    {
        $imagePath = \Yii::getAlias('@webroot') . '/' . File::FOLDER_NAME;
        if (!file_exists($imagePath)) {
            if (mkdir($imagePath))
                return true;
        }
        return true;
        die();
    }

    public function getSuperFiles()
    {
        $ret = [];
        if ($this->fields) foreach ($this->fields as $field => $fieldName) {
            $ret[$field] = File::find()->orderBy('ordering')->where(['field' => $field, 'object_id' => $this->owner->id, 'class' => $this->owner->className()])->all();
        }
        return $ret;
    }

    public function getAllSuperFilesAsArray()
    {
        $ret = [];
        if ($this->superFiles)
            foreach ($this->superFiles as $field => $array)
                foreach ($array as $file) {
                    $ret[] = $file->id;
                }
        return $ret;
    }

}