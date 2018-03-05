<?php

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
	
class cls_ecmac{
	var $return_array = array(); // 返回带有MAC地址的字串数组 
	var $mac_addr;
	
	function __construct($os_type)
    {
        $this->cls_ecmac($os_type);
    }
	
	function cls_ecmac($os_type){ 
		 switch ( strtolower($os_type) ){ 
			case "linux": 
				$this->forLinux(); 
			break; 
				case "solaris":
			break; 
				case "unix":
			break; 
				case "aix": 
			break; 
				default: 
				$this->forWindows(); 
			break; 
  
		  } 
  
			 
		  $temp_array = array(); 
		  if($this->return_array){
			  foreach ( $this->return_array as $value ){ 
					if ( 
	preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i",$value, 
	$temp_array ) ){ 
						 $this->mac_addr = $temp_array[0]; 
						 break; 
				   } 
				   else
				   {
						$this->mac_addr=$os_type;   
				   }
	  
			  } 
			  unset($temp_array); 
		  }
		  
		   
	 }
	 
	 function __tostring()
	 {
		return !empty($this->mac_addr)?$this->mac_addr:'0'; 
	 }
  
  
	 function forWindows(){ 
                  @exec("ipconfig /all", $this->return_array); 
                  if ( $this->return_array ){ 
                           return $this->return_array; 
                  }else{
                        if(isset($_SERVER["WINDIR"])){
                           $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe"; 
                           if ( is_file($ipconfig) ){ 
                              @exec($ipconfig." /all", $this->return_array); 
                           }else{ 
                              @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array); 
                           }
                           return $this->return_array; 
                        }else{
                            return $this->return_array; 
                        } 
                  } 
             } 
  
  
  
	 function forLinux(){ 
	 	if($this->return_array){
		  @exec("ifconfig -a", $this->return_array); 
		  return $this->return_array; 
		}
	 } 
}
	
?>