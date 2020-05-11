<?php

namespace app\models\db;

use app\components\behaviors\UserBehavior;
use app\components\validators\FilterClearTextValidator;
use app\components\validators\IntegerValidator;
use app\components\validators\PositiveIntegerValidator;
use yii\db\ActiveRecord;
use yii\validators\RequiredValidator;

/**
 * Стикер.
 *
 * @property int    $id        Идентификатор
 * @property int    $index     Индекс сортировки
 * @property string $text      Текст
 * @property int    $author_id Автор
 * @property int    $board_id  Доска
 *
 * @author Казанцев Александр <kazancev.al@dns-shop.ru>
 */
class Sticker extends ActiveRecord {

    const ATTR_ID        = 'id';
    const ATTR_INDEX     = 'index';
    const ATTR_TEXT      = 'text';
    const ATTR_AUTHOR_ID = 'author_id';
    const ATTR_BOARD_ID  = 'board_id';

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

    /**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[static::ATTR_ID,       PositiveIntegerValidator::class],
			[static::ATTR_INDEX,    RequiredValidator::class],
			[static::ATTR_INDEX,    IntegerValidator::class],
			[static::ATTR_TEXT,     RequiredValidator::class],
			[static::ATTR_TEXT,     FilterClearTextValidator::class],
            [static::ATTR_BOARD_ID, RequiredValidator::class],
            [static::ATTR_BOARD_ID, PositiveIntegerValidator::class],
		];
	}
}