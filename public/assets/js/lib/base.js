$.getInfo('find_organizations', { 'author' : 'GitFloat'}, function(data) {
	$("#organizations").html(data);
	if($("#repos option:selected").html() != ' --- ') {
		$.getInfo('find_repos', { 'org' : $('#organizations').val(), 'author' : 'GitFloat' }, function(data) {
			$("#repos").html(data);
		});
	}
});

$("#organizations").on("change", function(e) {
	var org = $(this).val();
	$.getInfo('set_organization', { 'org' : org, 'author' : 'GitFloat' });
	$("#repos").html("<option>Loading</option>");

	$.getInfo('find_repos', { 'org' : org, 'author' : 'GitFloat' }, function(data) {
		$("#repos").html(data);
	});
});

$("#repos").on("change", function(e) {
	$.getInfo('set_repo', { 'repo' : $(this).val(), 'author' : 'GitFloat' });
});