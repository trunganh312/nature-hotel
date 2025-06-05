/** Show google Map API **/
function show_google_map(lat, lng) {
   var map;
	var mainmarker;
   var api_key = 'ABQIAAAAxX2OsBvLnA-d4iEXlVT8sRRXgDufPzUC4DRIwmB0_hQn_WYXlhRq1IFt3vqV6zLO3kBN7Nj5fhXtvA';
	var latlng = new google.maps.LatLng(lat, lng);
   
	var settings = {
			zoom: 13,
			center: latlng,
			mapTypeControl: true,
			mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};
   
	map = new google.maps.Map(document.getElementById("map_canvas"), settings);
	
	mainmarker	=	new google.maps.Marker({
		position: latlng,
		map: map,
		draggable: true
	});
	google.maps.event.addListener(mainmarker, 'dragend', function(){
		var poit = mainmarker.getPosition();
		$('#lat').val(poit.lat());
		$('#lng').val(poit.lng());
		map.setCenter(poit);
	});
	
	google.maps.event.addListener(map, 'dragend', function(){
		var center = map.getCenter();
		//alert(center);
		mainmarker.setPosition(center);
		$('#lat').val(center.lat());
		$('#lng').val(center.lng());
      
	});//end of event dragend for map
	
	$('#keyword').geo_autocomplete(new google.maps.Geocoder,{
		mapkey: api_key,
		width: 550,
		delay: 150,
		max: 13,
		selectFirst: true, 			 
		cacheLength: 50,
		scroll: true,
		scrollHeight: 330
	}).result(function(_event, _data) {
		if (_data) map.fitBounds(_data.geometry.viewport);
      mainmarker.setPosition(_data.geometry.location);
		
		$('#lat').val(_data.geometry.location.lat());
		$('#lng').val(_data.geometry.location.lng());
		
	});
}
/** Select position map **/
function select_position(){
	window.parent.document.getElementById("latitude").value = document.getElementById("lat").value;
	window.parent.document.getElementById("longitude").value = document.getElementById("lng").value;
	window.parent.closeModal();
}