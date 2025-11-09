<?php
/** @var yii\web\View $this */
/** @var app\models\Story $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Удалить сообщение?';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="alert alert-warning">
    <p><strong>Внимание!</strong> Удаление доступно в течение 14 дней с момента публикации. Запись будет помечена как удалённая.</p>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><strong>Сообщение от <?= Html::encode($model->author_name ?: 'Аноним') ?></strong></div>
    <div class="panel-body">
        <p><?= strip_tags($model->body, '<b><i><s>') ?></p>
        <p class="text-muted">
            Опубликовано: <?= Yii::$app->formatter->asRelativeTime($model->created_at) ?>
        </p>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'action' => ['story/delete', 'id' => $model->id, 'token' => $model->manage_token],
    'method' => 'post',
]); ?>

<div class="form-group">
    <?= Html::submitButton('Да, удалить', ['class' => 'btn btn-danger']) ?>
    <?= Html::a('Отмена', ['story/index'], ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>
