<?php
class ModelQingyouMqBalance extends Model
{
    public function balance()
    {
        $sql = "set names gbk";
        $query = $this->db->query($sql);

        $sql = "SELECT COUNT(*) AS count, SUM(total) AS total FROM oc_order WHERE order_status_id = 4 AND balance = 0";
        $query = $this->db->query($sql);
        $count = $query->row['count'];
        $total = $query->row['total'];

//      $sql = "UPDATE oc_order SET balance = 1 WHERE order_status_id = 4 AND balance = 0";
//      $query = $this->db->query($sql);

        $sql = "SELECT * FROM qy_balance";
        $query = $this->db->query($sql);
        if ( $query->num_rows == 0 )
        {
            $last_date = "Not Balanced";
        }
        else
        {
            // Get date of last balance
            $sql = "SELECT MAX(id) AS max_id FROM qy_balance";
            $query = $this->db->query($sql);
            $max_id = $query->row['max_id'];

            $sql = "SELECT * FROM qy_balance WHERE id = ".$max_id;
            $query = $this->db->query($sql);
            $last_date = $query->row['last_balance_date'];
        }

        $user = $this->user->getUserInfo();
        $districtid = $user['district_id'];
        $sql = "INSERT INTO qy_balance SET shop_id = ".$districtid;
        $query = $this->db->query($sql);
        $curr_id = $this->db->getLastId();
        $sql = "SELECT * FROM qy_balance WHERE id = ".$curr_id;
        $query = $this->db->query($sql);
        $curr_date = $query->row['last_balance_date'];

        $contents = $last_date."\r\n".$curr_date."\r\n".$total."\r\n".$count;

        return $contents;
    }
}