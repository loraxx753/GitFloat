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
