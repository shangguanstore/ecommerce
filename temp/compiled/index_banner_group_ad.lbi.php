
<?php 
$k = array (
  'name' => 'get_adv_child',
  'ad_arr' => $this->_var['index_banner_group'],
  'warehouse_id' => $this->_var['warehouse_id'],
  'area_id' => $this->_var['area_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>