// OpenAM returns the URL parameters with a "#" (in PHP you need a "?"). 
// This function processes these parameters and converts them.

$(document).ready(function () {
	var postBody = location.hash.substring(1);
	var params = getParamsFromFragment(postBody);
	if (params.id_token != undefined && params.access_token != undefined) {
		window.location = "?"+location.hash.substring(1);
	}
});


function getParamsFromFragment(postBody) {
    var params   = {};
    var regex    = /([^&=]+)=([^&]*)/g, m;
    while (m = regex.exec(postBody)) {
        params[decodeURIComponent(m[1])] = encodeURI(decodeURIComponent(m[2]));
    }
    return params;
}