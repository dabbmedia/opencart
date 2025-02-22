<?php
namespace Opencart\Catalog\Controller\Extension\Opencart\Total;
class Reward extends \Opencart\System\Engine\Controller {
	public function index(): string {
		if ($this->config->get('total_reward_status')) {
			$points = $this->customer->getRewardPoints();

			$points_total = 0;

			foreach ($this->cart->getProducts() as $product) {
				if ($product['points']) {
					$points_total += $product['points'];
				}
			}

			if ($points && $points_total) {
				$this->load->language('extension/opencart/total/reward');

				$data['heading_title'] = sprintf($this->language->get('heading_title'), $points);

				$data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);

				$data['save'] = $this->url->link('extension/opencart/total/reward|save', 'language=' . $this->config->get('config_language'), true);

				if (isset($this->session->data['reward'])) {
					$data['reward'] = $this->session->data['reward'];
				} else {
					$data['reward'] = '';
				}

				return $this->load->view('extension/opencart/total/reward', $data);
			}
		}

		return '';
	}

	public function save(): void {
		$this->load->language('extension/opencart/total/reward');

		$json = [];

		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if (!$this->config->get('total_status')) {
			$json['error'] = $this->language->get('error_reward');
		}

		if (!empty($this->request->post['reward'])) {
			$json['error'] = $this->language->get('error_reward');
		}

		if ($this->request->post['reward'] > $points) {
			$json['error'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
		}

		if ($this->request->post['reward'] > $points_total) {
			$json['error'] = sprintf($this->language->get('error_maximum'), $points_total);
		}

		if (!$json) {
			$this->session->data['reward'] = abs($this->request->post['reward']);

			$this->session->data['success'] = $this->language->get('text_success');

			$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
