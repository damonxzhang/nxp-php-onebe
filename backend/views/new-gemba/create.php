<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\NewGemba */

?>
<div class="new-gemba-create">

    <?= $this->render('_form', [
        'model' => $model,
        'userInfo' => $userInfo
    ]) ?>

</div>
