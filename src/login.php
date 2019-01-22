<?php
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
	require_once('includes/db.php');

	$username = htmlspecialchars($_POST['username']);
	$password = htmlspecialchars($_POST['password']);

	$req = $db->prepare('SELECT `id`, `username`, `password`, `admin` FROM `user` WHERE username = :username');
	$req->execute(array('username' => $username));
	$data = $req->fetch();

	$isPasswordCorrect = password_verify($password, $data['password']);

	if ($data && $isPasswordCorrect) {
		$_SESSION['id'] = $data['id'];
		$_SESSION['username'] = $data['username'];
		$_SESSION['password'] = $data['password'];
		$_SESSION['admin'] = $data['admin'];

		// Update last login time
		$req = $db->prepare('UPDATE `user` SET `date_login` = CURDATE() WHERE id = :id');
		$req->execute(array('id' => $_SESSION['id']));

		// Approved chart
		$req = $db->prepare('SELECT `approved` FROM `chart` WHERE id_member = :id');
		$req->execute(array('id' => $_SESSION['id']));
		$chart = $req->fetch();

		$_SESSION['chart'] = $chart['approved'];
	} else {
		header("location:?error=1");
	}
}

if (isset($_SESSION['id']) AND isset($_SESSION['username']))
{
	header("location:/index.php");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Connexion - Marsoc</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="icon" href="assets/img/favicon.ico">
</head>

<body class="bg-dark">
	<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
		<img class="navbar-brand mx-auto" src="assets/img/marsoc.png" alt="Marsoc 123 Tristan Team">
	</nav>
	<section id="login">
		<div class="container">
			<form id="loginForm" class="p-4 mx-auto bg-dark-tr" method="POST">
				<div class="from-group mb-3">
					<label for="login-input"><i class="fas fa-user gold mr-1"></i> Nom de guerre</label>
					<input type="username" class="form-control" id="login-input" name="username" autofocus>
				</div>
				<div class="from-group mb-4">
					<label for="password"><i class="fas fa-key gold mr-1"></i> Mot de passe</label>
					<input type="password" class="form-control" id="password-input" name="password">
				</div>

				<button type="submit" class="btn btn-primary">Connexion <i class="fas fa-sign-in-alt"></i></button>
				<?php if ($_GET['error'] == 1): ?>
					<div class="p-2 mt-3 mb-0 alert alert-error" role="alert">
						Nom de guerre ou mot de passe incorrect
					</div>
				<?php endif; ?>
			</form>
		</div>
		<div id="bg-video" class="bg-video">
			<video autoplay="true" loop="true" muted="true"><source src="assets/video/marsoc-home.mp4" type="video/mp4"></video>
		</div>
	</section>
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/tether.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>