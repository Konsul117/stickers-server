<?php

namespace app\models\db;

use app\components\behaviors\UserBehavior;
use app\components\validators\FilterClearTextValidator;
use app\components\validators\IntegerValidator;
use app\components\validators\PositiveIntegerValidator;
use yii\db\ActiveRecord;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;

/**
 * @property int    $id        Идентификатор
 * @property string $title     Название
 * @property int    $author_id Автор
 * @property int    $index     Индекс сортировки
 */
class Board extends ActiveRecord
{
    const ATTR_ID        = 'id';
    const ATTR_TITLE     = 'title';
    const ATTR_AUTHOR_ID = 'author_id';
    const ATTR_INDEX     = 'index';

    public function behaviors()
    {
        return [
            [
                'class'                                 => UserBehavior::class,
                UserBehavior::ATTR_CREATED_BY_ATTRIBUTE => static::ATTR_AUTHOR_ID,
                UserBehavior::ATTR_UPDATED_BY_ATTRIBUTE => null,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [static::ATTR_ID,        PositiveIntegerValidator::class],
            [static::ATTR_TITLE,     RequiredValidator::class],
            [static::ATTR_TITLE,     StringValidator::class, 'max' => 50],
            [static::ATTR_TITLE,     FilterClearTextValidator::class],
            [static::ATTR_AUTHOR_ID, PositiveIntegerValidator::class],
            [static::ATTR_INDEX,     IntegerValidator::class],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) === false) {
            return false;
        }

        if ($insert && $this->index === null) {
            /** @var int|false $prevBoard */
            $prevBoardIndex = static::find()
                ->select([Board::ATTR_INDEX])
                ->where([
                    static::ATTR_AUTHOR_ID => $this->author_id,
                ])
                ->orderBy([static::ATTR_INDEX => SORT_DESC])
                ->scalar();

            if ($prevBoardIndex !== false) {
                $this->index = $prevBoardIndex + 1;
            } else {
                $this->index = 0;
            }
        }

        return true;
    }
}