<?php
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $chars_len = strlen($chars);
        $sid_length = 5;
        $sid = '';
        for ($i=0; $i < $sid_length; $i++) {
		$r = (float)rand()/(float)getrandmax();
		$r = (float)($r * $chars_len);
                $rnum = floor($r);
                $sid = $sid .  substr($chars, $rnum, 1);

        }
        echo $sid;

?>
