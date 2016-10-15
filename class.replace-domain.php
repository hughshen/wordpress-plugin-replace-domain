<?php

class ReplaceDomain
{
	private static $initiated = false;
	private static $class_name = __CLASS__;
	private static $allow_tables = ['posts', 'options'];

	public static function init()
	{
		if (!self::$initiated) {
			self::init_hooks();
		}
	}

	public static function init_hooks()
	{
		add_action('admin_menu', array(self::$class_name, 'add_submenu'));
		add_action('admin_head', array(self::$class_name, 'add_main_css'));
		self::$initiated = true;
	}

	public static function add_submenu()
	{
		add_submenu_page('tools.php', 'Replace Domain', 'Replace Domain', 'import', 'replace-domain', array(self::$class_name, 'submenu_func'));
	}

	public static function submenu_func()
	{
		include_once(REPLACE_DOMAIN_DIR. 'form.replace-domain.php');
	}

	public static function do_replace_domain_func()
	{
		$message = '';
		$old_url = isset($_POST['old_url']) ? $_POST['old_url'] : '';
		$new_url = isset($_POST['new_url']) ? $_POST['new_url'] : '';
		$replace_table = isset($_POST['replace_table']) ? $_POST['replace_table'] : '';
		if (empty($old_url)) {
			$message .= 'Old url is missing.<br>';
		}
		if (empty($new_url)) {
			$message .= 'New url is missing.<br>';
		}
		if (!in_array($replace_table, self::$allow_tables)) {
			$message .= 'Selected table is not allow.<br>';
		}

		if (empty($message)) {
			if (self::update_table($replace_table, $old_url, $new_url)) {
				$message .= 'Replace domain success.';
			} else {
				$message .= 'Replace domain fail.';
			}
		}
		
		return $message;
	}

	private static function update_table($table, $search, $replace)
	{
		global $wpdb;
		$queries = [];
		switch ($table) {
			case 'posts':
				$queries[] = 'UPDATE `' . $wpdb->prefix . $table . '` SET `post_content` = REPLACE(`post_content`, "' . $search . '", "' . $replace . '") WHERE `post_content` LIKE "%' . $search . '%";';
				$queries[] = 'UPDATE `' . $wpdb->prefix . $table . '` SET `guid` = REPLACE(`guid`, "' . $search . '", "' . $replace . '") WHERE `guid` LIKE "%' . $search . '%";';
				break;
			case 'options':
				$options_select_query = 'SELECT * FROM `' . $wpdb->prefix . $table . '` WHERE `option_value` LIKE "%' . $search . '%"';
				$options_select_result = $wpdb->get_results($options_select_query, ARRAY_A);
				foreach ($options_select_result as $option) {
					if ($option['option_name'] == 'siteurl' || $option['option_name'] == 'home') continue;
					if (@unserialize($option['option_value']) !== false) {
						$new_option_value = serialize(self::replace_array(@unserialize($option['option_value']), $search, $replace));
					} else {
						$new_option_value = str_replace($search, $replace, $option['option_value']);
					}
					$queries[] = 'UPDATE `' . $wpdb->prefix . $table . '` SET `option_value` = \'' . $new_option_value . '\' WHERE `option_id` = ' . $option['option_id'] . ';';
				}
				break;
		}
		// start to commit query
		if (!empty($queries)) {
			$wpdb->query('START TRANSACTION');
			$result = true;
			foreach ($queries as $query) {
				if ($wpdb->query($query) === false) $result = false;
			}
			if ($result !== false) {
				$wpdb->query('COMMIT');
				return true;
			} else {
				$wpdb->query('ROLLBACK');
			}
		}
		return false;
	}

	private static function replace_array($array, $search, $replace, $replace_key = false)
	{
		if (!is_array($array)) return [];

		foreach ($array as $key => $val) {
			if (is_array($val)) {
				if (count($val) > 0) {
					$array[$key] = self::replace_array($val, $search, $replace, $replace_key);
				}
			} else {
				if (is_numeric($search) && is_numeric($replace)) {
					if (is_float($val)) {
						$array[$key] = (float)str_replace($search, $replace, $val);
					} elseif (is_int($val)) {
						$array[$key] = (int)str_replace($search, $replace, $val);
					} else {
						$array[$key] = str_replace($search, $replace, $val);
					}
				} else {
					$array[$key] = str_replace($search, $replace, $val);
				}
			}

			if ($replace_key) {
				$new_key = str_replace($search, $replace, $key);
				if ($key !== $new_key) {
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
		return $array;
	}

	public static function add_main_css()
	{
		echo '';
	}

	public static function plugin_activation() {}

	public static function plugin_deactivation() {}
}