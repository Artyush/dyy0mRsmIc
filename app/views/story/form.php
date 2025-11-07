<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\forms\StoryForm $model */
/** @var ActiveForm $form */
?>
<div class="story-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'body') ?>
        <?= $form->field($model, 'author_name') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'verifyCode') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- story-form -->
