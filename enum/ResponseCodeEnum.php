<?php

namespace app\enum;

/**
 * Перечисление кодов возврата API.
 */
class ResponseCodeEnum {
	/** Успешно */
	const CODE_OK = 0;

	/** Обрабатываемая ошибка. Сообщение можно вывести пользователю. */
	const CODE_ERROR = 1;

	/** Внутренняя ошибка */
	const CODE_EXCEPTION = 2;

	/**
	 * Список кодов.
	 *
	 * @return string[]
	 */
	public static function getList(): array {
		return [
			static::CODE_OK,
			static::CODE_ERROR,
			static::CODE_EXCEPTION,
		];
	}
}
