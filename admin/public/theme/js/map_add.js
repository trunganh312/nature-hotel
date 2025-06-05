	var drawingManager;
	var selectedShape;
	var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
	var selectedColor;
	var colorButtons = {};
	var arr_color = ['#00FFFF', '#000000', '#0000FF', '#8A2BE2', '#A52A2A', '#DEB887', '#5F9EA0', '#7FFF00', '#D2691E', '#6495ED', '#DC143C', '#B8860B', '#A9A9A9', '#006400', '#BDB76B', '#8B008B', '#556B2F', '#FF8C00'];
	var color_real = '';

	function clearSelection() {
		if (selectedShape) {
			selectedShape.setEditable(false);
			selectedShape = null;
		}
	}

	function setSelection(shape) {
		clearSelection();
		selectedShape = shape;
		shape.setEditable(true);
		selectColor(shape.get('fillColor') || shape.get('strokeColor'));
	}

	function deleteSelectedShape() {
		if (selectedShape) {
			selectedShape.setMap(null);
		}
	}

	function selectColor(color) {
		selectedColor = color;
		for (var i = 0; i < colors.length; ++i) {
		var currColor = colors[i];
		colorButtons[currColor].style.border = currColor == color ? '2px solid #789' : '2px solid #fff';
		}

		// Retrieves the current options from the drawing manager and replaces the
		// stroke or fill color as appropriate.
		var polylineOptions = drawingManager.get('polylineOptions');
		polylineOptions.strokeColor = color;
		drawingManager.set('polylineOptions', polylineOptions);

		var rectangleOptions = drawingManager.get('rectangleOptions');
		rectangleOptions.fillColor = color;
		drawingManager.set('rectangleOptions', rectangleOptions);

		var circleOptions = drawingManager.get('circleOptions');
		circleOptions.fillColor = color;
		drawingManager.set('circleOptions', circleOptions);

		var polygonOptions = drawingManager.get('polygonOptions');
		polygonOptions.fillColor = color;
		drawingManager.set('polygonOptions', polygonOptions);
	}

	function setSelectedShapeColor(color) {
		if (selectedShape) {
			if (selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {
				selectedShape.set('strokeColor', color);
			} else {
				selectedShape.set('fillColor', color);
			}
		}
	}

	function makeColorButton(color) {
		var button = document.createElement('span');
		button.className = 'color-button';
		button.style.backgroundColor = color;
		google.maps.event.addDomListener(button, 'click', function() {
			selectColor(color);
			setSelectedShapeColor(color);
		});

		return button;
	}

	function buildColorPalette() {
		var colorPalette = document.getElementById('color-palette');
		for (var i = 0; i < colors.length; ++i) {
			var currColor = colors[i];
			var colorButton = makeColorButton(currColor);
			colorPalette.appendChild(colorButton);
			colorButtons[currColor] = colorButton;
		}
		selectColor(colors[0]);
	}

	google.maps.event.addDomListener(window, 'load', function(){
		initialize();
	});

// Edit by Mr.P
function addPosition(value, index){

	var table      =  "<table class='form_table'>";
	var namepos =  "<tr align='center'><td class='form_text'><input type='text' id='pos_name' class='form_control name_value' name='pos_name[]' value='" + index + "' title='Tên gọi' maxlength='150' style='width: 100px;' /></td></tr>";
	var tr         =  "<tr><td class='form_text'>";
	var input_lat  =  "<input type='text' id='pos_lat' class='form_control lat_value' name='pos_lat[]' value='" + value.lat() + "' readonly maxlength='255' style='width: 100px;' title='Vĩ độ' />";
	var input_lng  =  "<input type='text' id='pos_lng' class='form_control lng_value' name='pos_lng[]' value='" + value.lng() + "' readonly maxlength='255' style='width: 100px;' title='Kinh độ' />";
	$("#posremove", window.parent.document).remove();
	$("#positions", window.parent.document).append("<li class='wapper_position' data_id='" + index + "'>" + table + namepos + tr + input_lat + "</td></tr>" + tr + input_lng + "</td></tr></table></li>");
}

function editPosition(value, index){

	var table      =  "<table class='form_table'>";
	var namepos =  "<tr align='center'><td class='form_text'><input type='text' id='pos_name' class='form_control name_value' name='pos_name[]' value='" + index + "' title='Tên gọi' maxlength='150' style='width: 100px;' /></td></tr>";
	var tr         =  "<tr><td class='form_text'>";
	var input_lat  =  "<input type='text' id='pos_lat' class='form_control lat_value' name='pos_lat[]' value='" + value.lat() + "' readonly maxlength='255' style='width: 100px;' title='Vĩ độ' />";
	var input_lng  =  "<input type='text' id='pos_lng' class='form_control lng_value' name='pos_lng[]' value='" + value.lng() + "' readonly maxlength='255' style='width: 100px;' title='Kinh độ' />";
	$("#posremove", window.parent.document).remove();
	$(".wapper_position[data_id=" + index + "]", window.parent.document).html(table + namepos + tr + input_lat + "</td></tr>" + tr + input_lng + "</td></tr></table>");
}

function insertPosition(value, index, length){
	for (var i = length-1; i >= index; i--) {
		$(".wapper_position[data_id=" + i + "] .name_value", window.parent.document).val(i+1);
		$(".wapper_position[data_id=" + i + "]", window.parent.document).attr("data_id", i+1);
	};

	var table      =  "<table class='form_table'>";
	var namepos =  "<tr align='center'><td class='form_text'><input type='text' id='pos_name' class='form_control name_value' name='pos_name[]' value='" + index + "' title='Tên gọi' maxlength='150' style='width: 100px;' /></td></tr>";
	var tr         =  "<tr><td class='form_text'>";
	var input_lat  =  "<input type='text' id='pos_lat' class='form_control lat_value' name='pos_lat[]' value='" + value.lat() + "' readonly maxlength='255' style='width: 100px;' title='Vĩ độ' />";
	var input_lng  =  "<input type='text' id='pos_lng' class='form_control lng_value' name='pos_lng[]' value='" + value.lng() + "' readonly maxlength='255' style='width: 100px;' title='Kinh độ' />";
	$(".wapper_position[data_id=" + (index-1) + "]", window.parent.document).after("<li class='wapper_position' data_id='" + index + "'>" + table + namepos + tr + input_lat + "</td></tr>" + tr + input_lng + "</td></tr></table></li>");
}

function removePosition(index, length){
	console.log(index);
	console.log(length);
	$(".wapper_position[data_id=" +  index + "]", window.parent.document).remove();
	for (var i = index+1; i <= length; i++) {
		console.log(i);
		$(".wapper_position[data_id=" + i + "] .name_value", window.parent.document).val(i-1);
		$(".wapper_position[data_id=" + i + "]", window.parent.document).attr("data_id", i-1);
	};
}

function deletePosition(){
	$("#positions", window.parent.document).empty();
	addDefault();

}

function addDefault(){
	var table      =  "<table class='form_table'>";
	var namepos =  "<tr align='center'><td class='form_text'><input type='text' id='pos_name' class='form_control' name='pos_name[]' maxlength='150' title='Tên gọi' placeholder='Tên gọi' style='width: 100px;' /></td></tr>";
	var tr         =  "<tr><td class='form_text'>";
	var input_lat  =  "<input type='text' id='pos_lat' class='form_control' name='pos_lat[]' value='' maxlength='255' title='Vĩ độ' placeholder='Vĩ độ' style='width: 100px;' />";
	var input_lng  =  "<input type='text' id='pos_lng' class='form_control' name='pos_lng[]' value='' maxlength='255' title='Kinh độ' placeholder='Kinh độ' style='width: 100px;' />";
	$("#positions", window.parent.document).append("<li id='posremove'>" + table + namepos + tr + input_lat + "</td></tr>" + tr + input_lng + "</td></tr></table></li>");
}

// function polygonCenter(poly) {
// 	var lowx,
// 	highx,
// 	lowy,
// 	highy,
// 	lats = [],
// 	lngs = [],
// 	vertices = poly.getPath();

// 	for(var i=0; i<vertices.length; i++) {
// 		lngs.push(vertices.getAt(i).lng());
// 		lats.push(vertices.getAt(i).lat());
// 	}

// 	lats.sort();
// 	lngs.sort();
// 	lowx = lats[0];
// 	highx = lats[vertices.length - 1];
// 	lowy = lngs[0];
// 	highy = lngs[vertices.length - 1];
// 	center_x = lowx + ((highx-lowx) / 2);
// 	center_y = lowy + ((highy - lowy) / 2);
// 	return (new google.maps.LatLng(center_x, center_y));
// }

function addCenter(center, prefix){
	var center_lat = center.lat();
	var center_lng = center.lng();
	$("#" + prefix + "lat", window.parent.document).val(center_lat);
	$("#" + prefix + "lng", window.parent.document).val(center_lng);
}