<?php

namespace app\services;

use app\forms\StoryForm;
use app\models\Story;
use app\repositories\StoryRepository;
use Random\RandomException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\Url;

final class StoryService
{
    public const int|float EDIT_WINDOW_SEC = 12 * 3600;
    public const int|float DELETE_WINDOW_SEC = 14 * 86400;
    public const int DEFAULT_RATE_LIMIT_SEC = 18;

    public function __construct(
        private readonly StoryRateLimiter $rateLimiter,
        private readonly MailService $mailService,
        private readonly StoryRepository  $storyRepository,
    ) {
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function createFromForm(StoryForm $form, string $ip, ?string $ua, int $periodSec = self::DEFAULT_RATE_LIMIT_SEC): array
    {
        if (!$this->rateLimiter->canPost($ip, $periodSec)) {
            return [
                'ok' => false,
                'message' => 'Можно отправлять одно стори раз в 3 минуты. Следующая отправка: ' .
                Yii::$app->formatter->asRelativeTime($this->rateLimiter->nextAllowedAt($ip, $periodSec))];
        }

        $story = new Story();
        $story->author_name = $form->author_name ?: null;
        $story->email = $form->email ?: null;
        $story->body = strip_tags($form->body, '<b><i><s>');
        $story->ip = $ip;
        $story->user_agent = $ua;
        $story->created_at = time();
        $story->manage_token = bin2hex(random_bytes(16));

        if (!$this->storyRepository->save($story)) {
            return ['ok' => false, 'message' => 'Failed to save the story.'];
        }

        $this->rateLimiter->markPosted($ip, $periodSec);
        $editLink = Url::to(['story/edit', 'id'=>$story->id, 'token'=>$story->manage_token], true);
        $deleteLink  = Url::to(['story/confirm-delete', 'id'=>$story->id, 'token'=>$story->manage_token], true);
        $this->mailService->sendManageLinks($story->email, $editLink, $deleteLink);

        return ['ok' => true, 'message' => 'Стори сохранено! Приватные ссылки отправлены на e-mail.'];
    }

    /**
     * @throws Exception
     */
    public function editBody(int $id, string $token, string $newBody): array
    {
        $story = $this->storyRepository->findActiveByIdAndToken($id, $token);
        if (!$story) {
            return ['ok' => false, 'error' => 'not_found'];
        }
        if ((time() - $story->created_at) > self::EDIT_WINDOW_SEC) {
            return ['ok' => false, 'error' => 'edit_window_expired'];
        }

        $story->body = strip_tags($newBody);
        if (!$this->storyRepository->save($story, false, ['body'])) {
            return ['ok' => false, 'error' => 'save_failed'];
        }

        return ['ok' => true, 'message' => 'Стори изменено'];
    }

    /**
     * @throws Exception
     */
    public function softDelete(int $id, string $token, string $deletedIp): array
    {
        $story = $this->storyRepository->findActiveByIdAndToken($id, $token);
        if (!$story) {
            return ['ok' => false, 'error' => 'not_found'];
        }
        if ((time() - $story->created_at) > self::DELETE_WINDOW_SEC) {
            return ['ok' => false, 'error' => 'delete_window_expired'];
        }

        if (!$this->storyRepository->softDelete($story, $deletedIp)) {
            return ['ok' => false, 'error' => 'save_failed'];
        }

        return ['ok' => true, 'message' => 'Стори  удалено '];
    }

    public static function maskIp(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[2] = '**';
            $parts[3] = '**';
            return implode('.', $parts);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            $parts = array_pad($parts, 8, '0000');
            $parts[4] = '****';
            $parts[5] = '****';
            $parts[6] = '****';
            $parts[7] = '****';
            return implode(':', $parts);
        }

        return $ip;
    }

    public function countByIp(string $ip): int
    {
        return $this->storyRepository->countByIp($ip);
    }

    public function getDataProvider(int $pageSize = 10): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->storyRepository->listActiveQuery(),
            'pagination' => ['pageSize' => $pageSize],
        ]);
    }

    public function createStoryForm(?Story $story = null): StoryForm
    {
        $form = new StoryForm();
        if ($story) {
            $form->author_name = $story->author_name;
            $form->email = $story->email;
            $form->body = $story->body;
        }

        return $form;
    }

    public function findActiveByIdAndToken(int $id, string $token): ?Story
    {
        return $this->storyRepository->findActiveByIdAndToken($id, $token);
    }

}
