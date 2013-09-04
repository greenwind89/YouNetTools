<?php

function return_to_client($result, $message) {
	echo $result ? 'success' : 'faield';
	echo '<br>';
	echo 'message: ' . $message;
	echo '<br>';
}