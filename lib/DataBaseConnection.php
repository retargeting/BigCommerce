<?php
//namespace Retargeting\Lib;

class DBConn {
	
	// ! add mysqli_real_escape to all params

	protected $db = null;

	public function __construct($host, $user, $pass, $db) {

		$this->db = mysqli_connect($host, $user, $pass, $db);

		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			return false;
		}

		return true;
	}

	public function saveShop($accessToken, $scope, $id, $email, $context, $domain) {
		
		$sql = "INSERT INTO shops (access_token, scope, id, email, context, domain) VALUES ('".$accessToken."', '".$scope."', '".$id."', '".$email."', '".$context."', '".$domain."') ON DUPLICATE KEY UPDATE access_token='".$accessToken."', scope='".$scope."', email='".$email."', context='".$context."', domain='".$domain."'";

		if ($this->db->query($sql) === TRUE) {
			
			$sql = "INSERT INTO configurations (user_id) VALUES ('".$id."') ON DUPLICATE KEY UPDATE user_id='".$id."'";
			
			if ($this->db->query($sql) === TRUE) {
				
				return $id;
			}	
		}

		return false;
	}

	public function deleteShop($shopId) {
		
		$sql = "DELETE FROM shops WHERE id = ".(int)$shopId;

		return $this->db->query($sql);
	}

	public function deleteTokens($shopId) {
		
		$sql = "DELETE FROM access_tokens WHERE id = ".(int)$shopId;

		return $this->db->query($sql);
	}

	public function deleteConfig($shopId) {
		
		$sql = "DELETE FROM configurations WHERE shop_id = ".(int)$shopId;

		return $this->db->query($sql);
	}

	public function updateShop($shopId, $appVer) {
		
		$sql = "UPDATE shops set version = '".$appVer."' WHERE id = ".(int)$shopId;

		return $this->db->query($sql);
	}

	public function getShopId($id, $email, $storeHash) {
		
		$sql = "SELECT id FROM shops WHERE id='".$id."' AND email='".$email."' AND (context='".$storeHash."' || context='stores/".$storeHash."') LIMIT 1";

		$res = $this->db->query($sql);

		if ($res->num_rows > 0) {
			$row = $res->fetch_assoc();
			return $row['id'];
		} else {
			return false;
		}
	}

	public function getShopIdByUrl($shop) {
		
		$sql = "SELECT id FROM shops WHERE domain='".$shop."' OR domain='http://".$shop."' OR domain='https://".$shop."' LIMIT 1";

		$res = $this->db->query($sql);

		if ($res->num_rows > 0) {
			$row = $res->fetch_assoc();
			return $row['id'];
		} else {
			return false;
		}
	}

	public function getShopConfig($shopId) {
		
		$sql = "SELECT status, init, domain_api_key, discounts_api_key, qs_add_to_cart, help_pages FROM configurations WHERE user_id='".$shopId."' ORDER BY id DESC LIMIT 1";
		
		$res = $this->db->query($sql);

		if ($res->num_rows > 0) {
			$row = $res->fetch_assoc();
			return $row;
		} else {
			return false;
		}
	}

	public function getShopData($shopId) {
		
		$sql = "SELECT email, context FROM shops WHERE id='".$shopId."' LIMIT 1";
		
		$res = $this->db->query($sql);

		if ($res->num_rows > 0) {
			$row = $res->fetch_assoc();
			return $row;
		} else {
			return false;
		}
	}

	public function getShopTokens($shopId) {
		
		$sql = "SELECT * FROM shops WHERE id='".$shopId."' LIMIT 1";
		
		$res = $this->db->query($sql);

		if ($res->num_rows > 0) {
			$row = $res->fetch_assoc();
			
			$tokens = new stdClass();
			$tokens->access_token = $row['access_token'];
			$tokens->context = $row['context'];
			
			return $tokens;
		} else {
			return false;
		}
	}

	public function updateShopConfig($shopId, $status, $domainApiKey, $discountsApiKey, $helpPages, $qsAddToCart) {
		
		if ($status) {
			$changeStatusSQL = '';
		}
		
		$sql = "UPDATE configurations set ".($status ? 'status = NOT status, ' : '')."domain_api_key = '".$domainApiKey."', discounts_api_key = '".$discountsApiKey."', help_pages = '".$helpPages."', qs_add_to_cart = '".$qsAddToCart."' WHERE user_id = ".(int)$shopId;
		
		return $this->db->query($sql);
	}

	public function disableInit($shopId) {
		$sql = "UPDATE configurations set init = true WHERE user_id = ".(int)$shopId;

		return $this->db->query($sql);
	}
}