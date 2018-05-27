	var map_width;
	$(document).ready(function(){
		if( ! $("#latitude").val() && ! $("#longitude").val() ){
			$("#latitude").val("32.253");
			$("#longitude").val("-110.912");
		}
		// figure out the beginning width to prevent it from changing during scroll
		// I used affix and scroll spy but the css kept on getting messed up upon scrolling
		map_width = $("#scroll_map").width();
		$(window).scroll(function() {
			$("#scroll_map").width(map_width);
		});
		$("#paginate").on('change', function(){
			paginate_query_db_for_pt(this);
		});
		// I was concerned about a case where the user directly changes something and directly clicks submit
		// for instance you're in the middle of browsing a specific result set, but then the user changes the
		// lat or lon and tries to select the next page... that may or may not exist but the result will not make sense either way
		// still if the user is savvy, they can directly click and hold on the list and it won't get an opportunity to hide it
		$(".prevent_pagination_change_query").on('change', function(){
			$(".prevent_pagination_change_query_hide").hide();
		});
		$("#submit").on('click', function(){
			$(".prevent_pagination_change_query_hide").show();
		});
		// initial query
		query_db_for_pt();
		// setTimeout(function(){ $("#expander_effect").click() }, 350);
	});

	function list_colors(obj,list_elems){
		for(var i = 0; i < list_elems.length; i++){
			$(list_elems[i]).removeClass("active");
			// only make it normal if it needs it
			if( ! $(list_elems[i]).hasClass("list-group-item-action") ){
				$(list_elems[i]).addClass("list-group-item-action");
			}
		}
		$(obj).removeClass("list-group-item-action");
		$(obj).addClass("active");
	}

	function list_styling_after_load(){
		var list_elems = $(".list-group-item");
		$(list_elems).on('click change',function(){
			list_colors(this,list_elems);
		});
	}
	// function expander_effect(){
	// 	// simulate click
	// 	$("#expander_effect").click();
	// }

	// I thought this struct / prototype would be useful but it may actually be very not useful in the end
	function sql_list_pager_obj(total_pgs = 0, total_res = 0 ,curr_set_lower_lim = 0, curr_set_upper_lim = 0) {
	    this.total_res = total_res;
	    this.total_pgs = total_pgs;
	    this.curr_set_lower_lim = curr_set_lower_lim;
	    this.curr_set_upper_lim = curr_set_upper_lim;
	}

	// for use in the query, this can be refactored to simply to a ceiling operation so you don't need an if else block at all
	function get_num_pages(cnt){
	  	if(cnt < 20){
	    	return 1;
	  	}else if(cnt/20 > 0){
	    	return Math.ceil(cnt/20);
	  	}
	}

	var pager;

	var resp;
	var markers = [];
	var info_window_open;
	// abstract method, can handle any request
	function build_request(url, params ,call_back, other_params = null){
		var xhr = new XMLHttpRequest();
		var param_str = jQuery.param( params );
		xhr.open('GET', url + "?" + param_str, true);
		xhr.onload = function() {
		    if (xhr.status === 200) {
		     	resp = JSON.parse(xhr.responseText);
		     	if(other_params != null){
					call_back(resp, other_params);
		     	}else{
		     		call_back(resp);
		     	}
		    }
		    else {
		        console.log('Something went wrong, returned response code of: ' + xhr.status);
		    }
		};
		xhr.send();
	}

	function catch_flickr_test(resp){
		console.log(resp);
	}

	function trigger_flickr_request(){
		build_request("flickr.php","",catch_flickr_test);
	}

	function geocache_query_cb(resp){
		var count = resp[0]['count'];
		var num_pages = get_num_pages(count);
		pager = new sql_list_pager_obj(num_pages,count,0,20)
     	clear_markers();
	}

	function geocache_limit_query_cb(resp){
		clear_markers();
     	redraw_list(resp);
     	add_list_listeners();
     	recenter_on_center_of_circle();
     	rebuild_paginate_select();
     	notify_user();
	}

	function rebuild_paginate_select(){
		var selected_index = intFromField("paginate");
		$("#paginate").empty();
		for(var i = 0; i < pager.total_pgs; i++){
			$('#paginate').append($('<option/>', { 
		        value: i,
		        text : "Page " + (parseInt(i) + 1)
		    }));
		}
		if( selected_index < $('#paginate').children('option').length){
			$('#paginate')[0].selectedIndex = selected_index;
		}else{
			$('#paginate')[0].selectedIndex = 0;
		}
	}

	function notify_user(){
		if(intFromField("paginate") == 0){
			 $.notify({
		      title: "<strong>Your search returned " + pager.total_res + " results, here are the first 20!</strong>",
		      icon: 'glyphicon glyphicon-map-marker',
		      message: " -- Select an item from the list to get more information."
		    },{
		      type: 'info',
		      animate: {
				    enter: 'animated fadeInUp',
		        exit: 'animated fadeOutRight'
		      },
		      placement: {
		        from: "top",
		        align: "center"
		      },
		      offset: 200,
		      spacing: 20,
		      z_index: 1050,
		    });
		}else if(intFromField("paginate") > 0){
			 $.notify({
	      title: "<strong>Here are the next 20</strong>",
	      icon: 'glyphicon glyphicon-map-marker',
	      message: " -- Select an item from the list to get more information."
	    },{
	      type: 'info',
	      animate: {
			    enter: 'animated fadeInUp',
	        exit: 'animated fadeOutRight'
	      },
	      placement: {
	        from: "top",
	        align: "center"
	      },
	      offset: 200,
	      spacing: 20,
	      z_index: 1050,
	    });
		}
	   
	}

	function redraw_list(list){
		var ctr = 0;
		$("#location_list").empty();
		for (var key in list) {
		    if (list.hasOwnProperty(key)) {
		        var li = $('<a></a>').addClass('list-group-item list-group-item-action').attr('id', "row_" + ctr);
				li.text("Lat " + list[key].latitude + ", Lon " + list[key].longitude + ", Difficulty "+ list[key].difficulty_rating + ", Cache Type " + list[key].cache_type);
				li.appendTo('#location_list');				
				ctr++;
		    }
		}
		add_markers(list);
		list_styling_after_load();

	}

	function close_any_info_window(){
		if( info_window_open ){
			info_window_open.close();
		}
	}

	function call_flickr_farm(json, me){
		close_any_info_window();
		// take first 12 photos
		var content_str = "<div><table class='info_window'>";

		for(var i = 0; i < 12; i++){
			if( i % 4 == 0 || i == 0){
				content_str += "<tr>";
			}
			content_str += "<td>"
			var photo_url;
			var photo = json.photos.photo[i];
			photo_url = "https://farm" +
			photo['farm'] + ".staticflickr.com/" +
			photo['server'] + "/" +
			photo['id'] + "_" +
			photo['secret'] +
			"_t.jpg";
			content_str += "<img class='info_window_img' src='"
			content_str += photo_url
			content_str += "'"
			content_str += " alt= '"
			content_str += photo['title']
			content_str += "'"
			content_str += "</td>";
			if( i!= 0 && (i == 3) || (i == 7) || (i==11)){
				content_str += "</tr>";
			}
		}
		content_str += '</div>';
  		var infowindow = new google.maps.InfoWindow({
	    	content: content_str,
  		});
		infowindow.open(map, me);
		info_window_open = infowindow;
	}

	function click_list_item(){
		var me = this;
		var id = this.id;
		var row_id = "#row_" + id;
		if(event.target.nodeName !== "AREA"){
			return;
		}
		$(row_id).trigger('click');
		center_on_list_item(row_id)

	}

	function call_flickr(){
		var me = this;
		var id = me.id;
		var latitude = this.latitude;
		var longitude = this.longitude;
		var params = {lat: latitude, lon: longitude, format: "json", nojsoncallback: "?"};
		build_request("flickr.php", params, call_flickr_farm, me);
	}

	function center_on_list_item(id){
		// to cause a scroll
		($(id)[0]).scrollIntoView();
	}

	function center_on_map_item(){
		// to cause a scroll
		var new_id = this.id.split("_")[1];
		$(new_id)[0].scrollIntoView();
	}

	function recenter_on_center_of_circle(){
		map.fitBounds(bounds);
		map.panToBounds(bounds);
	}

	function add_markers(list){
		var ctr = 0;
		var lat;
		var lon;
		for (var key in list) {
		    if (list.hasOwnProperty(key)) {
		    	lat = parseFloat(list[key]['latitude']);
		    	lon = parseFloat(list[key]['longitude']);
				add_marker( lat, lon, ctr );
				ctr++;
		    }
		}
	}

	function add_marker(lat, lon, position){
		var marker = new google.maps.Marker({
			map: map,
			draggable: false,
			animation: google.maps.Animation.DROP,
			position: {lat: lat, lng: lon},
			id: position,
			latitude: lat,
			longitude: lon
		});
		marker.addListener('click', click_list_item);
		marker.addListener('click', call_flickr);
		markers.push(marker);
	}

	function clear_markers(){
    	while (markers.length > 0) {
        	markers.pop().setMap(null);
    	}
    	markers.length = 0;
	}

	var map;
	var bounds;
	// initialize
	function initMap() {
		map = new google.maps.Map(document.getElementById('map'), {
		  	zoom: 11,
		  	center: new google.maps.LatLng(32.253,-110.912),
		  	mapTypeId: 'terrain'
		});
	}

	// initial query should always be for the first twenty (onload)
	function query_db_for_pt(){
		$('#paginate')[0].selectedIndex = 0;
      	var curr_val = intFromField("distance");
      	var cache_type = intFromField("cache_type");
      	var difficulty = intFromField("difficulty");
      	var rad = 1609.34 * curr_val;
      	var newPos = {lat: floatFromField("latitude"), lng: floatFromField("longitude")};
      	zone = new google.maps.Circle({center: newPos, radius: rad}); 
      	bounds = zone.getBounds();
		maxLat = bounds.getNorthEast().lat(); 
		minLat = bounds.getSouthWest().lat(); 
		maxLng = bounds.getNorthEast().lng(); 
		minLng = bounds.getSouthWest().lng();
		params = {  maxLat: maxLat.toString(), 
					minLat: minLat.toString(), 
					maxLng: maxLng.toString(), 
					minLng: minLng.toString(),
					cache_type: cache_type.toString(),
					difficulty: difficulty.toString()
				};
		params_cnt = $.extend({}, params);
		params_cnt.count_only = 'count_only';
		console.log("running db ajax");
		build_request("db.php", params_cnt, geocache_query_cb);

		params_limit = $.extend({}, params);
		params_limit.lower_lim = 0;
		params_limit.upper_lim = 20;

		build_request("db.php", params_limit, geocache_limit_query_cb);
  	}

  	// lots of duplication here but at least it's working!
  	// probably needs a nice refactor
	function paginate_query_db_for_pt(elem){
      	var curr_val = intFromField("distance");
      	var cache_type = intFromField("cache_type");
      	var difficulty = intFromField("difficulty");
      	var rad = 1609.34 * curr_val;
      	var newPos = {lat: floatFromField("latitude"), lng: floatFromField("longitude")};
      	zone = new google.maps.Circle({center: newPos, radius: rad}); 
      	bounds = zone.getBounds();
		maxLat = bounds.getNorthEast().lat(); 
		minLat = bounds.getSouthWest().lat(); 
		maxLng = bounds.getNorthEast().lng(); 
		minLng = bounds.getSouthWest().lng();
		params = {  maxLat: maxLat.toString(), 
					minLat: minLat.toString(), 
					maxLng: maxLng.toString(), 
					minLng: minLng.toString(),
					cache_type: cache_type.toString(),
					difficulty: difficulty.toString()
				};
		console.log("running db ajax for paginate");
		params_limit = $.extend({}, params);

		var index = elem.selectedIndex;

		// these are poorly named really the upper limit is a "choose" parameter
		// it effectively should say start index choose 20 or something like that in SQL you can
		// be explicit by saying limit 20 offset 0 or limit 20 offset 200 (depending on what was selected)
		pager.curr_set_lower_lim = ((index) * 20);
	    pager.curr_set_upper_lim = 20;

		params_limit.lower_lim = pager.curr_set_lower_lim;
		params_limit.upper_lim = pager.curr_set_upper_lim;

		build_request("db.php", params_limit, geocache_limit_query_cb);
  	}

  	// prevents circular recursion
  	function add_list_listeners(){
  		$("#location_list").on('click', function (e){
  			// ensure the link was clicked, not the body
  			if(e.target.nodeName !== "A"){
  				return;
  			}
			fire_marker_event(e);
  		});
  		
  	}

  	function fire_marker_event(e){
		var id = this.event.target.id.split("row_")[1];
		google.maps.event.trigger(markers[id], 'click');
  	}

	function intFromField(field){
      	return parseInt( $("#" + field ).val() );
	}

    function floatFromField(field){
      	return parseFloat( $("#" + field ).val() );
	}