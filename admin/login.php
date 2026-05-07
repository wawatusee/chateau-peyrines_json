<?php
/*Ce fichier gère l'interface et le processus de connexion de l'administration :
Il utilise un fichier externe login.class.php (que nous devrons créer) pour la logique d'authentification.
Il inclut un formulaire HTML simple demandant un nom d'utilisateur (username) et un mot de passe (password).
Lors de la soumission du formulaire ($_POST['submit']), il instancie la classe LoginUser pour vérifier les identifiants.
Il affiche les messages d'erreur ou de succès renvoyés par l'objet $user ($user->error ou $user->success).*/
 require("login.class.php") ?>
<?php 
	if(isset($_POST['submit'])){
		$user = new LoginUser($_POST['username'], $_POST['password']);
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	 <link rel="stylesheet" href="styles.css">
	 <link rel="stylesheet" href="css/login.css">
	<title>Log in form</title>
</head>
<body>
	<form action="" method="post" enctype="multipart/form-data" autocomplete="off">
		<h2>Login form</h2>
		<h4>Both fields are <span>required</span></h4>

		<label>Username</label>
		<input type="text" name="username">

		<label>Password</label>
		<input type="text" name="password">

		<button type="submit" name="submit">Log in</button>

		<p class="error"><?php echo @$user->error ?></p>
		<p class="success"><?php echo @$user->success ?></p>
	</form>

</body>
</html>