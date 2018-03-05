
<?php 
$k = array (
  'name' => 'get_adv_child',
  'ad_arr' => $this->_var['index_group_banner'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>