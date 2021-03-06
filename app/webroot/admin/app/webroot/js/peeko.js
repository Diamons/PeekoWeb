$(function(){
	$("body").show();
	var serverUrl = "http://direct.theboxngo.com:8080/";
	var markers = [];
	var firstRun = true;
	var shareHandler;
	
	var mapOptions = {
		center: new google.maps.LatLng(120, 60),
		zoom: 14,
		disableDefaultUI: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(120, 60),
		map: map,
		icon: {
			url: serverUrl+'img/me.png',
			scaledSize: new google.maps.Size(24,28),
			animation: google.maps.Animation.DROP
		}
	});
	
	var userMarker = new google.maps.Marker({
		position: new google.maps.LatLng(120, 60), 
		map: map
	});
	
	startCountdown();
	
	// Wait for device API libraries to load
    //
    document.addEventListener("deviceready", onDeviceReady, false);
	
	//When device is ready, do onSuccess.
	function onDeviceReady(){
		navigator.geolocation.getCurrentPosition(onSuccess, onError);
	}
	
	function onSuccess(position){
		try{
			centerMap(position.coords.latitude, position.coords.longitude, firstRun);
			if(firstRun){
				firstRun = false;
			}
		}catch(error){
			alert(error);
		}
	}
	
	function onError(error) {
		alert("Unfortunately it looks like you're not connected. You will need to have internet access and allow us to use your location for Peeko to work.");
	}
	
	//Show an overlay.
	function showOverlay(marker, data){
		$("#productName").text(data.name);
		$("#description").html(data.description);
		$("#price").html(data.price);
		$("#buyOnline").attr('href', data.url);
		$("#productImage").attr('src', data.images);
		$("#productOverlay").fadeIn(400);
		shareHandler = $("#share").click(function(e){
			e.preventDefault();
			try{
				window.plugins.socialsharing.share('$'+data.price+' - '+data.name, null, data.images, data.url);
			}catch(e){
				alert(e);
			}
		});
	}
	
	//Add a marker
	function addMarker(longitude, latitude, data){
		var tmpMarker = new google.maps.Marker({
			position: new google.maps.LatLng(latitude, longitude),
			map: map,
			icon: {
				url: serverUrl+'brands/'+data.active_block['icon'],
				scaledSize: new google.maps.Size(80,100),
				zIndex: 1,
			}
		});
		google.maps.event.addListener(tmpMarker, 'click', function() { 
			if(tmpMarker.getZIndex() != 10){
				for(i = 0; i < markers.length; i++){
					markers[i].setZIndex(1);
				}
				 tmpMarker.setZIndex(10);
			}
			showOverlay(tmpMarker, data['active_block']);
		});
	
		markers.push(tmpMarker);
	}
	
	function centerMap(latitude, longitude, moveMap){
		var geo = new google.maps.LatLng(latitude, longitude);
		if(moveMap){
			map.setCenter(geo);
			marker.setPosition(geo);
		}else{
			map.setZoom(15);
		}
		$.ajax({
			url: serverUrl+'blocks/'+longitude+'/'+latitude,
			success: function(response){
				for(i = 0; i < response.length; i++){
					addMarker(response[i].loc[0], response[i].loc[1], response[i]._brand[0]);
				}
			}
		});
	}
	
	function placeGenericMarker(location){
		userMarker.setPosition(location);
		centerMap(location.lat(), location.lng(), false);
	}
	
	google.maps.event.addListener(map, 'click', function(event) {
		placeGenericMarker(event.latLng);
	});
	
	function resizeOverlay(){
		var width = $(window).width();
		var height = $(window).height();
		$("#productOverlay").parent().width(width).height(height);
	}
	
	function startCountdown(){
		var countdown = new Date();
		var targetHour = 0;
		if((countdown.getHours() >= 7) && (countdown.getHours() < 14)){
			targetHour = 14;
		}else{
			targetHour = 7;
		}
		countdown.setHours(targetHour,0,0,0);
		$('#countdown').countdown({until: countdown}); 
	}
	
	$("#close").click(function(){
		$("#productOverlay").fadeOut(400);
	});
	
	$("#buyOnline").click(function(event){
		event.preventDefault();
		var ref = window.open($(this).attr('href'), '_blank', 'location=yes');
	});
	
	$("#locateMe").on("click", function(event){
		event.preventDefault();
		firstRun = true;
		map.setZoom(16);
		navigator.geolocation.getCurrentPosition(onSuccess, onError);
	});
	
	$(window).bind('orientationchange resize', function(event,ui){
		resizeOverlay();
	});
});