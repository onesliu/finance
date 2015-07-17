<?php
class ModelMobileStoreExtra extends Model {

	// missing function from model product/category -- removed on 1.5.5.1
	public function getCategoriesByParentId($category_id) {
		$category_data = array();

		$category_query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$category_id . "'");

		foreach ($category_query->rows as $category) {
			$category_data[] = $category['category_id'];

			$children = $this->getCategoriesByParentId($category['category_id']);

			if ($children) {
				$category_data = array_merge($children, $category_data);
			}			
		}

		return $category_data;
	}
}
?>