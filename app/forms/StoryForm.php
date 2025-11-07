<?php

namespace app\forms;

use app\models\Story;
use Random\RandomException;
use yii\base\Model;

class StoryForm extends Model
{
    public ?string $author_name = null;
    public ?string $email = null;
    public string $body = '';
    public string $verifyCode = '';

    public function rules(): array
    {
        return [
            ['body', 'required'],
            ['body', 'trim'],
            ['body', 'string', 'min' => 5, 'max' => 1000],
            ['body', function ($attribute) {
                if (preg_match('/^\s*$/u', $this->$attribute)) {
                    $this->addError($attribute, 'Сообщение не может состоять только из пробелов.');
                }
            }],
            ['author_name', 'string', 'min' => 2, 'max' => 15],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 191],
            ['verifyCode', 'captcha', 'captchaAction' => 'story/captcha'],
        ];
    }

    /**
     * @throws RandomException
     */
    public function toStory(string $ip, ?string $userAgent): Story
    {
        $model = new Story();
        $model->author_name = $this->author_name ?: null;
        $model->email = $this->email ?: null;
        $model->body = strip_tags($this->body, '<b><i><s>');
        $model->ip = $ip;
        $model->user_agent = $userAgent;
        $model->created_at = time();
        $model->manage_token = bin2hex(random_bytes(16));

        return $model;
    }
}
