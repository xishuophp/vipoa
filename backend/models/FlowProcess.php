<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "flow_process".
 *
 * @property integer $id
 * @property integer $flow_id
 * @property integer $prcs_id
 * @property integer $prcs_flag
 * @property string $prcs_name
 * @property string $prcs_item
 * @property string $prcs_dept_user
 * @property string $prcs_to
 * @property integer $sign_look
 * @property string $mail_to
 * @property string $prcs_out_set
 * @property string $prcs_in
 * @property string $prcs_in_set
 * @property string $prcs_out
 * @property integer $allow_back
 * @property string $condition_desc
 * @property integer $attach_edit_priv
 * @property integer $auto_approval
 */
class FlowProcess extends \common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'flow_process';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['flow_id', 'prcs_id', 'prcs_flag', 'sign_look', 'allow_back', 'attach_edit_priv', 'auto_approval'], 'integer'],
            [['prcs_name'], 'required'],
            [['prcs_item', 'prcs_dept_user', 'prcs_to', 'mail_to', 'prcs_out_set', 'prcs_in', 'prcs_in_set', 'prcs_out', 'condition_desc'], 'string'],
            [['prcs_name'], 'string', 'max' => 200],
            [['flow_id', 'prcs_id'], 'unique', 'targetAttribute' => ['flow_id', 'prcs_id'], 'message' => 'The combination of Flow ID and Prcs ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'flow_id' => Yii::t('app', 'Flow ID'),
            'prcs_id' => Yii::t('app', 'Prcs ID'),
            'prcs_flag' => Yii::t('app', 'Prcs Flag'),
            'prcs_name' => Yii::t('app', 'Prcs Name'),
            'prcs_item' => Yii::t('app', 'Prcs Item'),
            'prcs_dept_user' => Yii::t('app', 'Prcs Dept User'),
            'prcs_to' => Yii::t('app', 'Prcs To'),
            'sign_look' => Yii::t('app', 'Sign Look'),
            'mail_to' => Yii::t('app', 'Mail To'),
            'prcs_out_set' => Yii::t('app', 'Prcs Out Set'),
            'prcs_in' => Yii::t('app', 'Prcs In'),
            'prcs_in_set' => Yii::t('app', 'Prcs In Set'),
            'prcs_out' => Yii::t('app', 'Prcs Out'),
            'allow_back' => Yii::t('app', 'Allow Back'),
            'condition_desc' => Yii::t('app', 'Condition Desc'),
            'attach_edit_priv' => Yii::t('app', 'Attach Edit Priv'),
            'auto_approval' => Yii::t('app', 'Auto Approval'),
        ];
    }
}
