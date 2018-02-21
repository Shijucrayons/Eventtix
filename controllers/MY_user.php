<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function total_items($id)
{
    $CI =& get_instance();
    return $CI->common->getRdAnswers($id);
}