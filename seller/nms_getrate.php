<?php

define('IN_ECS', true);
@set_time_limit(300);
require dirname(__FILE__) . '/includes/init.php';
require_once ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php';
include_once ROOT_PATH . '/includes/cls_image.php';
$image = new cls_image($_CFG['bgcolor']);
$goods_id = intval($_REQUEST['goods_id']);
$goods_name = trim($_REQUEST['goods_name']);
$istitle = intval($_GET['istitle']);
$cnum = intval($_GET['cnum']);
if ($_REQUEST['act'] == 'rate_set') {
	//	gongneng($adminprv, "D");
	include_once ROOT_PATH . 'includes/fckeditor/fckeditor.php';
	// 包含 html editor 类文件
	/* 创建 html editor */
	create_html_editor_ec('content_web');
	$session_set = $_SESSION['nms_colect_setting'][$class]['D'];
	$user_setting = unserialize($session_set);
	if (!$user_setting['conum']) {
		$user_setting['conum'] = 10;
	}
	$smarty->assign('user_setting', $user_setting);
	//
	//	$good_type_list = get_goodstype();
	//	$goods_type_id = $GLOBALS['db']->getOne("SELECT cat_id FROM " . $GLOBALS['ecs']->table('goods_type')  . " WHERE cat_name = '采集属性'");
	//	$goods_type_id = $user_setting['goods_type_id'] ? $user_setting['goods_type_id']:$goods_type_id;
	//    $smarty->assign('goods_type_arr',   $good_type_list);
	//    $smarty->assign('goods_type_id',   $goods_type_id);
	//	if (ADMIN_PATH == 'supplier')
	//		$smarty->assign('seller_cat_list',     sel_cat_list(0));
	//	$smarty->assign('is_sup',   ADMIN_PATH);
	$smarty->assign('goods_id', $goods_id);
	$smarty->assign('goods_name', $goods_name);
	$smarty->assign('ur_here', "评论采集");
	$smarty->assign('full_page', 1);
	$smarty->display('nms_getrate.dwt');
} elseif ($_REQUEST['act'] == 'getrate') {
	$goods_id = intval($_REQUEST['goods_id']);
	$keyword = trim($_REQUEST['goods_name']);
	$content = trim($_REQUEST['content']);
	$content_web = trim($_REQUEST['content_web']);
	$col_type = (int) $_REQUEST['col_type'];
	$is_order = intval($_REQUEST['is_order']);
	if (!$content and $col_type == 1) {
		echo "<script>alert('请输入店铺链接！');history.go(-1);</script>";
		die;
	}
	if (!$content_web and $col_type == 2) {
		echo "<script>alert('请输入页面内容！');history.go(-1);</script>";
		die;
	}
	$stime = time() + microtime();
	$data['itemlist'] = $_REQUEST['itemlist'];
	$itemlist = $data['itemlist'];
	if (!$data['itemlist']) {
		flush_echo_nms(pr_heads());
		//打印网页头
		showjsmessage('正在准备采集....');
	}
	$data['content'] = $content;
	$data['keyword'] = $keyword;
	$data['is_order'] = $is_order;
	$data['conum'] = (int) $_REQUEST['conum'];
	$conum = (int) $_REQUEST['conum'];
	//记录用户输入
	save_user_setting($data, $class, 'D');
	if ($col_type == 1) {
		$keyword = str_replace("_id", "", $content);
		if (preg_match('/id=(\d{8}\d+)/', $keyword, $r)) {
			$num_iid = $r[1];
			$ratenum = GetRate($num_iid, $goods_id, $conum, $is_order, $data['itemlist']);
			$pass_time = cj_timer($stime);
			showjsmessage('<br><font class="cj_over"><strong>[' . $ratenum . ']</strong>条评论采集完成！</font>(用时：' . $pass_time . '秒)<br>');
			showjsmessage('进入<a href="comment_manage.php?act=list">评论列表</a>', 1);
			die;
		}
	}
	if ($col_type == 3) {
	} else {
		$result = stripslashes(mb_convert_encoding($content_web, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));
		//pr($result,1);
		$result = preg_replace("/(\r\n|\n)/", "", $result);
		//pr($result,1);
		preg_match_all('/<li\sid="review(.+?)<\/div>/', $result, $res);
		preg_match_all('/tb-tbcr-content(.+?)<\/div>/', $result, $res2);
		preg_match_all('/<span\sclass="tb-r-date"(.+?)<\/span>/', $result, $res3);
		//pr($res2[0],1);
		foreach ((array) $res[0] as $value) {
			$xxx = preg_replace("/<(.+?)>/", "", $value);
			$xxx = trim($xxx);
			$users[] = str_replace("(匿名)", "", $xxx);
		}
		foreach ((array) $res2[0] as $value) {
			$xxx = str_replace("tb-tbcr-content", "<", $value);
			$xxx = preg_replace("/<(.+?)>/", "", $xxx);
			$xxx = trim($xxx);
			$rate[] = str_replace("'", "’", $xxx);
		}
		foreach ((array) $res3[0] as $value) {
			$xxx = preg_replace("/<(.+?)>/", "", $value);
			$r_data[] = trim($xxx);
		}
		//pr($users);
		//pr($rate);
		//pr($r_data,1);
		$flag = 0;
		foreach ((array) $rate as $ii => $value) {
			if ((int) $flag == (int) $conum) {
				break;
			}
			//pr($value,1);
			$content = $value;
			$user_name = $users[$ii];
			$rdate = str_replace('年', '-', $r_data[$ii]);
			$rdate = str_replace('月', '-', $rdate);
			$rdate = str_replace('日', '', $rdate);
			$add_time = strtotime($rdate);
			$zo = 0;
			$z1 = 1;
			$email = time() * rand(10, 100) . '@qq.com';
			$password = time();
			$reg_date = $add_time - 30 * 24 * 3600;
			$ip = rand(100, 255) . "." . rand(100, 255) . "." . rand(100, 255) . "." . rand(100, 255);
			$is_ex = $GLOBALS['db']->getOne("SELECT count(*) FROM " . $GLOBALS['ecs']->table("comment") . " WHERE user_name = '" . $user_name . "' and content='" . $content . "'");
			if ($is_ex > 0) {
				continue;
			}
			if ($itemlist == 1) {
				$flag += 1;
				$comment_list[$flag] = array('id' => $flag, 'comment_type' => $zo, 'id_value' => $goods_id, 'email' => $email, 'user_name' => $user_name, 'content' => $content, 'comment_rank' => 5, 'add_time' => $add_time, 'ip_address' => $ip, 'status' => $z1, 'rdate' => $rdate);
			} elseif ($is_order) {
				//检查用户名，不存在则生成新用户
				$user_id = check_user($user_name);
				if ($user_id == false) {
					$GLOBALS['db']->query('INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(`email`, `user_name`, `password`, `reg_time`, `last_login`, `last_ip`) VALUES ('{$email}', 
										'{$user_name}', '{$password}', '{$reg_date}', '{$reg_date}', '{$ip}')");
					$user_id = $GLOBALS['db']->insert_id();
				}
				if (rand(1, 15) != 5) {
					$sql_back = "INSERT IGNORE INTO" . $GLOBALS['ecs']->table('comment') . "(comment_type, id_value, email, user_name, content,  comment_rank, add_time, ip_address, status, parent_id, user_id) VALUES ";
					$sql_back .= " ( '" . $zo . "', 
														 '" . $goods_id . "',
														 '" . $email . "',
														 '" . $user_name . "',
														 '" . $content . "',
														 '5', 
														 '" . $add_time . "', 
														 '" . $ip . "',
														 '" . $z1 . "', 
														 '" . $zo . "', 
														 '" . $user_id . "'
														  )";
					$GLOBALS['db']->ping();
					$GLOBALS['db']->query($sql_back, 'SILENT');
					$flag += 1;
					showjsmessage('<font  class="cj_green">' . $flag . '.&nbsp;&nbsp;[' . $user_name . ']</font>&nbsp;&nbsp;[<font  class="cj_black">' . mb_substr($content, 0, 40, 'utf-8') . '...]</font>: 成功');
				}
				//生成订单****
				$order_sn = get_order_sn000();
				$order_status = 5;
				$shipping_status = 2;
				$pay_status = 2;
				$pay_time = $payTime_1 > 0 ? $payTime_1 : $add_time - rand(3, 10) * 3600 * 24;
				$addtime = $pay_time - rand(20, 3600);
				// pr(date("Y-m-d",$addtime));
				// pr($pay_time,1);
				$goods_number = rand(1, 10) == 5 ? rand(1, 10) : 1;
				$goods_info = $GLOBALS['db']->getRow("SELECT goods_name,goods_sn,market_price,shop_price FROM " . $GLOBALS['ecs']->table("goods") . " WHERE goods_id = " . $goods_id);
				$xxc = $addtime - $_SESSION['TimeSplit'];
				//插入订单
				$GLOBALS['db']->query('INSERT IGNORE INTO ' . $GLOBALS['ecs']->table("order_info") . "(`order_sn`, `user_id`, `order_status`, `shipping_status`, `pay_status`, `add_time`, `pay_time`) 
							VALUES ('{$order_sn}', '{$user_id}', '{$order_status}', '{$shipping_status}', '{$pay_status}', '{$addtime}', '{$pay_time}')");
				$orderid = $GLOBALS['db']->getOne("SELECT order_id FROM " . $GLOBALS['ecs']->table("order_info") . " WHERE order_sn = '" . $order_sn . "'");
				$GLOBALS['db']->query('INSERT IGNORE INTO ' . $GLOBALS['ecs']->table("order_goods") . "(`order_id`, `goods_id`, `goods_name`, `goods_sn`, `goods_number`, `market_price`, 
							`goods_price`, `send_number`, `is_real`) VALUES ('{$orderid}', '{$goods_id}', '" . $goods_info['goods_name'] . "', '" . $goods_info['goods_sn'] . "', '{$goods_number}', " . $goods_info['market_price'] . ", " . $goods_info['shop_price'] . ", '{$goods_number}', 1)");
			} else {
				$sql_back = "INSERT IGNORE INTO" . $GLOBALS['ecs']->table('comment') . "(comment_type, id_value, email, user_name, content,  comment_rank, add_time, ip_address, status, parent_id) VALUES ";
				$sql_back .= " ( '" . $zo . "', 
												 '" . $goods_id . "',
												 '" . $email . "',
												 '" . $user_name . "',
												 '" . $content . "',
												 '5', 
												 '" . $add_time . "', 
												 '" . $ip . "',
												 '" . $z1 . "', 
												 '" . $zo . "' 
												  )";
				// pr($sql_back,1);
				$GLOBALS['db']->ping();
				$GLOBALS['db']->query($sql_back, 'SILENT');
				$flag += 1;
				showjsmessage('<font  class="cj_green">' . $flag . '.&nbsp;&nbsp;[' . $user_name . ']</font>&nbsp;&nbsp;[<font  class="cj_black">' . mb_substr($content, 0, 40, 'utf-8') . '...]</font>: 成功');
			}
		}
		if ($itemlist == 1) {
			nms_comment_list($comment_list);
		}
	}
	$pass_time = cj_timer($stime);
	showjsmessage('<br><font class="cj_over"><strong>[' . $flag . ']</strong>条评论采集完成！</font>(用时：' . $pass_time . '秒)<br>');
	showjsmessage('进入<a href="comment_manage.php?act=list">评论列表</a>', 1);
	die;
} elseif ($_REQUEST['act'] == 'insert_comment_list') {
	if (isset($_POST['checkboxes'])) {
		$comment_list = unserialize(stripslashes($_REQUEST['comment_list_data']));
		flush_echo_nms(pr_heads());
		//打印网页头
		showjsmessage('正在采集选定评论....');
		$flag = count($_POST['checkboxes']);
		foreach ((array) $_POST['checkboxes'] as $id) {
			$insert_comment = $comment_list[$id];
			unset($insert_comment['id']);
			unset($insert_comment['rdate']);
			if ($GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('comment'), $insert_comment, 'INSERT')) {
				showjsmessage('<font  class="cj_green">' . $id . '.&nbsp;&nbsp;[' . $insert_comment['user_name'] . ']</font>&nbsp;&nbsp;[<font  class="cj_black">' . mb_substr($insert_comment['content'], 0, 40, 'utf-8') . '...]</font>: 成功');
			} else {
				showjsmessage('<font  class="cj_green">' . $id . '.&nbsp;&nbsp;[' . $insert_comment['user_name'] . ']</font>&nbsp;&nbsp;[<font  class="cj_black">' . mb_substr($insert_comment['content'], 0, 40, 'utf-8') . '...]</font>: 失败');
			}
		}
		showjsmessage('<br><font class="cj_over"><strong>[' . $flag . ']</strong>条评论采集完成！</font><br>');
		showjsmessage('进入<a href="comment_manage.php?act=list">评论列表</a>', 1);
	}
}
/*评论采集*/
function GetRate($num_iid, $goods_id, $conum, $is_order, $itemlist)
{
	global $db, $ecs;
	$flag = 0;
	$attr = array();
	if (empty($tb_seller_id)) {
		//兼容以前的采集版本：如果商品表tb_seller_id没有记录，就要采集淘宝商家ID
		$tb_seller_id = '2091079883';
	}
	ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)');
	for ($j = 1; $j <= 100; $j++) {
		$tbrate = get_taobao_rate($num_iid, $tb_seller_id, $j);
		$content = mb_convert_encoding($tbrate, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
		$content = str_replace(":[", "OOO1", $content);
		$content = str_replace("],", "OOO2", $content);
		$content = str_replace("[", "（", $content);
		$content = str_replace("]", "）", $content);
		$content = str_replace("OOO1", ":[", $content);
		$content = str_replace("OOO2", "],", $content);
		$content = str_replace("评价,系统", "评价，系统", $content);
		$content = "{" . $content . "}";
		$rate_html = json_decode($content, true);
		$rate = $rate_html['rateDetail']['rateList'];
		if (empty($rate)) {
			//			showjsmessage("此商品没有评论!");
			break;
		}
		foreach ((array) $rate as $value) {
			if ((int) $flag == (int) $conum) {
				break 2;
			}
			$content = $value['appendComment']['content'];
			if (empty($content)) {
				$content = $value['rateContent'];
			}
			$user_name = $value['displayUserNick'];
			$rdate = $value['rateDate'];
			$rdate = strtotime($rdate);
			$add_time = $rdate;
			$zo = 0;
			$z1 = 1;
			$email = time() * rand(10, 100) . '@qq.com';
			$password = time();
			$reg_date = $add_time - 30 * 24 * 3600;
			$ip = rand(100, 255) . "." . rand(100, 255) . "." . rand(100, 255) . "." . rand(100, 255);
			$is_ex = $GLOBALS['db']->getOne("SELECT count(*) FROM " . $GLOBALS['ecs']->table("comment") . " WHERE user_name = '" . $user_name . "' and content='" . $content . "'");
			if ($is_ex > 0) {
				continue;
			}
			if ($itemlist == 1) {
				$flag += 1;
				$comment_list[$flag] = array('id' => $flag, 'comment_type' => $zo, 'id_value' => $goods_id, 'email' => $email, 'user_name' => $user_name, 'content' => $content, 'comment_rank' => 5, 'add_time' => $add_time, 'ip_address' => $ip, 'status' => $z1, 'rdate' => $value['rateDate']);
			} elseif ($is_order) {
				//检查用户名，不存在则生成新用户
				$user_id = check_user($user_name);
				if ($user_id == false) {
					$GLOBALS['db']->query('INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(`email`, `user_name`, `password`, `reg_time`, `last_login`, `last_ip`) VALUES ('{$email}', 
										'{$user_name}', '{$password}', '{$reg_date}', '{$reg_date}', '{$ip}')");
					$user_id = $GLOBALS['db']->insert_id();
				}
				if (rand(1, 15) != 5) {
					$sql_back = "INSERT IGNORE INTO" . $GLOBALS['ecs']->table('comment') . "(comment_type, id_value, email, user_name, content,  comment_rank, add_time, ip_address, status, parent_id, user_id) VALUES ";
					$sql_back .= " ( '" . $zo . "', 
														 '" . $goods_id . "',
														 '" . $email . "',
														 '" . $user_name . "',
														 '" . $content . "',
														 '5', 
														 '" . $add_time . "', 
														 '" . $ip . "',
														 '" . $z1 . "', 
														 '" . $zo . "', 
														 '" . $user_id . "'
														  )";
					$GLOBALS['db']->ping();
					$GLOBALS['db']->query($sql_back, 'SILENT');
					$flag += 1;
					showjsmessage('<font  class="cj_green">' . $flag . '.&nbsp;&nbsp;[' . $user_name . ']</font>&nbsp;&nbsp;[<font  class="cj_black">' . mb_substr($content, 0, 40, 'utf-8') . '...]</font>: 成功');
				}
				//生成订单****
				$order_sn = get_order_sn000();
				$order_status = 5;
				$shipping_status = 2;
				$pay_status = 2;
				$pay_time = $payTime_1 > 0 ? $payTime_1 : $rdate - rand(3, 10) * 3600 * 24;
				$addtime = $pay_time - rand(20, 3600);
				$goods_attr = $value['auction']['sku'];
				// pr(date("Y-m-d",$addtime));
				// pr($pay_time,1);
				$goods_number = rand(1, 10) == 5 ? rand(1, 10) : 1;
				$goods_info = $GLOBALS['db']->getRow("SELECT goods_name,goods_sn,market_price,shop_price FROM " . $GLOBALS['ecs']->table("goods") . " WHERE goods_id = " . $goods_id);
				$xxc = $addtime - $_SESSION['TimeSplit'];
				//插入订单
				$GLOBALS['db']->query('INSERT IGNORE INTO ' . $GLOBALS['ecs']->table("order_info") . "(`order_sn`, `user_id`, `order_status`, `shipping_status`, `pay_status`, `add_time`, `pay_time`) 
							VALUES ('{$order_sn}', '{$user_id}', '{$order_status}', '{$shipping_status}', '{$pay_status}', '{$addtime}', '{$pay_time}')");
				$orderid = $GLOBALS['db']->getOne("SELECT order_id FROM " . $GLOBALS['ecs']->table("order_info") . " WHERE order_sn = '" . $order_sn . "'");
				$GLOBALS['db']->query('INSERT IGNORE INTO ' . $GLOBALS['ecs']->table("order_goods") . "(`order_id`, `goods_id`, `goods_name`, `goods_sn`, `goods_number`, `market_price`, 
							`goods_price`, `send_number`, `goods_attr`,`is_real`) VALUES ('{$orderid}', '{$goods_id}', '" . $goods_info['goods_name'] . "', '" . $goods_info['goods_sn'] . "', '{$goods_number}', " . $goods_info['market_price'] . ", " . $goods_info['shop_price'] . ", '{$goods_number}','{$goods_attr}', 1)");
			} else {
				$sql_back = "INSERT IGNORE INTO" . $GLOBALS['ecs']->table('comment') . "(comment_type, id_value, email, user_name, content,  comment_rank, add_time, ip_address, status, parent_id) VALUES ";
				$sql_back .= " ( '" . $zo . "', 
													 '" . $goods_id . "',
													 '" . $email . "',
													 '" . $user_name . "',
													 '" . $content . "',
													 '5', 
													 '" . $add_time . "', 
													 '" . $ip . "',
													 '" . $z1 . "', 
													 '" . $zo . "' 
													  )";
				// pr($sql_back,1);
				$GLOBALS['db']->ping();
				$GLOBALS['db']->query($sql_back, 'SILENT');
				$flag += 1;
				showjsmessage('<font  class="cj_green">' . $flag . '.&nbsp;&nbsp;[' . $user_name . ']</font>&nbsp;&nbsp;[<font  class="cj_black">' . mb_substr($content, 0, 40, 'utf-8') . '...]</font>: 成功');
			}
		}
	}
	if ($itemlist == 1) {
		nms_comment_list($comment_list);
	}
	unset($rate_html);
	unset($rate);
	clear_cache_files();
	return $flag;
}
function get_taobao_rate($num_iid, $tb_seller_id, $j)
{
	$url = 'http://121.199.160.218/get_taobao_rate.php?itemId=' . $num_iid . '&sellerId=' . $tb_seller_id . '&j=' . $j;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if (is_array($postFields) && 0 < count($postFields)) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	}
	$reponse = curl_exec($ch);
	curl_close($ch);
	//pr($reponse,1);//测试变量返回值
	return $reponse;
}
/**
 * 生成编辑器
 * @param   string  input_name  输入框名称
 * @param   string  input_value 输入框值
 */
