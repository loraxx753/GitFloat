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

if(localStorage.commitRegex) {
	$("#commitRegex").val(localStorage.commitRegex);
}
$(".commit-audit-save-regex").popover({
	trigger : 'manual',
	content : 'Saved!',
	delay   : { 'show' : 0, 'hide' : 500} 
});
$(".commit-audit-save-regex").on('click', function(e) {
	$this = $(this);
	localStorage.setItem('commitRegex', $("#commitRegex").val());
	$this.popover('show');
	setTimeout(function(){ $this.popover('hide'); }, 800);
});