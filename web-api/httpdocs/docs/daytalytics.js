
function signRequest(url, secret) {
	if (secret == '' || secret == null || secret == undefined) {
		return '';
	}
	var apiUrl = url.split(SWAGGER.basePath), path = apiUrl.pop();
	var shaObj = new jsSHA('SHA-1', "TEXT");
	shaObj.setHMACKey(secret, "TEXT");
	shaObj.update(path);
	var hmac = shaObj.getHMAC("HEX");
	return hmac;
}


$.ajaxPrefilter(function( options ) {
	
	var uri = URI(options.url), query = uri.search(true), signature;
	uri.removeSearch("Auth-Token");
	uri.removeSearch("Auth-Secret");
	uri.removeSearch("time");
	uri.addSearch("time", moment().unix());
	signature = signRequest(uri.toString(), query['Auth-Secret']);
	
	options.url = uri.toString();
	
    if (!options.beforeSend) {
        options.beforeSend = function (xhr) { 
        	xhr.setRequestHeader('Accept', $('#content-type').val());
        	xhr.setRequestHeader('Api-Auth-Token', query['Auth-Token']);
            xhr.setRequestHeader('Api-Auth-Signature', signature);
        }
    }
});

$(function(){
	
	var options = 	'<option value="application/json" selected>application/json</option>\
					<option value="application/xml">application/xml</option>\
					<option value="text/html">text/html</option>\
					<option value="application/vnd.php-object">application/vnd.php-object</option>';
	
	$('#Parameters .panel-body hr').after('<label>content type</label><div><select class="input-sm" id="content-type">'+options+'</select></div><hr>');
	
});