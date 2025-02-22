<?php
namespace Opencart\Catalog\Controller\Checkout;
class Register extends \Opencart\System\Engine\Controller {
	public function index(): string {
		$this->load->language('checkout/checkout');

		$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', 'language=' . $this->config->get('config_language') . '&redirect=' . urlencode($this->url->link('account/login', 'language=' . $this->config->get('config_language'), true))));

		$data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));

		$data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));

		$data['config_checkout_address'] = $this->config->get('config_checkout_address');
		$data['config_checkout_guest'] = ($this->config->get('config_checkout_guest') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		$data['config_file_max_size'] = $this->config->get('config_file_max_size');

		$data['language'] = $this->config->get('config_language');
		$data['shipping_required'] = $this->cart->hasShipping();

		$data['customer_groups'] = [];

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups  as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = 1;
		}

		if (isset($this->session->data['customer'])) {
			$data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
			$data['firstname'] = $this->session->data['customer']['firstname'];
			$data['lastname'] = $this->session->data['customer']['lastname'];
			$data['email'] = $this->session->data['customer']['email'];
			$data['telephone'] = $this->session->data['customer']['telephone'];
			$data['account_custom_field'] = $this->session->data['customer']['custom_field'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			$data['firstname'] = '';
			$data['lastname'] = '';
			$data['email'] = '';
			$data['telephone'] = '';
			$data['account_custom_field'] = [];
		}

		if (isset($this->session->data['payment_address'])) {
			$data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
			$data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
			$data['payment_company'] = $this->session->data['payment_address']['company'];
			$data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
			$data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
			$data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
			$data['payment_city'] = $this->session->data['payment_address']['city'];
			$data['payment_country_id'] = (int)$this->session->data['payment_address']['country_id'];
			$data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
			$data['payment_custom_field'] = $this->session->data['payment_address']['custom_field'];
		} else {
			$data['payment_firstname'] = '';
			$data['payment_lastname'] = '';
			$data['payment_company'] = '';
			$data['payment_address_1'] = '';
			$data['payment_address_2'] = '';
			$data['payment_postcode'] = '';
			$data['payment_city'] = '';
			$data['payment_country_id'] = $this->config->get('config_country_id');
			$data['payment_zone_id'] = '';
			$data['payment_custom_field'] = [];
		}

		if (isset($this->session->data['shipping_address'])) {
			$data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
			$data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
			$data['shipping_company'] = $this->session->data['shipping_address']['company'];
			$data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
			$data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
			$data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
			$data['shipping_city'] = $this->session->data['shipping_address']['city'];
			$data['shipping_country_id'] = (int)$this->session->data['shipping_address']['country_id'];
			$data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
			$data['shipping_custom_field'] = $this->session->data['shipping_address']['custom_field'];
		} else {
			$data['shipping_firstname'] = '';
			$data['shipping_lastname'] = '';
			$data['shipping_company'] = '';
			$data['shipping_address_1'] = '';
			$data['shipping_address_2'] = '';
			$data['shipping_postcode'] = '';
			$data['shipping_city'] = '';
			$data['shipping_country_id'] = $this->config->get('config_country_id');
			$data['shipping_zone_id'] = '';
			$data['shipping_custom_field'] = [];
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

		// Captcha
		$this->load->model('setting/extension');

		$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

		if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/'  . $extension_info['extension'] . '/captcha/' . $extension_info['code']);
		} else {
			$data['captcha'] = '';
		}

		$this->load->model('catalog/information');

		$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

		if ($information_info) {
			$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information|info', 'language=' . $this->config->get('config_language') . '&information_id=' . $this->config->get('config_account_id')), $information_info['title']);
		} else {
			$data['text_agree'] = '';
		}

		return $this->load->view('checkout/register', $data);
	}

	public function save(): void {
		$this->load->language('checkout/checkout');

		$json = [];

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);

				break;
			}
		}

		if (!$json) {
			$keys = [
				'account',
				'customer_group_id',
				'firstname',
				'lastname',
				'email',
				'telephone',
				'payment_company',
				'payment_address_1',
				'payment_address_2',
				'payment_city',
				'payment_postcode',
				'payment_country_id',
				'payment_zone_id',
				'payment_custom_field',
				'address_match',
				'shipping_firstname',
				'shipping_lastname',
				'shipping_company',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_postcode',
				'shipping_country_id',
				'shipping_zone_id',
				'shipping_custom_field',
				'password',
				'agree'
			];

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}



			if ($this->customer->isLogged() && $this->request->post['account']) {
				$json['error']['warning'] = $this->language->get('error_account');
			}

			// Customer Group
			if ($this->request->post['customer_group_id']) {
				$customer_group_id = (int)$this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			if ($customer_group_info) {
				if ($this->request->post['account'] == 'guest' && $customer_group_info['approval']) {
					$json['error']['warning'] = $this->language->get('error_customer_approval');
				}

				if (!in_array($customer_group_id, (array)$this->config->get('config_customer_group_display'))) {
					$json['error']['warning'] = $this->language->get('error_customer_group');
				}
			} else {
				$json['error']['warning'] = $this->language->get('error_customer_group');
			}


			// If not guest checkout disabled, login require price or cart has downloads
			if ($this->request->post['account'] == 'guest' && (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload())) {
				$json['error']['warning'] = $this->language->get('error_guest');
			}





			if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			// Register
			$this->load->model('account/customer');

			if ($this->request->post['account'] == 'register' && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}

			// Logged
			if ($this->customer->isLogged()) {

				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

				if ($customer_info['customer_id'] != $this->customer->getId()) {
					$json['error']['warning'] = $this->language->get('error_exists');
				}

			}





			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account') {
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
					}
				}
			}





			if ($this->config->get('config_checkout_address')) {
				if ((utf8_strlen($this->request->post['payment_address_1']) < 3) || (utf8_strlen($this->request->post['payment_address_1']) > 128)) {
					$json['error']['payment_address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($this->request->post['payment_city']) < 2) || (utf8_strlen($this->request->post['payment_city']) > 32)) {
					$json['error']['payment_city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry((int)$this->request->post['payment_country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['payment_postcode']) < 2 || utf8_strlen($this->request->post['payment_postcode']) > 10)) {
					$json['error']['payment_postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['payment_country_id'] == '') {
					$json['error']['payment_country'] = $this->language->get('error_country');
				}

				if ($this->request->post['payment_zone_id'] == '') {
					$json['error']['payment_zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['payment_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['payment_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['payment_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['payment_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
						}
					}
				}
			}





			if ($this->cart->hasShipping() && !$this->request->post['address_match']) {


				// If payment address not required we need to use the firstname and lastname from the account.
				if (!$this->config->get('config_checkout_address')) {
					if ((utf8_strlen($this->request->post['shipping_firstname']) < 1) || (utf8_strlen($this->request->post['shipping_firstname']) > 32)) {
						$json['error']['shipping_firstname'] = $this->language->get('error_firstname');
					}

					if ((utf8_strlen($this->request->post['shipping_lastname']) < 1) || (utf8_strlen($this->request->post['shipping_lastname']) > 32)) {
						$json['error']['shipping_lastname'] = $this->language->get('error_lastname');
					}
				}


				if ((utf8_strlen($this->request->post['shipping_address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address_1']) > 128)) {
					$json['error']['shipping_address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($this->request->post['shipping_city']) < 2) || (utf8_strlen($this->request->post['shipping_city']) > 128)) {
					$json['error']['shipping_city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry((int)$this->request->post['shipping_country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['shipping_postcode']) < 2 || utf8_strlen($this->request->post['shipping_postcode']) > 10)) {
					$json['error']['shipping_postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['shipping_country_id'] == '') {
					$json['error']['shipping_country'] = $this->language->get('error_country');
				}

				if ($this->request->post['shipping_zone_id'] == '') {
					$json['error']['shipping_zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['shipping_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['shipping_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
						}
					}
				}
			}





			// Register
			if ($this->request->post['account'] == 'register' && (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$json['error']['password'] = $this->language->get('error_password');
			}




			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !$this->request->post['agree']) {
				$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}

			// Captcha
			$this->load->model('setting/extension');

			$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

			if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/'  . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '|validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
				}
			}
		}




		if (!$json) {




			if ($this->request->post['account'] == 'register') {



				$customer_id = $this->model_account_customer->addCustomer($this->request->post);

				if ($this->config->get('config_checkout_address')) {
					$payment_address_data = [
						'firstname'    => $this->request->post['payment_firstname'],
						'lastname'     => $this->request->post['payment_lastname'],
						'company'      => $this->request->post['payment_company'],
						'address_1'    => $this->request->post['payment_address_1'],
						'address_2'    => $this->request->post['payment_address_2'],
						'city'         => $this->request->post['payment_city'],
						'postcode'     => $this->request->post['payment_postcode'],
						'country_id'   => $this->request->post['payment_country_id'],
						'zone_id'      => $this->request->post['payment_zone_id'],
						'custom_field' => isset($this->request->post['payment_custom_field']) ? $this->request->post['payment_custom_field'] : []
					];

					$this->load->model('account/address');

					$payment_address_id = $this->model_account_address->addAddress($customer_id, $payment_address_data);
				} else {
					$payment_address_id = 0;
				}

				if ($this->cart->hasShipping() && !$this->request->post['address_match']) {
					$shipping_address_data = [
						'firstname'    => $this->request->post['shipping_firstname'],
						'lastname'     => $this->request->post['shipping_lastname'],
						'company'      => $this->request->post['shipping_company'],
						'address_1'    => $this->request->post['shipping_address_1'],
						'address_2'    => $this->request->post['shipping_address_2'],
						'city'         => $this->request->post['shipping_city'],
						'postcode'     => $this->request->post['shipping_postcode'],
						'country_id'   => $this->request->post['shipping_country_id'],
						'zone_id'      => $this->request->post['shipping_zone_id'],
						'custom_field' => isset($this->request->post['shipping_custom_field']) ? $this->request->post['shipping_custom_field'] : []
					];

					$shipping_address_id = $this->model_account_address->addAddress($customer_id, $shipping_address_data);
				} else {
					$shipping_address_id = 0;
				}


				$customer_id = $this->customer->getId();

				$this->model_account_customer->editCustomer($customer_id, $this->request->post);

			}


			if (!$customer_group_info['approval']) {
				// If everything good login
				$this->customer->login($this->request->post['email'], $this->request->post['password']);

			}


			// Check if current customer group requires approval
			if (!$customer_group_info['approval']) {



				// Add customer details into session
				$this->session->data['customer'] = [
					'customer_id'       => $customer_id,
					'customer_group_id' => $customer_group_id,
					'firstname'         => $this->request->post['firstname'],
					'lastname'          => $this->request->post['lastname'],
					'email'             => $this->request->post['email'],
					'telephone'         => $this->request->post['telephone'],
					'custom_field'      => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : []
				];

				if ($this->config->get('config_checkout_address')) {
					$this->session->data['payment_address'] = [
						'address_id'   => $payment_address_id,
						'firstname'    => $this->request->post['payment_firstname'],
						'lastname'     => $this->request->post['payment_lastname'],
						'company'      => $this->request->post['payment_company'],
						'address_1'    => $this->request->post['payment_address_1'],
						'address_2'    => $this->request->post['payment_address_2'],
						'city'         => $this->request->post['payment_city'],
						'postcode'     => $this->request->post['payment_postcode'],
						'country_id'   => $this->request->post['payment_country_id'],
						'zone_id'      => $this->request->post['payment_zone_id'],
						'custom_field' => isset($this->request->post['payment_custom_field']) ? $this->request->post['payment_custom_field'] : []
					];
				}

				// If shipping address the same
				if ($this->cart->hasShipping() && $this->request->post['address_match']) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];
				} else {
					$this->session->data['shipping_address'] = [
						'address_id'   => $shipping_address_id,
						'firstname'    => $this->request->post['shipping_firstname'],
						'lastname'     => $this->request->post['shipping_lastname'],
						'company'      => $this->request->post['shipping_company'],
						'address_1'    => $this->request->post['shipping_address_1'],
						'address_2'    => $this->request->post['shipping_address_2'],
						'city'         => $this->request->post['shipping_city'],
						'postcode'     => $this->request->post['shipping_postcode'],
						'country_id'   => $this->request->post['shipping_country_id'],
						'zone_id'      => $this->request->post['shipping_zone_id'],
						'custom_field' => isset($this->request->post['shipping_custom_field']) ? $this->request->post['shipping_custom_field'] : []
					];
				}






				$json['success'] = 'Success: Your account has been successfully created!';
			} else {
				// If account needs approval we redirect to the account success / requires approval page.
				$json['redirect'] =  $this->url->link('account/success', 'language=' . $this->config->get('config_language'), true);
			}

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
