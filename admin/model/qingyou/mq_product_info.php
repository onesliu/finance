<?php
class ModelQingyouMqProductInfo extends Model
{
    public function getProductInfo($hasedit = true)
    {
    	$condition = "";
    	if ($hasedit) {
    		$condition = "and hasedit=1";
    	}
        $data = array();
        $sql = "select p.isbn as plu_serial_no, p.ean, p.price, p.product_type, pd.name
        	from oc_product as p left join oc_product_description as pd on p.product_id = pd.product_id
        	where p.isbn <> '' and p.isbn is not null $condition order by p.ean";

        $query = $this->db->query($sql);
        foreach ($query->rows as $result) {
            $o = new stdClass();
            foreach($result as $key => $val) {
                $o->$key = $val;
            }

            $data[] = $o;
        }

        $sql = "UPDATE oc_product SET hasedit = 0";
        $query = $this->db->query($sql);

        return $data;
    }
}