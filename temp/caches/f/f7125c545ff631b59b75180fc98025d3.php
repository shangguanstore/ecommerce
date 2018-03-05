<?php exit;?>00151737316116dc622aba528f37c575a14970fc98cas:997:"a:2:{s:8:"template";s:934:"<?php $__Template->display($this->getTpl("page_header")); ?>
<?php $n=1;if(is_array($list)) foreach($list as $list) { ?>
<?php $n=1;if(is_array($list['activity_list'])) foreach($list['activity_list'] as $key=>$activity) { ?>
<section <?php if($key > 0) { ?>class="m-top06"<?php } ?>>
    <a href="<?php echo $activity['url']; ?>">
        <div class="act-header-box-list">
            <div class="act-right-box">
                <?php echo $list['activity_name']; ?>
            </div>
            <img src="<?php echo $activity['activity_thumb']; ?>">
        </div>
        <div class="padding-all b-color-f ">
            <h3 class="my-u-title-size"><?php echo $activity['act_name']; ?></h3>
            <p class="act-cont"><?php echo $activity['start_time']; ?> - <?php echo $activity['end_time']; ?> </p>
        </div>
    </a>
</section>
<?php $n++;}unset($n); ?>
<?php $n++;}unset($n); ?>
</body>
</html>
";s:12:"compile_time";i:1517286761;}";