<?php

require_once('./helpers.php');




// get prefix id
// 
$book = R::load(get_table('base_language_prefix'), $id = 30);

$prefix = 'example';
$needles = R::find(get_table('base_language_prefix'),
        ' prefix = :prefix', 
            array( 
                ':prefix'=>$prefix 
            )
        );



//check already exist

// Insert into key table
// 


// Insert into value table

