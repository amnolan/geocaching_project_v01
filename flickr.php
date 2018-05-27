<?php
	// I realized that putting the key on the actual page was not a good practice. I then refactored it to call the server to 
	// do the request on the web page's behalf for security reasons.
	// I still want to do this for the maps api but it seems like it may be a bit more complicated to do that
	$api_key = "YOUR_API_KEY_HERE";
	$flickr_method = "flickr.photos.search";
	$flickr_rest_endpoint = "https://api.flickr.com/services/rest/";


	$api_key = $_GET['api_key'];
	$format = $_GET['format'];
	$lat = $_GET['lat'];
	$lon = $_GET['lon'];

	$url = "https://api.flickr.com/services/rest/?api_key=${api_key}&method=${flickr_method}&lat=${lat}&lon=${lon}&format=${format}&nojsoncallback=%3F";
	//echo $url;

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
	    "cache-control: no-cache"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	echo $response;

?>