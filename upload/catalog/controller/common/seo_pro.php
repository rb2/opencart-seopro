<?php
class ControllerCommonSeoPro extends Controller {
	private $cache_data = null;
	private $languages = array();
	private $config_language;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->cache_data = $this->cache->get('seo_pro');
		if (!$this->cache_data) {
			$query = $this->db->query("SELECT LOWER(`keyword`) as 'keyword', `query` FROM " . DB_PREFIX . "url_alias");
			$this->cache_data = array();
			foreach ($query->rows as $row) {
				$this->cache_data['keywords'][$row['keyword']] = $row['query'];
				$this->cache_data['queries'][$row['query']] = $row['keyword'];
			}
			$this->cache->set('seo_pro', $this->cache_data);
		}

		$query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language'");
		$this->config_language = $query->row['value'];

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1'");

		foreach ($query->rows as $result) {
			$this->languages[$result['code']] = $result;
		}

	}

	public function index() {

		$code = null;

		// If language specified in URI - switch to code from URI
		if(isset($this->request->get['_route_'])) {
			$route_ = $this->request->get['_route_'];
			$tokens = explode('/', $this->request->get['_route_']);

			if(array_key_exists($tokens[0], $this->languages)) {
				$code = $tokens[0];
				$this->request->get['_route_'] = substr($this->request->get['_route_'], strlen($code) + 1);
			}

			if(trim($this->request->get['_route_']) == '' || trim($this->request->get['_route_']) == 'index.php') {
				unset($this->request->get['_route_']);
			}
		}

		// Pavillion Theme fix for "original_route" param.
		// Theme: <http://themeforest.net/item/pavilion-premium-responsive-opencart-theme/9219645>
		if(isset($this->request->get['original_route'])) {
			unset($this->request->get['original_route']);
		}

		// Detect language code
		if(!isset($code)) {
			if (isset($this->session->data['language'])) {
				$code = $this->session->data['language'];
			} elseif (isset($this->request->cookie['language'])) {
				$code = $this->request->cookie['language'];
			} else {
				$code = $this->config_language;
			}
		}

		if(!isset($this->session->data['language']) || $this->session->data['language'] != $code) {
			$this->session->data['language'] = $code;
		}


		$xhttprequested =
			isset($this->request->server['HTTP_X_REQUESTED_WITH'])
			&& (strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

		$captcha = isset($this->request->get['route']) && $this->request->get['route']=='tool/captcha';

		if(!$xhttprequested && !$captcha) {
			setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/',
				($this->request->server['HTTP_HOST'] != 'localhost') ? $this->request->server['HTTP_HOST'] : false);
		}


		$this->config->set('config_language_id', $this->languages[$code]['language_id']);
		$this->config->set('config_language', $this->languages[$code]['code']);

		$language = new Language($this->languages[$code]['directory']);
		$language->load('default');
		$language->load($this->languages[$code]['directory']);
		$this->registry->set('language', $language);


		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		} else {
			return;
		}

		// Decode URL
		if (!isset($this->request->get['_route_'])) {
			$this->validate();
		} else {
			$route = $this->request->get['_route_'];
			unset($this->request->get['_route_']);
			$parts = explode('/', trim(utf8_strtolower($route), '/'));
			list($last_part) = explode('.', array_pop($parts));
			array_push($parts, $last_part);

			$rows = array();
			foreach ($parts as $keyword) {
				if (isset($this->cache_data['keywords'][$keyword])) {
					$rows[] = array('keyword' => $keyword, 'query' => $this->cache_data['keywords'][$keyword]);
				}
			}

			if (count($rows) == sizeof($parts)) {
				$queries = array();
				foreach ($rows as $row) {
					$queries[utf8_strtolower($row['keyword'])] = $row['query'];
				}

				reset($parts);
				foreach ($parts as $part) {

					// fix "undefined index" exception,
					// https://github.com/myopencart/ocStore/commit/51bd518ca3ee3330ae87314472f63def17dcf746
					if( ! isset($queries[$part])) return false;

					$url = explode('=', $queries[$part], 2);

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					} elseif (count($url) > 1) {
						$this->request->get[$url[0]] = $url[1];
					}
				}
			} else {
				$this->request->get['route'] = 'error/not_found';
			}

			if (isset($this->request->get['product_id'])) {
				$this->request->get['route'] = 'product/product';
				if (!isset($this->request->get['path'])) {
					$path = $this->getPathByProduct($this->request->get['product_id']);
					if ($path) $this->request->get['path'] = $path;
				}
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = 'product/category';
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer/info';
			} elseif (isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';

			// Compatibility with Shopencart News/Blog:
			} elseif (isset($this->request->get['news_id'])) {
				$this->request->get['route'] = 'news/article';
			} elseif (isset($this->request->get['author'])) {
				$this->request->get['route'] = 'news/ncategory';
			} elseif (isset($this->request->get['ncat'])) {
				$this->request->get['route'] = 'news/ncategory';
			} elseif (isset($this->request->get['ncategory_id'])) {
				$this->request->get['route'] = 'news/ncategory';
			} elseif (isset($this->request->get['author'])) {
				$this->request->get['route'] = 'news/ncategory';

			// Compatibility with VDF News (villagedefrance)
			} elseif (isset($this->request->get['news_id'])) {
				$this->request->get['route'] = 'information/news/news';
			} elseif (isset($this->request->get['posts_id'])) {
				$this->request->get['route'] = 'information/posts/posts';

			// Compatibility with some unknown Blog (blog/home, blog/category, blog/blog):
			} elseif (isset($this->request->get['blog_id'])) {
				$this->request->get['route'] = 'blog/blog';
			} elseif (isset($this->request->get['blog_category_id'])) {
				$this->request->get['route'] = 'blog/category';
				$this->request->get['blogpath'] = $this->request->get['blog_category_id'];
				unset($this->request->get['blog_category_id']);

			} elseif(isset($this->cache_data['queries'][$route_])) {
					header($this->request->server['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
					$this->response->redirect($this->cache_data['queries'][$route_], 301);
			} else {
				if (isset($queries[$parts[0]])) {
					$this->request->get['route'] = $queries[$parts[0]];
				}
			}


			$this->validate();

			if (isset($this->request->get['route'])) {
				return new Action($this->request->get['route']);
			}
		}
	}

	public function rewrite($link, $code = '') {
		if(!$code) {
			$code = $this->session->data['language'];
		}
		if($this->config->get('ocjazz_seopro_hide_default') && $code == $this->config_language) {
			$code='';
		}
		else {
			$code .='/';
		}
		if (!$this->config->get('config_seo_url')) return $link;

		$seo_url = '';

		$component = parse_url(str_replace('&amp;', '&', $link));

		$data = array();
		parse_str($component['query'], $data);

		$route = $data['route'];
		unset($data['route']);

		switch ($route) {
			case 'common/home':
				if ($component['scheme'] == 'https') {
					$link = $this->config->get('config_ssl');
				} else {
					$link = $this->config->get('config_url');
				}
				if($code != $this->config_language.'/') {
					$link .= $code;
				}
				if(isset($this->cache_data['queries']['common/home'])) {
					$link .= $this->cache_data['queries']['common/home'];
				}
				// Return clean shop link with any GET-parameters stripped off
				return $link;
				// (if you want to pass all parameters on homepage as is, comment the line above: `// return $link;`)
				break;

			case 'product/product':
				if (isset($data['product_id'])) {
					// Whitelist GET parameters
					$tmp = $data;
					$data = array();
					if ($this->config->get('config_seo_url_include_path')) {
						$data['path'] = $this->getPathByProduct($tmp['product_id']);
						if (!$data['path']) return $link;
					}

					$allowed_parameters = array(
						'product_id', 'tracking',
						// Compatibility with "OCJ Merchandising Reports" module.
						// Save and pass-thru module specific GET parameters.
						'uri', 'list_type',
						// Compatibility with Google Analytics
						'gclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
						'type', 'source', 'block', 'position', 'keyword',
						// Compatibility with Yandex Metrics, Yandex Market
						'yclid', 'ymclid', 'openstat', 'frommarket',
						'openstat_service', 'openstat_campaign', 'openstat_ad', 'openstat_source',
						// Compatibility with Themeforest Rgen templates (popup with product preview)
						'urltype'
						);
					foreach($allowed_parameters as $ap) {
						if (isset($tmp[$ap])) {
							$data[$ap] = $tmp[$ap];
						}
					}

				}
				break;

			case 'product/category':
				if (isset($data['path'])) {
					$category = explode('_', $data['path']);
					$category = end($category);
					$data['path'] = $this->getPathByCategory($category);
					if (!$data['path']) return $link;
				}
				break;

			// pages retreived by AJAX requests
			case 'product/product/review':
			case 'information/information/info':
			case 'information/information/agree':
			case 'product/live_options/js':
				return $link;
				break;

			default:
				break;
		}

		if ($component['scheme'] == 'https') {
			$link = $this->config->get('config_ssl');
		} else {
			$link = $this->config->get('config_url');
		}

		$link .= $code . 'index.php?route=' . $route;

		if (count($data)) {
			$link .= '&amp;' . urldecode(http_build_query($data, '', '&amp;'));
		}

		$queries = array();
		foreach ($data as $key => $value) {
			switch ($key) {
				case 'product_id':
				case 'manufacturer_id':
				case 'category_id':
				case 'information_id':
				case 'order_id':

				case 'search':
				case 'sub_category':
				case 'description':

				// Compatibility with Shopencart News/Blog:
				case 'news_id':
				case 'author':
				case 'ncat':

				// Compatibility with VDF News (villagedefrance)
				case 'news_id':
				case 'posts_id':

				// Compatibility with unknown Blog:
				case 'blog_id':
				case 'blog_category_id':
				case 'blogpath':

					$queries[] = $key . '=' . $value;
					unset($data[$key]);
					$postfix = 1;
					break;

				case 'page':
					if($value == 1) {
						unset($data[$key]);
					} else {
						$queries[] = $key . '=' . $value;
					}
					break;

				case 'path':
					// ATTN: user can set any path: path=2_4_1_2_3
					$category_path = explode('_', $value);

					// find real category path:
					$category_id = end($category_path);
					$categories = $this->getPathByCategory($category_id);

					// save all categories queries to find later their aliases
					$categories = explode('_', $categories);
					foreach ($categories as $category) {
						$queries[] = 'category_id=' . $category;
					}
					unset($data[$key]);
					break;

				default:
					break;
			}
		}

		if(empty($queries)) {
			$queries[] = $route;
		}

		$rows = array();
		foreach($queries as $query) {
			if(isset($this->cache_data['queries'][$query])) {
				$rows[] = array('query' => $query, 'keyword' => $this->cache_data['queries'][$query]);
			}

			// Leave "page=..." parameter as is
			if(preg_match('/^page=/', $query) === 1 && $query != 'page=1') {
				// Fix for site.com/?page=
				if (isset($this->cache_data['queries'][$route])) {
					$route_for_page = $this->cache_data['queries'][$route];
					$rows[] = array('query' => $query, 'keyword' => $route_for_page);
				} else {
					$rows[] = array('query' => $query, 'keyword' => '');
				}
			}
		}

		if(count($rows) == count($queries)) {
			$aliases = array();
			foreach($rows as $row) {
				$aliases[$row['query']] = $row['keyword'];
			}
			foreach($queries as $query) {
				$seo_url .= '/' . rawurlencode($aliases[$query]);
			}
		}

		if ($seo_url == '') return $link;

		$seo_url = $code . trim($seo_url, '/');

		if ($component['scheme'] == 'https') {
			$seo_url = $this->config->get('config_ssl') . $seo_url;
		} else {
			$seo_url = $this->config->get('config_url') . $seo_url;
		}

		if (isset($postfix)) {
			$seo_url .= trim($this->config->get('config_seo_url_postfix'));
		} else {
			$seo_url .= '/';
		}

		if(substr($seo_url, -2) == '//') {
			$seo_url = substr($seo_url, 0, -1);
		}


		if (count($data)) {
			$seo_url .= '?' . urldecode(http_build_query($data, '', '&amp;'));
		}

		return $seo_url;
	}

	private function getPathByProduct($product_id) {
		$product_id = (int)$product_id;
		if ($product_id < 1) return false;

		static $path = null;
		if (!is_array($path)) {
			$path = $this->cache->get('product.seopath');
			if (!is_array($path)) $path = array();
		}

		if (!isset($path[$product_id])) {
			$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . $product_id . "' ORDER BY main_category DESC LIMIT 1");

			$path[$product_id] = $this->getPathByCategory($query->num_rows ? (int)$query->row['category_id'] : 0);

			$this->cache->set('product.seopath', $path);
		}

		return $path[$product_id];
	}

	private function getPathByCategory($category_id) {
		$category_id = (int)$category_id;
		if ($category_id < 1) return false;

		static $path = null;
		if (!is_array($path)) {
			$path = $this->cache->get('category.seopath');
			if (!is_array($path)) $path = array();
		}

		if (!isset($path[$category_id])) {
			$max_level = 10;

			$sql = "SELECT CONCAT_WS('_'";
			for ($i = $max_level-1; $i >= 0; --$i) {
				$sql .= ",t$i.category_id";
			}
			$sql .= ") AS path FROM " . DB_PREFIX . "category t0";
			for ($i = 1; $i < $max_level; ++$i) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "category t$i ON (t$i.category_id = t" . ($i-1) . ".parent_id)";
			}
			$sql .= " WHERE t0.category_id = '" . $category_id . "'";

			$query = $this->db->query($sql);

			$path[$category_id] = $query->num_rows ? $query->row['path'] : false;

			$this->cache->set('category.seopath', $path);
		}

		return $path[$category_id];
	}

	private function validate() {
		// Leave some routes AS IS, pass through seo_pro.php
		$asis = array('error/not_found', 'product/live_options/js');
		if (isset($this->request->get['route']) && (
			in_array($this->request->get['route'], $asis)
			|| preg_match('~^api/~', $this->request->get['route'])    // All API requests
			))
		{
			return;
		}
		if (ltrim($this->request->server['REQUEST_URI'], '/') == 'sitemap.xml') {
			$this->request->get['route'] = 'feed/google_sitemap';
			return;
		}

		if(empty($this->request->get['route'])) {
			$this->request->get['route'] = 'common/home';
		}

		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return;
		}

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$url = str_replace('&amp;', '&', $this->config->get('config_ssl') . ltrim($this->request->server['REQUEST_URI'], '/'));
			$seo = str_replace('&amp;', '&', $this->url->link($this->request->get['route'], $this->getQueryString(array('route')), 'SSL'));
		} else {
			$url = str_replace('&amp;', '&',
				substr($this->config->get('config_url'), 0, strpos($this->config->get('config_url'), '/', 10)) // leave only domain
				. $this->request->server['REQUEST_URI']);
			$seo = str_replace('&amp;', '&', $this->url->link($this->request->get['route'], $this->getQueryString(array('route')), 'NONSSL'));
		}

		if (rawurldecode($url) != rawurldecode($seo)) {
			// header($this->request->server['SERVER_PROTOCOL'] . ' 303 See Other');
			// $this->response->redirect($seo,303);
			header($this->request->server['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
			$this->response->redirect($seo,301);
		}
	}

	private function getQueryString($exclude = array()) {
		if (!is_array($exclude)) {
			$exclude = array();
			}

		return urldecode(
			http_build_query(
				array_diff_key($this->request->get, array_flip($exclude))
				)
			);
		}
	}