function create_html_editor_ec($input_name, $input_value = '')
{
	global $smarty;
	$editor = new FCKeditor($input_name);
	$editor->BasePath = '../includes/fckeditor/';
	$editor->ToolbarSet = 'Normal';
	$editor->Width = '100%';
	$editor->Height = '320';
	$editor->Value = $input_value;
	$FCKeditor = $editor->CreateHtml();
	$smarty->assign('FCKeditor', $FCKeditor);
}
function flush_echo_nms($data)
{
	echo str_pad('  ', 32000);
	ob_end_flush();
	ob_implicit_flush(true);
	echo $data;
}
function showjsmessage($message, $ext = 0)
{
	//echo('showmessage(\''.addslashes($message).' \','.$ext.')');
	//exit;
	flush_echo_nms('<script type="text/javascript">showmessage(\'' . addslashes($message) . '\',' . $ext . ');</script>' . "\r\n");
}
function pr_heads()
{
	//采集输出样式
	$heads = '<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<style type="text/css">
		body{ padding:5px 0; background:#FFF; text-align:center; width:80%;min-width:400px;background: #F5F5F5;}
		body, td, input, textarea, select, button{ color:#666; font:12px/1.5 Verdana, Tahoma, Arial, "Microsoft Yahei", "Simsun", sans-serif; }
		.container{ overflow:hidden; margin:0 auto; width:700px; height:auto !important;text-align:left; border:1px solid #B5CFD9; }
		.main{ padding:20px 20px 0; background:#F7FBFE url(bg_repx.gif) repeat-x 0 -194px; }
		.main h3{ margin:10px auto; width:75%; color:#6CA1B4; font-weight:700; }
		#notice {overflow-y:scroll; margin: 20px; padding: 5px 20px; border: 1px solid #B5CFD9; text-align: left; background: #fff;height:70%;}
		#notice img{margin: 5px 0 0;width:30px; height:30px; border: 2px solid #ccc;vertical-align: bottom; }
		#notice .yuantu{width:16px; height:16px; border:none;vertical-align: bottom; }
		#notice a{color: #666;}
		#notice a:hover{color: #FF6600;}
		.hide{display:none}
		.cj_green{color:#009900;}
		.cj_red{color:#FF0000;}
		.cj_bulue{color:#0033FF;}
		.cj_cn{color:#FF00FF;}
		.cj_hui{color:#999;}
		.cj_fanyi{color:#009900;}
		.cj_black{color:#000;}
		.cj_over1{color:#000;}
		.cj_over{color:#FF0000;}
		</style>
		<meta name="Copyright" content="Comsenz Inc.">
		</head>
		<body>
		<script type="text/javascript">
		function showmessage(message,ext) {
			document.getElementById("notice1").innerHTML += message + "<br/>";
			if (ext==1){
					document.getElementById("zload").innerHTML="";
			}
			document.getElementById("notice").scrollTop = 100000000;
		}
		</script><br />
		<div id="notice">
		<div id="notice1"></div>
		<div id="zload"><img src=https://img.alicdn.com/imgextra/i2/619666972/TB2tFwrjVXXXXXnXpXXXXXXXXXX-619666972.gif class=yuantu></div>
		</div>';
	return $heads;
}
function pr($str, $ex = 0)
{
	print_r($str);
	echo "<br>";
	if ($ex == 1) {
		die;
	}
}
function cj_timer($stime)
{
	$etime = time() + microtime();
	$pass_time = sprintf("%.2f", $etime - $stime);
	//消耗时间
	return $pass_time;
}
function check_user($user_name)
{
	$sql = "SELECT user_id FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_name='{$user_name}'";
	$user_id = $GLOBALS['db']->getOne($sql);
	if ($user_id > 0) {
		return $user_id;
	} else {
		return false;
	}
}
// 得到新订单号
function get_order_sn000()
{
	//mt_srand((double)microtime() * 1000000);
	return date('Ymd') . str_pad(mt_rand(1, 99999) + mt_rand(1, 999), 8, '0', STR_PAD_LEFT);
}
function save_user_setting($set_arr, $class, $fun)
{
	$user_setting = serialize($set_arr);
	//序列化
	$_SESSION['nms_colect_setting'][$class][$fun] = $user_setting;
}
function nms_comment_list($comment_list)
{
	//pr($comment_list,1);
	$comment_list_data = serialize($comment_list);
	//序列化
	$smarty = $GLOBALS['smarty'];
	$smarty->assign('ur_here', '评论采集列表');
	$smarty->assign('full_page', 1);
	$smarty->assign('comment_list', $comment_list);
	$smarty->assign('comment_list_data', $comment_list_data);
	$smarty->assign('page_num', $page_num);
	$smarty->display('nms_comment_list.dwt');
	die;
}