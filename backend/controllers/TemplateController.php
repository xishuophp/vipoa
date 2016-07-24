<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\controllers\BaseController;
use common\base\YiiForum;

/**
 * 后台页面模板
 *
 */
class TemplateController extends BaseController
{
    public $layout = 'template';

    public function actionIndex()
    {

    }

    public function actionForm()
    {
        
    }

}