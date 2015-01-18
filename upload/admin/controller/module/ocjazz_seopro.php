<?php
################################################################################################
# Seopro Module
# Description
# Copyright (c) 2015 Sergey Ogarkov <sogarkov@gmail.com>
# GNU GPL v.3
################################################################################################
class ControllerModuleOcjazzSeopro extends Controller {

    private $error = array();

    public function index() {
        //Load the language file for this module
        $this->load->language('module/ocjazz_seopro');

        //Set the title from the language file $_['heading_title'] string
        $this->document->setTitle($this->language->get('heading_title'));
        
        $text_strings = array(
            'heading_title',
            'text_edit',
        	'no_options'
        );

        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }
        //END LANGUAGE


        $data['token'] = $this->session->data['token'];

        //SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/ocjazz_seopro', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        
        //Send the output.
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/ocjazz_seopro.tpl', $data));
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
