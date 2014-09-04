<?php
namespace EllisLab\ExpressionEngine\Model\Log\Gateway;

use EllisLab\ExpressionEngine\Service\Model\Gateway\RowDataGateway;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Developer Log Table
 *
 * @package		ExpressionEngine
 * @subpackage	Log\Gateway
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class DeveloperLogGateway extends RowDataGateway {
	protected static $_table_name = 'developer_log';
	protected static $_primary_key = 'log_id';
	protected static $_related_entites = array(
		'template_id' => array(
			'gateway' => 'TemplateGateway',
			'key' => 'template_id'
		),
	);


	protected $log_id;
	protected $timestamp;
	protected $viewed;
	protected $description;
	protected $function;
	protected $line;
	protected $file;
	protected $deprecated_since;
	protected $use_instead;
	protected $template_id;
	protected $template_name;
	protected $template_group;
	protected $addon_module;
	protected $addon_method;
	protected $snippets;
	protected $hash;

}