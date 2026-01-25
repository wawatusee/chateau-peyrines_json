<?php
/**
 * Classe LoginUser
 * Gère l'authentification des utilisateurs en lisant les identifiants depuis un fichier JSON.
 * Utilise la vérification de mot de passe haché (password_verify) pour la sécurité.
 */
class LoginUser{
	// class properties
    
    /**
     * @var string Nom d'utilisateur soumis par le formulaire.
     */
	private $username;
    
    /**
     * @var string Mot de passe soumis par le formulaire.
     */
	private $password;
    
    /**
     * @var string Stocke les messages d'erreur à afficher à l'utilisateur.
     */
	public $error;
    
    /**
     * @var string Stocke les messages de succès. Non utilisé actuellement pour la connexion.
     */
	public $success;
    
    /**
     * @var string Chemin vers le fichier JSON de stockage des utilisateurs.
     */
	private $storage = "login.json";
    
    /**
     * @var array Tableau décodé contenant tous les utilisateurs stockés dans le JSON.
     */
	private $stored_users;

	// class methods
    
    /**
     * Constructeur de la classe. Initialise les propriétés et lance le processus de connexion.
     * * @param string $username Nom d'utilisateur.
     * @param string $password Mot de passe.
     */
	public function __construct($username, $password){
		$this->username = $username;
		$this->password = $password;
        
        // Lecture et décodage du fichier JSON de stockage
		$this->stored_users = json_decode(file_get_contents($this->storage), true);
        
        // Lance la tentative de connexion
		$this->login();
	}

    /**
     * Tente de connecter l'utilisateur en vérifiant les identifiants.
     * * Logique:
     * 1. Parcourt les utilisateurs stockés.
     * 2. Cherche une correspondance avec le nom d'utilisateur soumis.
     * 3. Si trouvé, vérifie le mot de passe haché (password_verify).
     * 4. En cas de succès, démarre la session, définit $_SESSION['user'] et redirige.
     * 5. En cas d'échec, définit un message d'erreur.
     * * @return string|void Retourne le message d'erreur ou rien en cas de succès et de redirection.
     */
	private function login(){
		foreach ($this->stored_users as $user) {
			if($user['username'] == $this->username){
				if(password_verify($this->password, $user['password'])){
					// Connexion réussie
					session_start();
					$_SESSION['user'] = $this->username;
					header("location: admin.php");
					exit();
				}
			}
		}
		
        // Si la boucle est terminée sans succès, les identifiants sont faux
		return $this->error = "Wrong username or password";
	}

}
?>