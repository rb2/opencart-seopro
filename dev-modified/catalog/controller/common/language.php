<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['text_language'] = $this->language->get('text_language');

		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);
			}
		}

		if (!isset($this->request->get['route'])) {
			$data['redirect_route'] = 'common/home';
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['redirect_route']=$route;
			$data['redirect_query']=$url;
			$data['redirect_ssl']=$this->request->server['HTTPS'];
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/language.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/language.tpl', $data);
		} else {
			return $this->load->view('default/template/common/language.tpl', $data);
		}
	}

	public function language() {
		if (isset($this->request->post['code'])) {
			$this->session->data['language'] = $this->request->post['code'];
		}

		if (isset($this->request->post['redirect_route'])) {
			$url = $this->url->link($this->request->post['redirect_route'],
					isset($this->request->post['redirect_query']) ? html_entity_decode($this->request->post['redirect_query']) : '',
					isset($this->request->post['redirect_ssl']) ? $this->request->post['redirect_ssl'] : '');
			$this->response->redirect($url);
		} else {
			$this->response->redirect($this->url->link('common/home'));
		}
	}
}