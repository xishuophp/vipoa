<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "flow_form".
 *
 * @property integer $form_id
 * @property string $form_name
 * @property string $print_model
 * @property string $print_model_short
 * @property integer $item_max
 * @property string $css
 * @property string $script
 * @property string $print_template
 */
class FlowForm extends \common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'flow_form';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_name', 'print_model', 'print_model_short'], 'required'],
            [['print_model', 'print_model_short', 'css', 'script'], 'string'],
            [['item_max'], 'integer'],
            [['form_name', 'print_template'], 'string', 'max' => 200],
            [['form_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'form_id' => Yii::t('app', 'Form ID'),
            'form_name' => Yii::t('app', 'Form Name'),
            'print_model' => Yii::t('app', 'Print Model'),
            'print_model_short' => Yii::t('app', 'Print Model Short'),
            'item_max' => Yii::t('app', 'Item Max'),
            'css' => Yii::t('app', 'Css'),
            'script' => Yii::t('app', 'Script'),
            'print_template' => Yii::t('app', 'Print Template'),
        ];
    }
}
