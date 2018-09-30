<?php

namespace app\modules\admin;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{



    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->layout = 'admin_lectory';

        // custom initialization code goes here
    }
}
