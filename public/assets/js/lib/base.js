// Find organizations
$.getInfo('find_organizations', { 'author' : 'GitFloat'}, function(data) {
	$("#organizations").html(data);
	if($("#repos option:selected").html() != ' --- ') {
		$.getInfo('find_repos', { 'org' : $('#organizations').val(), 'author' : 'GitFloat' }, function(data) {
			$("#repos").html(data);
		});
	}
});

// When an organization changes, save it to the session
$("#organizations").on("change", function(e) {
	var org = $(this).val();
	$.getInfo('set_organization', { 'org' : org, 'author' : 'GitFloat' });
	$("#repos").html("<option>Loading</option>");
	// Also find the repos to the new org
	$.getInfo('find_repos', { 'org' : org, 'author' : 'GitFloat' }, function(data) {
		$("#repos").html(data);
	});
});

// Set the repo when it changes
$("#repos").on("change", function(e) {
	$.getInfo('set_repo', { 'repo' : $(this).val(), 'author' : 'GitFloat' });
});