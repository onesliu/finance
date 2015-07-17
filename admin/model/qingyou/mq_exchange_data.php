<?php
class ModelQingyouMqExchangeData extends Model
{
    public function uploadData($type)
    {
        if ( $type == 1 )   // upload goods info
        {
            $contents = file_get_contents("php://input");

            //$sql = iconv("gbk","UTF-8//IGNORE", $sql); //Both methods can be used.
            $query = $this->db->query("set names gbk");
            $sql = "INSERT INTO pos_exchange_data SET datatype = ".$type.", dataval = '".$contents."'";
            $query = $this->db->query($sql);

            /* Update new goods info to database */
            $arr_line = explode("\r\n", $contents);
            foreach ( $arr_line as $value )
            {
                $value = trim($value);
                if ( empty($value) )
                    continue;

                $this->log->write($value);
                $arr_goodsproperty = explode("|", $value);

                $goods_type_id          = trim($arr_goodsproperty[0]);
                $goods_type_parentid    = trim($arr_goodsproperty[1]);
                $goods_type_name        = trim($arr_goodsproperty[2]);
                $goods_barcode          = trim($arr_goodsproperty[3]);
                $goods_name             = trim($arr_goodsproperty[4]);
                $goods_code             = trim($arr_goodsproperty[5]);
                $goods_number           = trim($arr_goodsproperty[6]);
                $goods_cost             = trim($arr_goodsproperty[7]);
                $goods_labelprice       = trim($arr_goodsproperty[8]);
                $goods_lowestprice      = trim($arr_goodsproperty[9]);
                $goods_desp             = trim($arr_goodsproperty[10]);
                $goods_goodtype         = trim($arr_goodsproperty[11]);

                // oc_product
                $sql = "SELECT * FROM oc_product WHERE ean = '".$goods_barcode."'";
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                    $sql = "UPDATE oc_product SET product_type = ".$goods_goodtype.
                                          " WHERE          ean = '".$goods_barcode."'";
                    $query = $this->db->query($sql);

                    $product_id = $query->row['product_id'];
                }
                else
                {
                    $sql = "INSERT INTO oc_product SET ean = '".$goods_barcode.
                                                       "', price = ".$goods_labelprice.
                                                       ", product_type = ".$goods_goodtype;
                    $query = $this->db->query($sql);

                    $product_id = $this->db->getLastId();
                }

                // oc_product_description
                $sql = "SELECT * FROM oc_product_description WHERE product_id = ".$product_id;
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                    $sql = "UPDATE oc_product_description SET name = '".$goods_name.
                                                              "', description = '".$goods_desp.
                                                     "' WHERE product_id = ".$product_id;
                    $query = $this->db->query($sql);
                }
                else
                {
                    $sql = "INSERT INTO oc_product_description SET product_id = ".$product_id.
                                                                   ", name = '".$goods_name.
                                                                   "', description = '".$goods_desp."'";
                    $query = $this->db->query($sql);
                }

                // oc_product_to_category
                $sql = "SELECT * FROM oc_product_to_category WHERE product_id = ".$product_id;
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                    $sql = "UPDATE oc_product_to_category SET category_id = ".$goods_type_id.
                                                      " WHERE  product_id = ".$product_id;
                    $query = $this->db->query($sql);
                }
                else
                {
                    $sql = "INSERT INTO oc_product_to_category SET product_id = ".$product_id.
                                                                   ", category_id = ".$goods_type_id;
                    $query = $this->db->query($sql);
                }

                // oc_category
                $sql = "SELECT * FROM oc_category WHERE category_id = ".$goods_type_id;
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                    $sql = "UPDATE oc_category SET parent_id = ".$goods_type_parentid.
                                           " WHERE category_id = ".$goods_type_id;
                    $query = $this->db->query($sql);
                }
                else
                {
                    $sql = "INSERT INTO oc_category SET category_id = ".$goods_type_id.
                                                        ", parent_id = ".$goods_type_parentid;
                    $query = $this->db->query($sql);
                }

                // oc_category_description
                $sql = "SELECT * FROM oc_category_description WHERE category_id = ".$goods_type_id;
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                    $sql = "UPDATE oc_category_description SET name = '".$goods_type_name.
                                                      "' WHERE category_id = ".$goods_type_id;
                    $query = $this->db->query($sql);
                }
                else
                {
                    $sql = "INSERT INTO oc_category_description SET category_id = ".$goods_type_id.
                                                                    ", name = '".$goods_type_name."'";
                    $query = $this->db->query($sql);
                }

                // oc_category_to_store
                $sql = "SELECT * FROM oc_category_to_store WHERE category_id = ".$goods_type_id;
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                     ;//
                }
                else
                {
                    $sql = "INSERT INTO oc_category_to_store SET category_id = ".$goods_type_id.
                                                                 ", store_id = 0";
                    $query = $this->db->query($sql);
                }

                // oc_category_to_store
                $sql = "SELECT * FROM oc_category_to_store WHERE category_id = ".$goods_type_id;
                $query = $this->db->query($sql);
                if ( $query->num_rows != 0 )
                {
                     ;//
                }
                else
                {
                    $sql = "INSERT INTO oc_category_to_store SET category_id = ".$goods_type_id.
                                                                 ", store_id = 0";
                    $query = $this->db->query($sql);
                }
            }
        }
        else if ( $type == 2 )  // upload change price list
        {
            $contents = file_get_contents("php://input");

            //$sql = iconv("gbk","UTF-8//IGNORE", $sql); //Both methods can be used.
            $query = $this->db->query("set names gbk");
            $sql = "INSERT INTO pos_exchange_data SET datatype = ".$type.", dataval = '".$contents."'";
            $query = $this->db->query($sql);

            /* Update price to database */
            $arr_line = explode("\r\n", $contents);
            foreach ( $arr_line as $value )
            {
                $value = trim($value);
                if ( empty($value) )
                    continue;

                $arr_goodsproperty = explode("|", $value);
                if ( count($arr_goodsproperty) == 6 ) //调价单的第一行
                    continue;
                else
                {
                    $sql = "SELECT * FROM oc_product WHERE ean = ".trim($arr_goodsproperty[2]);
                    $query = $this->db->query($sql);
                    if ( $query->num_rows != 0 )
                    {
                        $sql = "UPDATE oc_product SET price = ".trim($arr_goodsproperty[4]);
                        $query = $this->db->query($sql);
                    }
                }
            }
        }
        else if ( $type == 3 )
        {
        }
        else if ( $type == 4 )
        {
        }
        else if ( $type == 5 )
        {
        }
    }

    public function downloadData($shopNo, $type)
    {
        if ( $type == 1 || // download goods info
             $type == 2 )  // download change price list
        {
            $sql = "set names gbk";
            $query = $this->db->query($sql);
            $sql = "SELECT * FROM pos_exchange_data WHERE datatype = ".$type;
            $query = $this->db->query($sql);

            $contents = "";
            foreach ($query->rows as $row)
            {
                $dataID = $row['id'];
                $sql = "SELECT * FROM pos_exchange_store WHERE storeid = ".$shopNo." AND dataid = ".$dataID;
                $query1 = $this->db->query($sql);
                if ($query1->num_rows != 0)
                    continue;
                else
                {
                    $contents .= $row['dataval']."\r\n";
                    $sql = "INSERT INTO pos_exchange_store SET storeid = ".$shopNo.", dataid = ".$dataID;
                    $query1 = $this->db->query($sql);
                }
            }
            return $contents;
        }
        else if ( $type == 3 )
        {
        }
        else if ( $type == 4 )
        {
        }
        else if ( $type == 5 )
        {
        }
    }
}