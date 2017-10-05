$(document).ready(function() {
var curfile = window.location.href ;
var curdir = curfile.substring(0, curfile.lastIndexOf('/'));
// alert(window.location.origin+'\n'+window.location.hostname+'\n'+curfile+'\n'+curdir);

init_tree(curdir);
init_map('#map');

$('#hu_list').DataTable( {
	searching: false,
	paging: false,
	dom: 'Bfrtip',
	buttons: ['excelHtml5','csvHtml5'],
	language : {
		"sProcessing":   "A processar...",
		"sLengthMenu":   "Mostrar _MENU_ registos",
		"sZeroRecords":  "Não foram encontrados resultados",
		"sInfo":         "Mostrando de _START_ até _END_ de _TOTAL_ registos",
		"sInfoEmpty":    "Mostrando de 0 até 0 de 0 registos",
		"sInfoFiltered": "(filtrado de _MAX_ registos no total)",
		"sInfoPostFix":  "",
		"sSearch":       "Procurar:",
		"sUrl":          "",
		"oPaginate": {
			"sFirst":    "Primeiro",
			"sPrevious": "Anterior",
			"sNext":     "Seguinte",
			"sLast":     "Último"
		}
	}
});

check_login();

$('#login_logon').dialog({ autoOpen: false, modal: true, minHeight: 200, minWidth: 200, position: { my: "left top", at: "left top", of: window } });
$('#form_edit').dialog({ autoOpen: false, modal: true });

//----------------------------------------------------------------------------------------------------------------------
// logout function
$('#hr').on('click', '#mfl_logout', function(){
	login_html = '<table><tbody>';
	login_html += '<tr><td>Acesso para área restrita</td></tr>';
	login_html += '<tr><td>Só para utentes cadastrados</td></tr>';
	login_html += '<tr><td><button id="mfl_login">Login</button></td></tr>';	
	login_html += '</tbody></table>';
    	$.ajax({
		url: curdir + '/logout.php',
		dataType: 'html',
		beforeSend: function(a){  },
		success: function(a){
            $('#hr').html(login_html);
        },
		error: function(a,b,c){ alert('erro ajax\na = ' + a.responseText + '\nb = ' + b + '\nc = ' + c ); },
		complete: function(a,b){  }
	});

});
//----------------------------------------------------------------------------------------------------------------------
// login function
$('#hr').on('click', '#mfl_login', function(){
	url = curdir + '/aut/login.php';
    $('#login_logon').html('<object type="text/html" data="' + url + '" style="height:500px;width:600px;"></object>');
    $('#login_logon').dialog( "open" );
});
//----------------------------------------------------------------------------------------------------------------------
function check_login() {
    	$.ajax({
		url: curdir + '/check_login.php',
		dataType: 'html',
		beforeSend: function(a){  },
		success: function(a){
            $('#hr').html(a);
        },
		error: function(a,b,c){ alert('erro ajax\na = ' + a.responseText + '\nb = ' + b + '\nc = ' + c ); },
		complete: function(a,b){  }
	});
}
//----------------------------------------------------------------------------------------------------------------------
// search results
$('#search_results').change(function(){
	var id = this.value;
	var txt = $(this).find('option:selected').text();
//	alert('id = '+ id + '\ntext = ' + txt); return;
	var map = init_map('#map');
	localise_unit_on_map(curdir,map,txt,id);
	init_form_elements(curdir,'#form_elements',id);
});
//----------------------------------------------------------------------------------------------------------------------
// search function
$('#hl img').click(function(){
	$('#search_results').children('option').remove();
	txt = $('#hl input').val();
    $.ajax({
		url: curdir + '/mfl_search.php',
        data: { mfl_search: txt },
		type: 'GET',
		dataType: 'html',
		beforeSend: function(a){  },
		success: function(a){
			if (a == 0) return;
			arr = $.parseJSON(a);
			$.each(arr, function(i, item) {
				$('#search_results').append( $('<option></option>').attr("value",item[0]).text(item[1]) ) ;
			});
			$('#search_results').removeAttr('disabled');
			var id = $('#search_results option:first').val();
			var txt = $('#search_results option:first').text();
			var map = init_map('#map');
			localise_unit_on_map(curdir,map,txt,id);
			init_form_elements(curdir,'#form_elements',id);
        },
		error: function(a,b,c){ alert('erro ajax\na = ' + a.responseText + '\nb = ' + b + '\nc = ' + c ); },
		complete: function(a,b){  }
	});
});
//----------------------------------------------------------------------------------------------------------------------
/*
$('#hu_list tr:first-child').click(function(){
	$("#hu_list").excelexportjs({
		containerid: "hu_list",
		datatype: 'table'
	});
});
*/
//----------------------------------------------------------------------------------------------------------------------
$('#hu_list td').click(function(){
	id = $(this).parent().children().eq(0).text();
	name = $(this).parent().children().eq(2).text();
//	alert('id = '+id+'\nname = '+ name);
	var map = init_map('#map');
	localise_unit_on_map(curdir,map,name,id);
	init_form_elements(curdir,'#form_elements',id);
});
//----------------------------------------------------------------------------------------------------------------------
function init_form_elements(curdir,div,unit_id) {
    	$.ajax({
		url: curdir + '/form_elements.php',
        data: { n: unit_id },
		type: 'GET',
		dataType: 'html',
		beforeSend: function(a){  },
		success: function(a){
            $('#form_elements table tbody').html(a);
        },
		error: function(a,b,c){ alert('erro ajax\na = ' + a.responseText + '\nb = ' + b + '\nc = ' + c ); },
		complete: function(a,b){  }
	});
}
//----------------------------------------------------------------------------------------------------------------------
function localise_unit_on_map(curdir,map,unit_name,unit_id) {
    $.ajax({
		url: curdir + '/mfl_coord.php',
        data: { n: unit_id },
		type: 'GET',
		dataType: 'html',
		beforeSend: function(a){  },
		success: function(a){
            var latlon = a.split(';');
            
            if (latlon[0] == 0 || latlon[1] == 0) return false;
            
            mark(map, latlon[0], latlon[1], unit_name);
		},
		error: function(a,b,c){ alert('erro ajax\na = ' + a.responseText + '\nb = ' + b + '\nc = ' + c ); },
		complete: function(a,b){  }
	});
}
//----------------------------------------------------------------------------------------------------------------------
function mark(map, lat, lon, title) {
	var myLatLng = new google.maps.LatLng(lat, lon);
	var marker = new google.maps.Marker({
		position: myLatLng,
		title: title,
		label: title
	});
	marker.setMap(map);
    latlngbounds = new google.maps.LatLngBounds();
    latlngbounds.extend(myLatLng);
    map.setCenter(latlngbounds.getCenter());
    map.fitBounds(latlngbounds);
    if (map.getZoom() > 10) map.setZoom(10);
}
//----------------------------------------------------------------------------------------------------------------------
function init_map(div) {
	var map = new google.maps.Map($(div)[0], {
		zoom: 6,
		center: new google.maps.LatLng(-19,36),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	return map;
}
//----------------------------------------------------------------------------------------------------------------------
function init_tree(url) {
	$.ajax({
		url: url+ '/mfl_tree.php',
		type: 'GET',
		dataType: 'html',
		beforeSend: function(a){  },
		success: function(a){
			$('#tree').html(a);
			$('li').on("click", function (e) {
				e.stopPropagation();
				$(this).children('ul').toggle('slow');
			});
			$('span').click(function(){
                var lugar = {};
				var huup_t = $(this).parent().parent().parent().parent().parent().children('span').text();
				var huup_i = $(this).parent().parent().parent().parent().parent().children('span').attr('id');
				var hup_t = $(this).parent().parent().parent().children('span').text();
				var hup_i = $(this).parent().parent().parent().children('span').attr('id');
                var h_t = $(this).text();
                var h_i = this.id;
                if (huup_t == '' && hup_t == '' ) {
                    lugar.provname = h_t;
                    lugar.provid = h_i;
                    lugar.distname = '';
                    lugar.distid = '';
                    lugar.unitname = '';
                    lugar.unitid = '';
                }
                if (huup_t == '' && hup_t != '' ) {
                    lugar.provname = hup_t;
                    lugar.provid = hup_i;
                    lugar.distname = h_t;
                    lugar.distid = h_i;
                    lugar.unitname = '';
                    lugar.unitid = '';
                }
                if (huup_t != '' && hup_t != '' ) {
                    lugar.provname = huup_t;
                    lugar.provid = huup_i;
                    lugar.distname = hup_t;
                    lugar.distid = hup_i;
                    lugar.unitname = h_t;
                    lugar.unitid = h_i;

					var map = init_map('#map');
					localise_unit_on_map(curdir,map,h_t,h_i.substr(2));
					init_form_elements(curdir,'#form_elements',h_i.substr(2));

                }
                var row = $('#tabinfo').children('tbody').eq(0).children('tr');
				$(row).eq(0).children('td').eq(1).text(lugar.provname);
				$(row).eq(0).children('td').eq(2).text(lugar.provid);
				$(row).eq(1).children('td').eq(1).text(lugar.distname);
				$(row).eq(1).children('td').eq(2).text(lugar.distid);
				$(row).eq(2).children('td').eq(1).text(lugar.unitname);
				$(row).eq(2).children('td').eq(2).text(lugar.unitid);
			});
		},
		error: function(a,b,c){ alert('erro ajax\na = ' + a.responseText + '\nb = ' + b + '\nc = ' + c ); },
		complete: function(a,b){  }
	});
}
//----------------------------------------------------------------------------------------------------------------------
/*
Google Map API key
AIzaSyCIeZTMfIeXUkueMevGm5dizgIcmCFkKRo

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCIeZTMfIeXUkueMevGm5dizgIcmCFkKRo&callback=initMap"
    async defer></script>

Cookies

https://github.com/carhartl/jquery-cookie#readme
Create session cookie:
$.cookie('name', 'value');
Create expiring cookie, 7 days from then:
$.cookie('name', 'value', { expires: 7 });
Create expiring cookie, valid across entire site:
$.cookie('name', 'value', { expires: 7, path: '/' });
Read cookie:
$.cookie('name'); // => "value"
$.cookie('nothing'); // => undefined
Read all available cookies:
$.cookie(); // => { "name": "value" }
Delete cookie:
// Returns true when cookie was successfully deleted, otherwise false
$.removeCookie('name'); // => true
    
*/
//----------------------------------------------------------------------------------------------------------------------

}); // $
