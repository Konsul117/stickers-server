<?php

namespace app\components\validators;

use yii\validators\FilterValidator;

/**
 * Фильтр-валидатор для чистого текста (без тегов и пр.).
 */
class FilterClearTextValidator extends FilterValidator {

	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->filter = function ($value) {
			return $this->filter($value);
		};

		parent::init();
	}

	/**
	 * Фильтрация.
	 *
	 * @param string $value Фильтруемая строка.
	 *
	 * @return string Результат
	 */
	public function filter($value) {
		$filteredValue = trim($value);

		//очищаем от табов, переносов строк, лишних пробелов и пр, заменяя на одиночный пробел
		$filteredValue = preg_replace('/(\s{2,})/', ' ', $filteredValue);

		$filteredValue = strip_tags($filteredValue);

		return $filteredValue;
	}

}