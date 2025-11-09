<?php

use app\services\StoryService;
use yii\widgets\ListView;
use yii\helpers\Html;

/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\forms\StoryForm $formModel */
$this->title = 'StoryValut';
?>

<div class="row">
    <div class="col-md-8">
        <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => function ($model) {
                    /** @var app\models\Story $model */
                    $created = Yii::$app->formatter->asRelativeTime($model->created_at);
                    /** @var StoryService $svc */
                    $svc = Yii::$container->get(StoryService::class);
                    $ipMasked = $svc->maskIp($model->ip);
                    $count = $svc->countByIp($model->ip);

                    $forms = ['пост', 'поста', 'постов'];
                    $n = $count % 100;
                    $n1 = $count % 10;
                    $formWord = ($n > 10 && $n < 20)
                            ? $forms[2]
                            : (($n1 == 1) ? $forms[0] : (($n1 >= 2 && $n1 <= 4) ? $forms[1] : $forms[2]));
                    return '
                <div class="card card-default" style="margin-bottom: 12px;">
                    <div class="card-body">
                        <h5 class="card-title">'.Html::encode($model->author_name ?: "Аноним").'</h5>
                        <p>'.strip_tags($model->body, "<b><i><s>").'</p>
                        <p class="text-muted">
                            '.$created.' | '.$ipMasked.' | '.$count.' '.$formWord.'
                        </p>
                    </div>
                </div>
                ';
                },
                'emptyText' => 'Пока нет сообщений.',
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= $this->render('form', ['model' => $formModel]) ?>
    </div>
</div>
