<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Controller\Settings;

use CP_Controller;

/**
 * Template Settings Controller
 */
class Template extends Settings {

	public function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group('can_access_design', 'can_admin_design'))
		{
			show_error(lang('unauthorized_access'), 403);
		}
	}

	/**
	 * General Settings
	 */
	public function index()
	{
		ee()->load->model('admin_model');

		ee()->lang->load('design');

		$vars['sections'] = array(
			array(
				array(
					'title' => 'strict_urls',
					'desc' => 'strict_urls_desc',
					'fields' => array(
						'strict_urls' => array(
							'type' => 'inline_radio',
							'choices' => array(
								'y' => 'enable',
								'n' => 'disable'
							)
						)
					)
				),
				array(
					'title' => 'site_404',
					'desc' => 'site_404_desc',
					'fields' => array(
						'site_404' => array(
							'type' => 'select',
							'choices' => $this->templateListSearch(),
							'filter_url' => ee('CP/URL', 'settings/template/search-templates')->compile(),
							'value' => [
								ee()->config->item('site_404') => ee()->config->item('site_404')
							],
							'no_results' => array(
								'text' => 'no_templates_found',
								'link_text' => 'create_new_template',
								'link_href' => ee('CP/URL')->make('design')
							)
						)
					),
				),
				array(
					'title' => 'save_tmpl_revisions',
					'desc' => 'save_tmpl_revisions_desc',
					'fields' => array(
						'save_tmpl_revisions' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'max_tmpl_revisions',
					'desc' => 'max_tmpl_revisions_desc',
					'fields' => array(
						'max_tmpl_revisions' => array('type' => 'text')
					)
				),
			)
		);

		ee()->form_validation->set_rules(array(
			array(
				'field' => 'max_tmpl_revisions',
				'label' => 'lang:max_tmpl_revisions',
				'rules' => 'integer'
			),
		));

		$base_url = ee('CP/URL')->make('settings/template');

		ee()->form_validation->validateNonTextInputs($vars['sections']);

		if (AJAX_REQUEST)
		{
			ee()->form_validation->run_ajax();
			exit;
		}
		elseif (ee()->form_validation->run() !== FALSE)
		{
			if ($this->saveSettings($vars['sections']))
			{
				ee()->view->set_message('success', lang('preferences_updated'), lang('preferences_updated_desc'), TRUE);
			}

			ee()->functions->redirect($base_url);
		}
		elseif (ee()->form_validation->errors_exist())
		{
			ee()->view->set_message('issue', lang('settings_save_error'), lang('settings_save_error_desc'));
		}

		ee()->view->base_url = $base_url;
		ee()->view->ajax_validate = TRUE;
		ee()->view->cp_page_title = lang('template_settings');
		ee()->view->save_btn_text = 'btn_save_settings';
		ee()->view->save_btn_text_working = 'btn_saving';

		ee()->cp->set_breadcrumb(ee('CP/URL')->make('design'), lang('template_manager'));

		ee()->cp->render('settings/form', $vars);
	}

	private function templateListSearch()
	{
		$search_query = ee('Request')->get('search');

		$templates = ee('Model')->get('Template')
			->with('TemplateGroup')
			->order('TemplateGroup.group_name')
			->order('Template.template_name');

		if ($search_query)
		{
			$templates = $templates = $templates->all()->filter(function($template) use ($search_query) {
				return strpos(strtolower($template->getPath()), strtolower($search_query)) !== FALSE;
			});
		}
		else
		{
			$templates = $templates->limit(100)->all();
		}

		$results = [];
		foreach ($templates as $template)
		{
			$results[$template->getPath()] = $template->getPath();
		}

		return $results;
	}

	public function searchTemplates()
	{
		return json_encode($this->templateListSearch());
	}
}
// END CLASS

// EOF
