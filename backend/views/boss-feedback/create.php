<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MaintainFeedback */

?>
<div class="x-body">


    <?= $this->render('_form', [
        'maintainModel' => $maintainModel,
        'feedbackModel' => $feedbackModel,
        'userInfo' => $userInfo,
    ]) ?>

</div>
