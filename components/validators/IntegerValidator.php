<?php

namespace app\components\validators;

use yii\validators\NumberValidator;

/**
 * @inheritdoc
 *
 * @author Казанцев Александр <kazancev.al@dns-shop.ru>
 */
class IntegerValidator extends NumberValidator {

	/** @inheritdoc */
	public $integerOnly = true;
}