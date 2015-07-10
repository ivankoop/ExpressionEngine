<?php

namespace EllisLab\ExpressionEngine\Controllers\Files;

use CP_Controller;

use EllisLab\ExpressionEngine\Model\File\UploadDestination;
use EllisLab\ExpressionEngine\Library\Data\Collection;
use EllisLab\ExpressionEngine\Library\CP\Table;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Abstract Files Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
abstract class AbstractFiles extends CP_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group('can_access_content', 'can_access_files'))
		{
			show_error(lang('unauthorized_access'));
		}

		ee()->lang->loadfile('filemanager');

		ee()->view->can_admin_upload_prefs = ee()->cp->allowed_group('can_admin_upload_prefs');
	}

	protected function sidebarMenu($active = NULL)
	{
		$active_id = NULL;
		if (is_numeric($active))
		{
			$active_id = (int) $active;
		}

		// Register our menu
		$vars = array(
			'can_admin_upload_prefs' => ee()->cp->allowed_group('can_admin_upload_prefs'),
			'upload_directories' => array()
		);

		$upload_destinations = ee('Model')->get('UploadDestination')
			->filter('site_id', ee()->config->item('site_id'));

		foreach ($upload_destinations->all() as $destination)
		{
			if ($destination->memberGroupHasAccess(ee()->session->userdata['group_id']) === FALSE)
			{
				continue;
			}

			$class = ($active_id == $destination->id) ? 'act' : '';

			$data = array(
				'name' => $destination->name,
				'id' => $destination->id,
				'url' => ee('CP/URL', 'files/directory/' . $destination->id),
				'edit_url' => ee('CP/URL', 'files/uploads/edit/' . $destination->id),
			);

			if ( ! empty($class))
			{
				$data['class'] = $class;
			}

			$vars['upload_directories'][] = $data;
		}

		ee()->view->left_nav = ee('View')->make('files/menu')->render($vars);
		ee()->cp->add_js_script(array(
			'file' => array('cp/files/menu'),
		));
	}

	protected function stdHeader()
	{
		ee()->view->header = array(
			'title' => lang('file_manager'),
			'form_url' => ee('CP/URL', 'files'),
			'toolbar_items' => array(
				'download' => array(
					'href' => ee('CP/URL', 'files/export'),
					'title' => lang('export_all')
				)
			),
			'search_button_value' => lang('search_files')
		);
	}

	protected function buildTableFromFileCollection(Collection $files, $limit = 20)
	{
		$table = ee('CP/Table', array('autosort' => TRUE, 'limit' => $limit, 'autosearch' => TRUE));
		$table->setColumns(
			array(
				'title_or_name' => array(
					'encode' => FALSE
				),
				'file_type',
				'date_added',
				'manage' => array(
					'type'	=> Table::COL_TOOLBAR
				),
				array(
					'type'	=> Table::COL_CHECKBOX
				)
			)
		);
		$table->setNoResultsText(lang('no_uploaded_files'));

		$data = array();
		$missing_files = FALSE;

		$file_id = ee()->session->flashdata('file_id');
		$member_group = ee()->session->userdata['group_id'];

		foreach ($files as $file)
		{
			if ( ! $file->memberGroupHasAccess($member_group))
			{
				continue;
			}

			$toolbar = array(
				'view' => array(
					'href' => '',
					'rel' => 'modal-view-file',
					'class' => 'm-link',
					'title' => lang('view'),
					'data-file-id' => $file->file_id
				),
				'edit' => array(
					'href' => ee('CP/URL', 'files/file/edit/' . $file->file_id),
					'title' => lang('edit')
				),
				'crop' => array(
					'href' => ee('CP/URL', 'files/file/crop/' . $file->file_id),
					'title' => lang('crop'),
				),
				'download' => array(
					'href' => ee('CP/URL', 'files/file/download/' . $file->file_id),
					'title' => lang('download'),
				),
			);

			if ( ! $file->isImage())
			{
				unset($toolbar['view']);
				unset($toolbar['crop']);
			}

			$column = array(
				$file->title . '<br><em class="faded">' . $file->file_name . '</em>',
				$file->mime_type,
				ee()->localize->human_time($file->upload_date),
				array('toolbar_items' => $toolbar),
				array(
					'name' => 'selection[]',
					'value' => $file->file_id,
					'data' => array(
						'confirm' => lang('file') . ': <b>' . htmlentities($file->title, ENT_QUOTES) . '</b>'
					)
				)
			);

			$attrs = array();

			if ( ! $file->exists())
			{
				$attrs['class'] = 'missing';
				$missing_files = TRUE;
			}

			if ($file_id && $file->file_id == $file_id)
			{
				if (array_key_exists('class', $attrs))
				{
					$attrs['class'] .= ' selected';
				}
				else
				{
					$attrs['class'] = 'selected';
				}
			}

			$data[] = array(
				'attrs'		=> $attrs,
				'columns'	=> $column
			);
		}

		$table->setData($data);

		if ($missing_files)
		{
			ee('Alert')->makeInline('missing-files')
				->asWarning()
				->cannotClose()
				->withTitle(lang('files_not_found'))
				->addToBody(lang('files_not_found_desc'))
				->now();
		}

		return $table;
	}

}
// EOF