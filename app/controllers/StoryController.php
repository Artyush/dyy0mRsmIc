<?php

namespace app\controllers;

use app\models\Story;
use app\services\StoryService;
use Random\RandomException;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\captcha\CaptchaAction;
use yii\db\Exception;
use yii\di\NotInstantiableException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;

final class StoryController extends Controller
{

    private StoryService $storyService;

    public function __construct(
        string $id,
        Module $module,
        StoryService $storyService,
        array $config = []
    )
    {
        $this->storyService = $storyService;
        parent::__construct($id, $module, $config);
    }

    public function actions(): array
    {
        return [
            'captcha' => ['class' => CaptchaAction::class],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index', [
            'formModel' => $this->storyService->createStoryForm(),
            'dataProvider' => $this->storyService->getDataProvider(),
        ]);
    }

    /**
     * @throws Exception
     * @throws RandomException
     * @throws BadRequestHttpException
     */
    public function actionCreate(Request $request, Session $session): Response
    {
        if (!$request->isPost) {
            throw new BadRequestHttpException('Invalid request');
        }

        $form = $this->storyService->createStoryForm();
        if (!$form->load($request->post()) || !$form->validate()) {
            $session->setFlash('error', 'Проверьте корректность полей.');

            return $this->redirect(['index']);
        }

        $res = $this->storyService->createFromForm($form, $request->userIP ?? '0.0.0.0', $request->userAgent);

        $session->setFlash($res['ok'] ? 'success' : 'error', $res['message']);

        return $this->redirect(['index']);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionEdit(int $id, string $token, Request $request, Session $session): string|Response
    {
        if ($request->isGet) {
            $story = $this->storyService->findActiveByIdAndToken($id, $token);
            if (!$story) {
                throw new NotFoundHttpException('Пост не найден или недоступен.');
            }

            $form = $this->storyService->createStoryForm($story);

            return $this->render('edit', [
                'model' => $story,
                'formModel' => $form,
            ]);
        }

        if (!$request->isPost) {
            throw new BadRequestHttpException('Invalid request');
        }

        $newBody = (string)($request->post('StoryForm')['body'] ?? '');
        $res = $this->storyService->editBody($id, $token, $newBody);

        $session->setFlash($res['ok'] ? 'success' : 'error', $res['message']);

        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionConfirmDelete(int $id, string $token): string
    {
        $model =  $this->storyService->findActiveByIdAndToken($id, $token);
        if (!$model) {
            throw new NotFoundHttpException('Пост не найден или уже удалён.');
        }
        return $this->render('confirm-delete', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionDelete(int $id, string $token, Request $request, Session $session): Response
    {
        if (!$request->isPost) {
            throw new BadRequestHttpException('Invalid request');
        }

        $res = $this->storyService->softDelete($id, $token, $request->userIP ?? '0.0.0.0');

        $session->setFlash($res['ok'] ? 'success' : 'error', $res['message']);
        return $this->redirect(['index']);
    }
}
