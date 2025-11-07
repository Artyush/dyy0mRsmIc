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
            [['body'], function ($attr) {
                if (preg_match('/^\s*$/u', (string)$this->$attr)) {
                    $this->addError($attr, 'The message cannot consist only of spaces.');
                }
            }],
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

    /**
     * @throws Exception
     */
    public function softDelete(?string $ip): bool
    {
        $this->deleted_at = time();
        $this->deleted_ip = $ip;
        return $this->save(false, ['deleted_at', 'deleted_ip']);
    }

    public static function findActive(): ActiveQuery
    {
        return static::find()
            ->where([
                'deleted_at' => null
            ]);
    }

    public static function findActiveById(int $id): ?self
    {
        return static::find()
            ->where([
                'id' => $id,
                'deleted_at' => null
            ])
            ->one();
    }

    public static function countByIp(string $ip): int
    {
        return (int)static::find()
            ->where([
                'ip' => $ip,
                'deleted_at' => null
            ])
            ->count();
    }

}
