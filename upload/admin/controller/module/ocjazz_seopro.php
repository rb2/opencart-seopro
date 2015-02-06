<?php
/**
 * Seopro Module
 * 
 * @copyright 2015 OpenCartJazz
 * @link http//www.opencartjazz.com
 * @author Sergey Ogarkov <sogarkov@gmail.com>
 * 
 * @license GNU GPL v.3
 */

/**
 * Configurator
 *
 */
class ControllerModuleOcjazzSeopro extends Controller {

    private $error = array();

    public function index() {
        //Load the language file for this module
        $this->load->language('module/ocjazz_seopro');

        //Set the title from the language file $_['heading_title'] string
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('setting/setting');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
        	$this->model_setting_setting->editSetting('ocjazz_seopro', $this->request->post);
        	$this->session->data['success'] = $this->language->get('text_success');
        	$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        
        $text_strings = array(
            'heading_title',
            'text_edit',
        	'text_no',
        	'text_yes',
        	'entry_hide_default',
        	'help_hide_default',
        	'button_save',
        	'button_cancel',
         );

        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
        //END LANGUAGE


        $data['token'] = $this->session->data['token'];

        //SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/ocjazz_seopro', 'token=' . $this->session->data['token'], 'SSL'),
        );

		$data['action'] = $this->url->link('module/ocjazz_seopro', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        
    	if (isset($this->request->post['ocjazz_seopro_hide_default'])) {
			$data['ocjazz_seopro_hide_default'] = $this->request->post['ocjazz_seopro_hide_default'];
		} else if($this->config->get('ocjazz_seopro_hide_default') !== null) {
			$data['ocjazz_seopro_hide_default'] = $this->config->get('ocjazz_seopro_hide_default');
		}
        else {
        	$data['ocjazz_seopro_hide_default'] = 1;
        }
        
        //Send the output.
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/ocjazz_seopro.tpl', $data));
    }
    

    protected function validate() {
    	if (!$this->user->hasPermission('modify', 'module/ocjazz_seopro')) {
    		$this->error['warning'] = $this->language->get('error_permission');
    	}
    
    	return !$this->error;
    }
    

    public function reset_cache() {
    	$this->cache->delete('seo_pro');
    }
    
    public function install() {
    	$this->load->model('extension/event');
    	 
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.category.add','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.category.edit','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.category.delete','module/ocjazz_seopro/reset_cache');

    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.information.add','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.information.edit','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.information.delete','module/ocjazz_seopro/reset_cache');

    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.manufacturer.add','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.manufacturer.edit','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.manufacturer.delete','module/ocjazz_seopro/reset_cache');

    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.product.add','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.product.edit','module/ocjazz_seopro/reset_cache');
    	$this->model_extension_event->addEvent('ocjazz_seopro','post.admin.product.delete','module/ocjazz_seopro/reset_cache');
    	 
    }

    public function uninstall() {
    	$this->load->model('extension/event');
    	$this->model_extension_event->deleteEvent('ocjazz_seopro');
    	 
    }
    
}
