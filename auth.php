<?php
session_start();

// Vos informations d'application Discord
$clientId = "1102573935658283038";
$clientSecret = "zr9460qWoo4NjrClORveZK5EQTv9i3O4";
$redirectUri = "https://phptests.vercel.app/auth.php";

// URL d'autorisation Discord
$authUrl = "https://discord.com/api/oauth2/authorize?client_id=$clientId&redirect_uri=$redirectUri&response_type=code&scope=identify";

// Gestion de la redirection
if(isset($_GET['code'])) {
    // Récupérer le code d'autorisation
    $code = $_GET['code'];

    // Obtenir le jeton d'accès avec le code
    $tokenUrl = "https://discord.com/api/oauth2/token";
    $tokenData = array(
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirectUri
    );

    $tokenOptions = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($tokenData),
        ),
    );

    $tokenContext = stream_context_create($tokenOptions);
    $tokenResult = file_get_contents($tokenUrl, false, $tokenContext);
    $tokenJson = json_decode($tokenResult, true);

    // Utiliser le jeton d'accès pour obtenir les informations utilisateur
    if(isset($tokenJson['access_token'])) {
        $userInfoUrl = "https://discord.com/api/users/@me";
        $userInfoOptions = array(
            'http' => array(
                'header' => "Authorization: Bearer " . $tokenJson['access_token'],
            ),
        );

        $userInfoContext = stream_context_create($userInfoOptions);
        $userInfoResult = file_get_contents($userInfoUrl, false, $userInfoContext);
        $userInfo = json_decode($userInfoResult, true);

        // Afficher les informations de l'utilisateur
        if(isset($userInfo['username'])) {
            echo "<h2>Bienvenue, " . $userInfo['username'] . "!</h2>";
            echo "<pre>";
            print_r($userInfo);
            echo "</pre>";
        }
    }
} else {
    // Rediriger vers l'URL d'autorisation Discord
    header("Location: $authUrl");
    exit();
}
?>