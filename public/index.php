<?php 
session_start();
ini_set('display_errors',1);
date_default_timezone_set("America/Chicago");
define('ROOT_DIR', dirname(__FILE__)."/..");

if($_POST) { 
	require_once(ROOT_DIR.'/vendor/autoload.php');
	$processor = \GitFloat\Processor::forge();
	$call = "run_".$_POST['request'];
	unset($_POST['request']);
	try { 
		echo call_user_func_array(array($processor, $call), array_filter($_POST));
	} catch(Exception $e) {
		echo $processor->run_error_page($e);
	}
} else { ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title></title><!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/assets/css/styles.css">
	<link rel="stylesheet" href="/assets/css/styles.css">

	<!-- Latest compiled and minified JavaScript -->
</head>
<body role="document">

	<!-- Fixed navbar -->
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">GitFloat</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<?php if(!isset($_SESSION['access_token'])) { ?>
				<ul class="nav navbar-nav navbar-right">
					<li class="navbar-right"><a href="/login.php?action=login">Sign in</a></li>
				</ul>
				<?php } else { ?>
				<ul class="nav navbar-nav navbar-right">
					<li><p class="navbar-text"><?=$_SESSION['user']->name?></p></li>
					<li><a href="/login.php?action=logout">Logout</a></li>
				</ul>
				<?php } ?>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<div class="container theme-showcase" role="main">
	<div class="page-header">
	  <h1>GitFloat <small>Tearing it up since 2015</small></h1>
	</div>
<?php if(isset($_SESSION['access_token'])) { ?>
		<!-- Main jumbotron for a primary marketing message or call to action -->
		<div class="row">
			<div class="col-md-12">
				<form id="drilldown" class="form-inline text-center">
					<select class="form-control" id="organizations">
						<option> <?=(isset($_SESSION['organization']))?$_SESSION['organization']:'Loading'?> </option>
					</select> / <select class="form-control" id="repos">
						<option> <?=(isset($_SESSION['repo']))?$_SESSION['repo']:' --- '?> </option>
					</select>
				</form>
			</div>
		</div>
		<div class="row" id="topRow">
			<div class="col-md-4">
			<!-- <div class="col-md-12"> -->
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Commit Audit</h3>
					</div>
					<div class="panel-body">
						<p>Put in a date or an amount of time to see the amount of good commits and bad commits since then.</p>
						<form id="commit_audit">
							<div class="form-group">
								<label for="auditSinceBranch">Branch</label>
								<input type="text" class="form-control" id="auditSinceBranch" placeholder="dev">
							</div>
							<div class="form-group">
								<label for="commitRegex">Commit Regex</label>
								<input type="text" class="form-control" id="commitRegex" placeholder="/([A-Z]+\-[0-9]+)/">
							</div>
							<div class="form-group">
								<label for="auditSince">Audit Commits Since</label>
								<input type="text" class="form-control" id="auditSince" placeholder="08-23-2015 or 2 weeks ago">
							</div>
							<button type="submit" class="btn btn-primary btn-block">Analyze</button>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-4">
			<!-- <div class="col-md-12"> -->
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Difference between two Branches</h3>
					</div>
					<div class="panel-body">
						<p>Put in a branch and see the differences between that branch and another.</p>
						<form id="compare">
							<div class="form-group">
								<label for="compareCommitsFrom">Commits on branch:</label>
								<input type="text" class="form-control" id="compareCommitsFrom" placeholder="dev">
							</div>
							<div class="form-group">
								<label for="compareCommitsTo">That aren't on branch:</label>
								<input type="text" class="form-control" id="compareCommitsTo" placeholder="master">
							</div>
							<button type="submit" class="btn btn-primary btn-block">Compare</button>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Hotfix Audit</h3>
					</div>
					<div class="panel-body" id="hotfix_audit">
						<p>This will list any hotfixes that are on the production branch that aren't in the development branch</p>
						<div class="form-group">
							<label for="productionBranch">Production Branch:</label>
							<input type="text" class="form-control" id="productionBranch" placeholder="master">
						</div>
						<div class="form-group">
							<label for="developmentBranch">Development Branch:</label>
							<input type="text" class="form-control" id="developmentBranch" placeholder="dev">
						</div>
						<button type="submit" class="btn btn-primary btn-block">Show me some hotfixes!</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row loading">
			<h1 style="text-align: center;">
				<span class="glyphicon glyphicon-cog spin-glyph" aria-hidden="true"></span>
				<span class="glyphicon glyphicon-plus spin-glyph" aria-hidden="true"></span>
				<span class="glyphicon glyphicon-asterisk spin-glyph" aria-hidden="true"></span>
				<span class="glyphicon glyphicon-off spin-glyph" aria-hidden="true"></span>
				<span class="glyphicon glyphicon-fullscreen spin-glyph" aria-hidden="true"></span>
			</h1>
		</div>
		<div class='row' id='results'>
		</div>
<?php } else { ?>
	<div class="row">
		<div class="col-md-12">
			<h2>What is GitFloat?</h2>
			<p>GitFloat is a reporting applicaiton that takes your organizations GitHub Repositories and makes neet little reports with the commits.</p>

			<p>So far, the following widgets are available</p>

			<div class="list-group">
				<div class="list-group-item">
					<h4 class="list-group-item-heading">Commit Audit</h4>
					<p class="list-group-item-text">Feed this a regex and it will show you the commits that are following that regex and the commits that aren't. Pretty useful when you're commits need to match ticket numbers.</p>
				</div>
				<div class="list-group-item">
					<h4 class="list-group-item-heading">Branch Differ</h4>
					<p class="list-group-item-text">Takes two branches and highlights the commits that are on one, but not the other.</p>
				</div>
				<div class="list-group-item">
					<h4 class="list-group-item-heading">Hotfix Audit</h4>
					<p class="list-group-item-text">Feed your production branch and your development branch in and it will point out commits that are on your production branch but haven't yet been merged into your development branch.</p>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
	</div>
	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="/assets/js/lib/Widget.js"></script>
	<script src="/assets/js/dashboard.js"></script>

</body>
</html>
<?php } ?>