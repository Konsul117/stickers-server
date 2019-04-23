<?php

namespace app\components\validators;

use yii\validators\FilterValidator;

/**
 * Расширение валидатора: автоматическое приведение к типу integer.
 */
class IntValValidator extends FilterValidator {
	/** @inheritdoc */
	public $filter = 'intval';

	/** @inheritdoc */
	public $skipOnEmpty = true;
}