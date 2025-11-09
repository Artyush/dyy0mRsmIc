<?php

namespace app\forms;

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
            [['body', 'author_name', 'email'], 'required'],
            ['body', 'trim'],
            ['body', 'string', 'min' => 5, 'max' => 1000],
            ['body', 'match',
                'pattern' => '/^(?!\s*$).+/',
                'message' => 'стори не может состоять только из пробелов.',
                'skipOnEmpty' => false,
            ],
            ['author_name', 'string', 'min' => 2, 'max' => 15],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 191],
            ['verifyCode', 'captcha', 'captchaAction' => 'story/captcha'],
        ];
    }
}
