<?php
// Start the session
session_start();
if (isset($_SESSION['user'])) header('location: dashboard.php');

$error_message = '';

if ($_POST) {
	include('database/connection.php');

	$username = $_POST['username'];
	$password = $_POST['password'];

	$query = 'SELECT * FROM users WHERE users.email=:username AND users.password=:password LIMIT 1';
	$stmt = $conn->prepare($query);
	$stmt->bindParam(':username', $username);
	$stmt->bindParam(':password', $password);
	$stmt->execute();

	if ($stmt->rowCount() > 0) {
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$user = $stmt->fetchAll()[0];

		// Capture data of currently login users.
		$_SESSION['user'] = $user;

		header('Location: dashboard.php');
	} else $error_message = 'Please make sure that username and password are correct.';
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>BSM Login - Book Store Management System</title>
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body id="loginBody">
	<?php if (!empty($error_message)) { ?>
		<div id="errorMessage">
			<strong>ERROR:</strong></p><?= $error_message ?> </p>
		</div>
	<?php } ?>
	<div class="container">
		<div class="loginHeader">
			<h1>BSM</h1>
			<p>Book Store Management System</p>
		</div>
		<div class="loginBody">
			<form action="login.php" method="POST">
				<div class="loginInputsContainer">
					<label for="username">Username</label>
					<input placeholder="username" name="username" id="username" type="text" />
				</div>
				<div class="loginInputsContainer">
					<label for="password">Password</label>
					<input placeholder="password" name="password" id="password" type="password" />
				</div>
				<div class="loginButtonContainer">
					<button>Login</button>
				</div>
			</form>
		</div>
	</div>
</body>

</html>