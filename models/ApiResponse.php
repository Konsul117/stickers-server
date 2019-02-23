<?php

namespace app\models;

use app\enum\ResponseCodeEnum;
use yii\base\Model;
use yii\validators\RangeValidator;
use yii\validators\RequiredValidator;

/**
 * Модель ответа на запрос API.
 */
class ApiResponse extends Model {
	/** @var mixed Данные */
	public $data;
	const ATTR_DATA = 'data';

	/** @var string|null Сообщение в случае ошишки */
	public $message;
	const ATTR_MESSAGE = 'message';

	/** @var int Код */
	public $code;
	const ATTR_CODE = 'code';

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[static::ATTR_CODE, RequiredValidator::class],
			[static::ATTR_CODE, RangeValidator::class, 'range' => ResponseCodeEnum::getList()],
		];
	}
}
