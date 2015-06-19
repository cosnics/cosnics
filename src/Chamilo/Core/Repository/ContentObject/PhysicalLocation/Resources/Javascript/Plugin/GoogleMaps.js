var geocoder;
var map;

function initialize(myZoom) 
{
	geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = 
    {
      zoom: myZoom,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}

function codeAddress(address, title) 
{
	geocoder.geocode( { address: address},
		function(results, status) 
		{
			if (status == google.maps.GeocoderStatus.OK && results.length) 
			{
				// You should always check that a result was returned, as it is
				// possible to return an empty results object.
				if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
					map.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
						position: results[0].geometry.location,
						map: map,
						title: title + " - " + address
					});
				}
			} else 
			{
				alert("Geocode was unsuccessful due to: " + status);
			}
		}
	);
}

