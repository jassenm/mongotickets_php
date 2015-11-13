<?php
#
# Copyright (c) 2007, Jassen Moran. All rights reserved.
# Quad tickets Confidential Proprietary.
#


function SpecialCharToDashes($string)
{
    $s = preg_replace ('/[^a-zA-Z0-9]/', '-', $string);

	return $s;
}

function AmpersandToAnd($string)
{
    $s = preg_replace ('/\&/', ' and ', $string);
	return $s;
}

function RemoveSpecialChars($string)
{
	$s = preg_replace ('/[^a-zA-Z0-9 ]/', ' ', $string);

	return $s;
}

?>
