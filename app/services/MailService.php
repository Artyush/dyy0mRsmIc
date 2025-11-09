<?php
namespace app\services;

use Yii;

final readonly class MailService
{
    public function sendManageLinks(string $email, string $editLink, string $deleteLink): void
    {
        Yii::$app->mailer->compose()
            ->setFrom(['no-reply@storyvalut.local' => 'StoryValut'])
            ->setTo($email ?: 'debug@example.test')
            ->setSubject('Управление вашим постом — StoryValut')
            ->setTextBody("Редактировать (12ч): $editLink\nУдалить (14д): $deleteLink")
            ->send();
    }
}
