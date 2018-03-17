rcmail.addEventListener('init', function(){

	var img = document.getElementsByTagName('img');

	for (var i = 0; i < img.length; i++) {
		if ( img[i].src.indexOf("https://" + window.location.hostname) === -1 ) {
			img[i].src = ( "https://" + window.location.hostname + "/plugins/remote_proxy/proxy.php?proxyImg=" + img[i].src );
		}
	}
});
