<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "flow_item".
 *
 * @property integer $flow_id
 * @property string $flow_name
 * @property integer $form_id
 * @property integer $sort_no
 * @property integer $flow_type
 * @property integer $flow_doc
 * @property integer $category_id
 * @property string $flow_desc
 * @property integer $free_other
 * @property integer $flow_status
 * @property string $auto_name
 * @property string $auto_field
 * @property integer $auto_len
 * @property integer $is_auto
 * @property integer $auto_task
 * @property string $view_user
 * @property integer $view_priv
 * @property integer $is_version
 */
class FlowItem extends \common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'flow_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['flow_name'], 'required'],
            [['form_id', 'sort_no', 'flow_type', 'flow_doc', 'category_id', 'free_other', 'flow_status', 'auto_len', 'is_auto', 'auto_task', 'view_priv', 'is_version'], 'integer'],
            [['flow_desc', 'auto_name', 'auto_field', 'view_user'], 'string'],
            [['flow_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'flow_id' => Yii::t('app', 'Flow ID'),
            'flow_name' => Yii::t('app', 'Flow Name'),
            'form_id' => Yii::t('app', 'Form ID'),
            'sort_no' => Yii::t('app', 'Sort No'),
            'flow_type' => Yii::t('app', 'Flow Type'),
            'flow_doc' => Yii::t('app', 'Flow Doc'),
            'category_id' => Yii::t('app', 'Category ID'),
            'flow_desc' => Yii::t('app', 'Flow Desc'),
            'free_other' => Yii::t('app', 'Free Other'),
            'flow_status' => Yii::t('app', 'Flow Status'),
            'auto_name' => Yii::t('app', 'Auto Name'),
            'auto_field' => Yii::t('app', 'Auto Field'),
            'auto_len' => Yii::t('app', 'Auto Len'),
            'is_auto' => Yii::t('app', 'Is Auto'),
            'auto_task' => Yii::t('app', 'Auto Task'),
            'view_user' => Yii::t('app', 'View User'),
            'view_priv' => Yii::t('app', 'View Priv'),
            'is_version' => Yii::t('app', 'Is Version'),
        ];
    }
}
