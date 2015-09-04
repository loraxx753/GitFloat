$.widget("commit_audit", ['auditSince', 'auditSinceBranch', 'commitRegex'], function() {
	$(".totalsHeader").on('click', function(e) {
		e.preventDefault();
		$(".details").slideToggle();
	});
	$(".details li .target-area").on("click", function(e) {
		e.preventDefault();
		$(this).parent().find(".individual-results").slideToggle();
	});
});
$.widget('hotfix_audit');
$.widget('compare', ['compareCommitsFrom', 'compareCommitsTo']);

$.getInfo('find_organizations', function(data) {
	$("#organizations").html(data);
	if($("#repos option:selected").html() != ' --- ') {
		$.getInfo('find_repos', { 'org' : $('#organizations').val() }, function(data) {
			$("#repos").html(data);
		});
	}
});

$("#organizations").on("change", function(e) {
	var org = $(this).val();
	$.getInfo('set_organization', { 'org' : org });
	$("#repos").html("<option>Loading</option>");

	$.getInfo('find_repos', { 'org' : org }, function(data) {
		$("#repos").html(data);
	});
});

$("#repos").on("change", function(e) {
	$.getInfo('set_repo', { 'repo' : $(this).val() });
});