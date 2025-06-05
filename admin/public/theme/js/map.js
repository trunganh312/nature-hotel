//Tạo google map và tìm kiếm địa chỉ để lấy Lat Lng insert vào DB
function initAutocomplete() {
    
    //Tọa độ hiện tại (Mặc định là HN)
    const myLatLng  =   { lat: current_lat ? current_lat : 21.0227387, lng: current_lon ? current_lon : 105.8194541 };
    
    //Tạo map
    const map = new google.maps.Map(document.getElementById("map"), {
        center: myLatLng,
        zoom: 14,
        mapTypeId: "roadmap",
    });
    
    //Tạo Marker
    const marker = new google.maps.Marker({
        position: myLatLng,
        map,
        title: "Vị trí hiện tại đây!",
        draggable: true
    });
    
    //Event khi kéo marker thì sẽ lấy lat và lon
    marker.addListener("dragend", (e) => {
        var position = e.latLng.toJSON();
        get_position(position);
    });
    
    //Xử lý search autocomplete tìm kiếm địa chỉ trên map
    const input = document.getElementById("pac-input");
    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo("bounds", map);
    //Các dữ liệu sẽ lấy từ map để hiển thị vào ô thông tin của vị trí tìm thấy trên bảo đồ
    autocomplete.setFields([
      "address_components",
      "geometry",
      "icon",
      "name",
    ]);
    
    const infowindow = new google.maps.InfoWindow();
    const infowindowContent = document.getElementById("infowindow-content");
    infowindow.setContent(infowindowContent);
    
    autocomplete.addListener("place_changed", () => {
        
        //Ẩn tạm marker đi
        marker.setVisible(false);
        const place = autocomplete.getPlace();
        
        if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert(
                "Không tìm thấy địa chỉ bạn vừa nhập!"
            );
            return;
        }
        
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17); // Why 17? Because it looks good.
        }
        
        //Fill lại thông tin Lat Lng vào ô text box
        get_position(place.geometry.location);
        
        //Set lại marker
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        let address = "";
        
        if (place.address_components) {
            address = [
                (place.address_components[0] &&
                place.address_components[0].short_name) ||
                "",
                (place.address_components[1] &&
                place.address_components[1].short_name) ||
                "",
                (place.address_components[2] &&
                place.address_components[2].short_name) ||
                "",
            ].join(" ");
        }
        
        //Set lại các thông tin cho inforwindow của map
        infowindowContent.children["place-icon"].src = place.icon;
        infowindowContent.children["place-name"].textContent = place.name;
        infowindowContent.children["place-address"].textContent = address;
        infowindow.open(map, marker);
    });
    
}

/** Lấy thông tin Lat Lng để fill vào 2 ô text **/ 
function get_position(position) {
    $('#lat').val(position.lat);
    $('#lng').val(position.lng);
}