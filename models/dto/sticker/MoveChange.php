<?php

namespace app\models\dto\sticker;

use app\components\validators\IntegerValidator;
use app\components\validators\IntValValidator;
use yii\base\Model;
use yii\validators\RequiredValidator;

/**
 * Данные о перемещении стикера.
 */
class MoveChange extends Model
{
    /** @var int Индекс позиции */
    public $index;
    const ATTR_INDEX = 'index';

    /** @var int Стикер */
    public $stickerId;
    const ATTR_STICKER_ID = 'stickerId';

    public function rules(): array
    {
        return [
            [static::ATTR_INDEX,      RequiredValidator::class],
            [static::ATTR_INDEX,      IntegerValidator::class],
            [static::ATTR_INDEX,      IntValValidator::class],
            [static::ATTR_STICKER_ID, RequiredValidator::class],
            [static::ATTR_STICKER_ID, IntegerValidator::class],
            [static::ATTR_STICKER_ID, IntValValidator::class],
        ];
    }
}