<?php
/** @var yii\web\View $this */
/** @var app\models\Story $model */
/** @var app\forms\StoryForm $formModel */

use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать сообщение';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="row">
    <div class="col-md-8">
        <?php $form = ActiveForm::begin([
            'action' => ['story/edit', 'id' => $model->id, 'token' => $model->manage_token],
            'method' => 'post',
        ]); ?>

        <?= $form->field($formModel, 'author_name')->textInput(['maxlength' => 15, 'disabled' => true]) ?>
        <?= $form->field($formModel, 'email')->textInput(['maxlength' => 191, 'disabled' => true]) ?>

        <?= $form->field($formModel, 'body')->textarea([
            'rows' => 6,
            'maxlength' => 1000,
            'placeholder' => 'Измените текст сообщения (доступно 12 часов после публикации)…',
        ]) ?>

        <?= $form->field($formModel, 'verifyCode')->widget(Captcha::class, [
            'captchaAction' => 'story/captcha',
            'template' => '<div class="row"><div class="col-xs-6">{image}</div><div class="col-xs-6">{input}</div></div>',
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Отмена', ['story/index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
