<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "story".
 *
 * @property int $id
 * @property string|null $author_name
 * @property string|null $email
 * @property string $body
 * @property string $ip
 * @property string|null $user_agent
 * @property int $created_at
 * @property int|null $deleted_at
 * @property string|null $deleted_ip
 * @property string $manage_token
 */
class Story extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%story}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['author_name', 'email', 'user_agent', 'deleted_at', 'deleted_ip'], 'default', 'value' => null],
            [['body', 'ip', 'created_at', 'manage_token'], 'required'],
            [['body', 'email'], 'trim'],
            [['body'], 'string', 'min' => 5, 'max' => 1000],
            [['body'], 'match',
                'pattern' => '/^(?!\s*$).+/',
                'message' => 'The message cannot consist only of spaces.',
                'skipOnEmpty' => false,
            ],
            [['created_at', 'deleted_at'], 'integer'],
            [['author_name'], 'string', 'min' => 2, 'max' => 15],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 191],
            [['ip', 'deleted_ip'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 255],
            [['manage_token'], 'string', 'max' => 64],
            [['manage_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'author_name' => 'Author Name',
            'email' => 'Email',
            'body' => 'Body',
            'ip' => 'Ip',
            'user_agent' => 'User Agent',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
            'deleted_ip' => 'Deleted Ip',
            'manage_token' => 'Manage Token',
        ];
    }

}
