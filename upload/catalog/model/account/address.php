<?php
namespace Opencart\Catalog\Model\Account;
class Address extends \Opencart\System\Engine\Model {
	public function addAddress(int $customer_id, array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "address` SET `customer_id` = '" . (int)$customer_id . "', `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "', `company` = '" . $this->db->escape((string)$data['company']) . "', `address_1` = '" . $this->db->escape((string)$data['address_1']) . "', `address_2` = '" . $this->db->escape((string)$data['address_2']) . "', `postcode` = '" . $this->db->escape((string)$data['postcode']) . "', `city` = '" . $this->db->escape((string)$data['city']) . "', `zone_id` = '" . (int)$data['zone_id'] . "', `country_id` = '" . (int)$data['country_id'] . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "'");

		$address_id = $this->db->getLastId();

		if ($data['default']) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		return $address_id;
	}

	public function editAddress(int $address_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "address` SET `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "', `company` = '" . $this->db->escape((string)$data['company']) . "', `address_1` = '" . $this->db->escape((string)$data['address_1']) . "', `address_2` = '" . $this->db->escape((string)$data['address_2']) . "', `postcode` = '" . $this->db->escape((string)$data['postcode']) . "', `city` = '" . $this->db->escape((string)$data['city']) . "', `zone_id` = '" . (int)$data['zone_id'] . "', `country_id` = '" . (int)$data['country_id'] . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "' WHERE `address_id` = '" . (int)$address_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "'");

		if ($data['default']) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
	}

	public function deleteAddress(int $address_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "address` WHERE `address_id` = '" . (int)$address_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "'");

		if ($address_id == $this->customer->getAddressId()) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '0' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
	}

	public function getAddress(int $address_id): array {
		$address_query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "address` WHERE `address_id` = '" . (int)$address_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "'");

		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE `country_id` = '" . (int)$address_query->row['country_id'] . "' AND `status` = '1'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE `zone_id` = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$address_data = [
				'address_id'     => $address_query->row['address_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'custom_field'   => json_decode($address_query->row['custom_field'], true)
			];

			return $address_data;
		} else {
			return [];
		}
	}

	public function getAddresses(): array {
		$address_data = [];

		$query = $this->db->query("SELECT `address_id` FROM `" . DB_PREFIX . "address` WHERE `customer_id` = '" . (int)$this->customer->getId() . "'");

		foreach ($query->rows as $result) {
			$address_info = $this->getAddress($result['address_id']);

			if ($address_info) {
				$address_data[$result['address_id']] = $address_info;
			}
		}

		return $address_data;
	}

	public function getTotalAddresses(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "address` WHERE `customer_id` = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
