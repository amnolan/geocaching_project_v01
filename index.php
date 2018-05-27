<!doctype html>
<?php
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
?>
<html lang="en">
	<head>
		<!-- this includes all imports including my javascript code -->
		<?php include 'head.php'?>
		<!-- favicon -->
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
	</head>
	<body class="background_color_azure">
		<!-- <script src="bootstrap_jquery_import.js"></script> -->
		<div class="container">
			<!-- <p>
			  <a id="expander_effect" class="btn btn-primary hide_it" data-toggle="collapse" href="#map_div" role="button" aria-expanded="false" aria-controls="map_div" >Toggle first element</a>
			</p>
			<div class="collapse multi-collapse" id="map_div">
  			<div class="card card-body"> -->

			<div id="jt" class="jumbotron jumbotron-fluid background_color_blue_font_white text_color_ivory">
			    <h1>Geocache Adventure</h1>      
			    <p>Set off on your own geocaching adventure below!</p>

		  	</div>
		  	<span class="body_text">
				<div class="col-md-5">
					<form id="form" onsubmit="query_db_for_pt();return false;">
						<div class="form-group prevent_pagination_change_query">
							<label for="latitude">Latitude</label>
							<input type="number" step="any" class="form-control" id="latitude" placeholder="Latitude" >
						</div>
						<div class="form-group prevent_pagination_change_query">
							<label for="longitude">Longitude</label>
							<input type="number" step="any" class="form-control" id="longitude" placeholder="Longitude" >
						</div>
						<div class="form-group prevent_pagination_change_query">
							<label for="distance">Distance in Miles</label>
							<select class="form-control" id="distance">
								<option>5</option>
								<option selected="selected">10</option>
								<option>15</option>
								<option>20</option>
								<option>25</option>
								<option>30</option>
								<option>35</option>
								<option>40</option>
								<option>45</option>
								<option>50</option>
								<option>55</option>
								<option>60</option>
								<option>65</option>
								<option>70</option>
								<option>75</option>
								<option>80</option>
								<option>85</option>
								<option>90</option>
								<option>95</option>
								<option>100</option>
								<option>105</option>
								<option>110</option>
								<option>115</option>
								<option>120</option>
								<option>125</option>
								<option>130</option>
								<option>135</option>
								<option>140</option>
								<option>145</option>
								<option>150</option>
								<option>155</option>
								<option>160</option>
								<option>165</option>
								<option>170</option>
								<option>175</option>
								<option>180</option>
								<option>185</option>
								<option>190</option>
								<option>195</option>
								<option>200</option>
							</select>
						</div>
						<div class="form-group prevent_pagination_change_query">
							<label for="cache_type">Cache Type</label>
							<select class="form-control" id="cache_type">
								<option value=0>Any</option>
								<option value=1>Traditional</option>
								<option value=2>Mystery/Puzzle</option>
								<option value=3>Multi-Cache</option>
							</select>
						</div>
						<div class="form-group prevent_pagination_change_query">
							<label for="difficulty">Difficulty</label>
							<select class="form-control" id="difficulty">
								<option value=0>All</option>
								<option>1</option>
								<option>2</option>
								<option>3</option>
								<option>4</option>
								<option>5</option>
								<option>6</option>
								<option>7</option>
								<option>8</option>
								<option>9</option>
								<option>10</option>
							</select>
						</div>
						<div class="form-group">
							<button id="submit" type="submit" class="btn btn-outline-info">Submit</button>
						</div>
					</form>
			  		<div class="well well-md background_color_blue_font_white text_color_ivory">
			  			<h4 class="display-4 twenty_point_top_margin">
			  				<span class="glyphicon glyphicon-map-marker">
			  				</span>
			  				&nbsp;Locations
		  				</h4>
	  				</div>
				  	<div class="list-group" id="location_list">
					</div>
					<div class="form-group prevent_pagination_change_query_hide">
						<label for="paginate">Select a page</label>
						<select class="form-control" id="paginate">
						</select>
					</div>
				</div>
			    <div class="col-md-7">
			    <?php if(isMobile()){?>
			    <div id="scroll_map">
					<div id="map"></div>
				</div>
				<?php }else{ ?>
		    		<div id="scroll_map" data-spy="affix" data-offset-top="35" data-offset-bottom="340" >
						<div id="map"></div>
					</div>
				<?php }?>
		  		</div>
	  		</span>
		</div>
		<footer class="page-footer font-small blue-grey lighten-5 pt-0">
		    <div class="background_color_blue_font_white">
		        <div class="container">
		            <div class="row justify-content-md-center">
		                <div>
		                    <h2 class="mb-0 text_color_ivory text-center text-md-center">
		                        <span class="glyphicon glyphicon-tree-conifer">
		                        </span>
		                        <span class="glyphicon glyphicon-tree-deciduous">
		                        </span>
		                        <strong>Geocache Adventure</strong>
		                        <span class="glyphicon glyphicon-tree-deciduous">
		                        </span>
		                        <span class="glyphicon glyphicon-tree-conifer">
		                        </span>
		                    </h2>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="container mt-5 mb-4 text-center text-md-left">
		        <div class="row mt-3">
		            <div class="col-md-4 col-lg-4 col-xl-4 mb-4 dark-grey-text">
		                <h6 class="text-uppercase font-weight-bold">
		                    <strong>Geocache Adventure</strong>
		                </h6>
		                <hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
		                <p>Geocaching is an inherently adventurous activity. Be smart, when you go out, bring plenty of water with you and tell someone where you're going and when you'll be back. Remember you do it at your own risk. Geocaching Adventure &copy; may not be held responsible for your choices. Stay safe, but most of all have fun. We take donations because we love money. Geocaching Adventures is a wholly owned subsidary of Reynholm Industries&trade;</p>
		            </div>
		            <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4 dark-grey-text">
		                <h6 class="text-uppercase font-weight-bold">
		                    <strong>Company</strong>
		                </h6>
		                <hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
		                <p>
		                    <a href="https://en.wikipedia.org/wiki/The_IT_Crowd" class="dark-grey-text" target="_blank" >Find out more about our mission</a>
		                </p>
		                <p>
		                    <a href="https://www.youtube.com/watch?v=nn2FB1P_Mn8" class="dark-grey-text" target="_blank" >IT Issues</a>
		                </p>
		                <p>
		                    <a href="https://youtu.be/bzv4R0S7tmk?t=49" class="dark-grey-text" target="_blank" >Visit our affiliates</a>
		                </p>
		                <p>
		                    <a href="http://theitcrowd.wikia.com/wiki/The_IT_Crowd_Wiki" target="_blank" class="dark-grey-text">Help</a>
		                </p>
		            </div>
		            <div class="col-md-4 col-lg-4 col-xl-4 dark-grey-text">
		                <h6 class="text-uppercase font-weight-bold">
		                    <strong>Contact</strong>
		                </h6>
		                <hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
		                <p>
		                    <i class="fa fa-home mr-3"></i>London E14 9JW, UK
		                </p>
		                <p>
		                    <i class="fa fa-home mr-3"></i>Trinity Tower, C, 28 Quadrant Walk, Isle of Dogs
		                </p>
		                <p>
		                    <i class="fa fa-home mr-3"></i>Basement Level 2
		                </p>
		                <p>
		                    <i class="fa fa-home mr-3"></i><a href="http://theitcrowd.wikia.com/wiki/The_IT_Crowd_Wiki" target="_blank">Reynholm Industries Corporate Website</a>
		                </p>
		                <p>
		                    <i class="fa fa-envelope mr-3"></i> <a href="#email" id="email"/> RayTrennenman@example.com</a>
		                </p>
		            </div>
		        </div>
		    </div>
		</footer>      
	</body>
</html>