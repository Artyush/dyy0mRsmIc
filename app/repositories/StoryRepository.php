<?php
namespace app\repositories;

use app\models\Story;
use yii\db\ActiveQuery;
use yii\db\Exception;

final class StoryRepository
{
    public function listActiveQuery(): ActiveQuery
    {
        return Story::find()
            ->where(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function findActiveByIdAndToken(int $id, string $token): ?Story
    {
        return Story::find()
            ->where(['id' => $id, 'manage_token' => $token, 'deleted_at' => null])
            ->one();
    }

    public function countByIp(string $ip): int
    {
        return (int) Story::find()
            ->where(['ip' => $ip, 'deleted_at' => null])
            ->count();
    }

    /**
     * @throws Exception
     */
    public function save(Story $model, bool $validate = false, ?array $attrs = null): bool
    {
        return $model->save($validate, $attrs);
    }

    /**
     * @throws Exception
     */
    public function softDelete(Story $model, string $deletedIp): bool
    {
        $model->deleted_at = time();
        $model->deleted_ip = $deletedIp;
        return $model->save(false, ['deleted_at', 'deleted_ip']);
    }

}
