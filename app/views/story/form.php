<?php

use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\forms\StoryForm $model */
/** @var ActiveForm $form */
?>
<div class="story-form">

    <?php
    $form = ActiveForm::begin([
        'action' => ['story/create'],
        'method' => 'post',
    ]);
    ?>

    <?= $form->field($model, 'author_name') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'body')->textarea([
        'rows' => 4,
        'maxlength' => 1000,
        'placeholder' => 'Напишите своё сообщение...',
    ]) ?>
    <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
        'captchaAction' => 'story/captcha',
        'template' => '
        <div class="row">
            <div class="col-xs-6">{image}</div>
            <div class="col-xs-6">{input}</div>
        </div>'
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
