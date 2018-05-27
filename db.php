<?php
$servername = "127.0.0.1";
$username = "root";
$password = "my_fake_password";
$db_name = "my_fake_db_name";

// first load
if(! isset( $_GET["maxLat"] )  || ! isset( $_GET["minLat"] ) || ! isset( $_GET["maxLng"] )  || ! isset( $_GET["minLng"] )){
	//hard-coded (10 miles) from Tucson
	$maxLat = '32.39756947193449';
	$minLat = '32.10843052806551';
	$maxLng = '-110.74105338366127';
	$minLng = '-111.0829466163388';
}else{
	$maxLat = $_GET["maxLat"];
	$minLat = $_GET["minLat"];
	$maxLng = $_GET["maxLng"];
	$minLng = $_GET["minLng"];
}

// figure out what type of query to make
if( isset($_GET["cache_type"]) ){
	 if( $_GET["cache_type"] != "0" ){
	 	$cache_type_param = $_GET["cache_type"];
		$cache_type_cond = "AND ct.type_id = ${cache_type_param}";
		$complex_query_flag = true;
	 }
}
if( isset($_GET["difficulty"]) ){
	 if( $_GET["difficulty"] != "0" ){
	 	$difficulty_param = $_GET["difficulty"];
		$difficulty_cond = "AND td.difficulty_rating = ${difficulty_param}";
		$complex_query_flag = true;
	 }
}
if( isset($_GET["lower_lim"]) && isset($_GET["upper_lim"])){
 	$lower_lim = $_GET["lower_lim"];
	$upper_lim = $_GET["upper_lim"];
	$limit_query_flag = true;
}
if( isset($_GET["count_only"]) ){
	$count_only_flag = true;
}

// set up db connection
$mysqli = new mysqli($servername, $username, $password, $db_name) OR die ('Unable to connect!');
mysqli_set_charset( $mysqli, 'utf8' );

// I really want to rename these queries as the names aren't super descriptive
function get_limited_result_set($minLat,$maxLat,$minLng,$maxLng){
	return "SELECT td.*, ct.cache_type FROM test_data td LEFT JOIN cache_types ct ON ct.type_id = td.cache_type_id WHERE ( latitude BETWEEN ${minLat} AND ${maxLat} 
			AND longitude BETWEEN ${minLng} AND ${maxLng}) ORDER BY td.latitude ASC, td.longitude ASC";
}

function get_limited_result_set_limits($minLat,$maxLat,$minLng,$maxLng,$lower_lim,$upper_lim){
	return "SELECT td.*, ct.cache_type FROM test_data td LEFT JOIN cache_types ct ON ct.type_id = td.cache_type_id WHERE ( latitude BETWEEN ${minLat} AND ${maxLat} 
			AND longitude BETWEEN ${minLng} AND ${maxLng}) ORDER BY td.latitude ASC, td.longitude ASC LIMIT ${lower_lim}, ${upper_lim}";
}

function get_limited_result_set_limits_count($minLat,$maxLat,$minLng,$maxLng,$lower_lim,$upper_lim){
	return "SELECT COUNT(*) AS count FROM test_data td LEFT JOIN cache_types ct ON ct.type_id = td.cache_type_id WHERE ( latitude BETWEEN ${minLat} AND ${maxLat} 
			AND longitude BETWEEN ${minLng} AND ${maxLng})";
}

function get_complex_result_set_filters($minLat,$maxLat,$minLng,$maxLng,$cache_type_cond,$difficulty_cond){
	return "SELECT td.*, ct.cache_type FROM test_data td LEFT JOIN cache_types ct ON ct.type_id = td.cache_type_id WHERE ( latitude BETWEEN ${minLat} AND ${maxLat} 
			AND longitude BETWEEN ${minLng} AND ${maxLng} ${cache_type_cond} ${difficulty_cond}) ORDER BY td.latitude ASC, td.longitude ASC";
}

function get_complex_result_count_only($minLat,$maxLat,$minLng,$maxLng,$cache_type_cond,$difficulty_cond){
	return "SELECT COUNT(*) AS count FROM test_data td LEFT JOIN cache_types ct ON ct.type_id = td.cache_type_id WHERE ( latitude BETWEEN ${minLat} AND ${maxLat} 
			AND longitude BETWEEN ${minLng} AND ${maxLng} ${cache_type_cond} ${difficulty_cond}) ORDER BY td.latitude ASC, td.longitude ASC";
}

function get_complex_result_limits($minLat,$maxLat,$minLng,$maxLng,$cache_type_cond,$difficulty_cond, $lower_lim, $upper_lim
){
	return "SELECT td.*, ct.cache_type FROM test_data td LEFT JOIN cache_types ct ON ct.type_id = td.cache_type_id WHERE ( latitude BETWEEN ${minLat} AND ${maxLat} 
			AND longitude BETWEEN ${minLng} AND ${maxLng} ${cache_type_cond} ${difficulty_cond}) ORDER BY td.latitude ASC, td.longitude ASC LIMIT ${lower_lim}, ${upper_lim}";
}

if($complex_query_flag){
	if($limit_query_flag){
		$db_limited_result = $mysqli->query(get_complex_result_limits($minLat,$maxLat,$minLng,$maxLng,$cache_type_cond,$difficulty_cond,$lower_lim,$upper_lim));
	}else if($count_only_flag){
		$db_limited_result = $mysqli->query(get_complex_result_count_only($minLat,$maxLat,$minLng,$maxLng,$cache_type_cond,$difficulty_cond));
	}
}else{
	if($count_only_flag){
		//echo get_limited_result_set_limits_count($minLat,$maxLat,$minLng,$maxLng,$lower_lim, $upper_lim);
		$db_limited_result = $mysqli->query( get_limited_result_set_limits_count($minLat,$maxLat,$minLng,$maxLng,$lower_lim, $upper_lim));
	}else{
		//echo get_limited_result_set_limits($minLat,$maxLat,$minLng,$maxLng,$lower_lim, $upper_lim);
			$db_limited_result = $mysqli->query( get_limited_result_set_limits($minLat,$maxLat,$minLng,$maxLng,$lower_lim, $upper_lim));
	}
}
$db_limited_result_set = [];

if( $db_limited_result->num_rows > 0 ){
    while($row = $db_limited_result->fetch_array(MYSQLI_BOTH)) {
            $db_limited_result_set[] = $row;
    }
}

echo json_encode($db_limited_result_set);

?>
