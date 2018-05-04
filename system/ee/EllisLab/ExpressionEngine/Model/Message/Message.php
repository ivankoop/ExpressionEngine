<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2018, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Model\Message;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Private message
 */
class Message extends Model {

	protected static $_primary_key = 'message_id';
	protected static $_table_name = 'message_data';

	protected static $_relationships = [
		'Member' => [
			'type' => 'belongsTo',
			'from_key' => 'sender_id'
		],
		'Recipients' => [
			'type' => 'hasAndBelongsToMany',
			'model' => 'Member',
			'pivot' => [
				'table' => 'message_copies',
				'left' => 'message_id',
				'right' => 'recipient_id'
			]
		]
	];

	protected static $_events = [
		'beforeDelete'
	];

	protected $message_id;
	protected $sender_id;
	protected $message_date;
	protected $message_subject;
	protected $message_body;
	protected $message_tracking;
	protected $message_attachments;
	protected $message_recipients;
	protected $message_cc;
	protected $message_hide_cc;
	protected $message_sent_copy;
	protected $total_recipients;
	protected $message_status;

	public function onBeforeDelete()
	{
		foreach ($this->Recipients as $recipient)
		{
			if ($recipient->private_messages > 0)
			{
				$recipient->private_messages--;
			}
		}

		$this->Recipients->save();
	}
}
// END CLASS

// EOF
