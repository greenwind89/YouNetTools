<?php
require_once('./database/rb.php');
require_once('./helpers.php');
// I implement it as a web service

//Oxwall 
$db_name = 'oxwall';
$db_driver = 'mysql';
$db_host = 'localhost';
$username = 'root';
$password ='1111122222';
$table_prefix = 'ow_';

//get data from reqest 
//
if(!$_REQUEST)
{
	return_to_client(false, 'no request found');
	exit;
}

//in oxwall we assume prefix = module name
$module_name = $_REQUEST['module_name'];

$phrase_content = $_REQUEST['phrase_content'];
$phrase_key = $_REQUEST['phrase_key'];


R::setup("{$db_driver}:host={$db_host};dbname={$db_name}",
        $username, $password);

R::setStrictTyping(false);


// get prefix id
// 
$book = R::load(get_table('base_language_prefix'), $id = 30);

$prefix = $module_name;
$prefixs = R::find(get_table('base_language_prefix'),
        ' prefix = :prefix', 
            array( 
                ':prefix'=>$prefix 
            )
        );

//only get the first result
$prefix_id = current($prefixs)->id;


if(check_phrase_exist($phrase_key, $prefix_id))
{
	return_to_client(false, 'phrase existed');
	exit;
}

// $phrase_key_object = R::dispense(get_table('base_language_key'));
// $phrase_key_object->prefixid = $prefix_id;
// $phrase_key_object->key = $phrase_key;
// $new_phrase_key_id = R::store($phrase_key_object); 

try{
	$query = 'INSERT INTO ' . get_table('base_language_key') . "(prefixId, `key`) VALUES ($prefix_id, '$phrase_key')";
	$result = R::exec( $query);
} catch (Exception $e)
{
	return_to_client(false, 'insert into ' . get_table('base_language_key') . '  ' . $e->getMessage());
	exit;
}

$phrase_key_id = get_phrase_key_id($phrase_key, $prefix_id);

if(!$phrase_key_id)
{
	return_to_client(false, 'cannot get phrase key id');
}

try{
	$query = 'INSERT INTO ' . get_table('base_language_value') . "(languageId, keyId, value) VALUES (1, $phrase_key_id, '$phrase_content')";
	R::exec( $query);
} catch (Exception $e)
{
	return_to_client(false, 'insert into ' . get_table('base_language_value') . '  ' . $e->getMessage());
	exit;
}


function check_phrase_exist($phrase_key, $prefix_id) {
	$phrases = R::find(get_table('base_language_key'),
        " prefixId = :prefix_id AND " . get_table('base_language_key') . ".key LIKE :phrase_key ", 
            array( 
                ':prefix_id' => $prefix_id,
                ':phrase_key' => $phrase_key  
            )
        );

	if(count($phrases) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_phrase_key_id($phrase_key, $prefix_id)
{
	$phrases = R::find(get_table('base_language_key'),
        " prefixId = :prefix_id AND " . get_table('base_language_key') . ".key LIKE :phrase_key ", 
            array( 
                ':prefix_id' => $prefix_id,
                ':phrase_key' => $phrase_key  
            )
        );

	return current($phrases)->id;
}
function get_table($table_name) {
	global $table_prefix;
	return $table_prefix . $table_name;
}