<?php

namespace app\components\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\web\User;

/**
 * Поведение для моделей для автоматического указания id пользователя, создавшего/изменившего запись
 *
 * @package common\base
 */
class UserBehavior extends AttributeBehavior {

	/**
	 * Поле, в которое пишем id пользователя, создавшего запись
	 * если false, то не будет использоваться
	 * @var string
	 */
	public $createdByAttribute = 'create_user_id';
	const ATTR_CREATED_BY_ATTRIBUTE = 'createdByAttribute';

	/**
	 * Поле, в которое пишем id пользователя, обновившего запись
	 * @var string
	 * если false, то не будет использоваться
	 */
	public $updatedByAttribute = 'update_user_id';
	const ATTR_UPDATED_BY_ATTRIBUTE = 'updatedByAttribute';

	/** @var User */
	private $userService;

	public function __construct(User $userService, $config = [])
    {
        parent::__construct($config);
        $this->userService = $userService;
    }

    /**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		if (empty($this->attributes)) {
			$this->attributes = [
				BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
				BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
			];
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue($event) {
	    return $this->userService->id;
	}
}