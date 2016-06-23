<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 23.06.2016
 * Time: 13:43
 */


namespace floor12\superfilefield;

use yii\base\Behavior;


class SuperFileFieldBehavior extends Behavior
{

    public $fields = [];



    public function superFileForm($field)
    {
        SuperFileFieldAsset::register(\Yii::$app->view);
        return \Yii::$app->view->renderFile('@vendor/floor12/yii2-super-file-field/views/fileForm.php', ['attributeName' => $this->fields[$field], 'field' => $field, 'class' => \yii\helpers\StringHelper::basename(get_class($this->owner))]);
    }


}