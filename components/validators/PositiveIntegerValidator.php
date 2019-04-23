<?php

namespace app\components\validators;

/**
 * @inheritdoc
 */
class PositiveIntegerValidator extends IntegerValidator {
	/** @inheritdoc */
	public $min = 0;
}