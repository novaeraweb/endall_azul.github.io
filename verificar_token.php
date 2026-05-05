<?php
// Verificar se o token do reCAPTCHA invisível foi recebido
if(isset($_POST['token'])) {
    $token = $_POST['token'];
    
    // Chave secreta do reCAPTCHA invisível
    $chave_secreta = '6Lf3K6caAAAAAOaSJwiu_Y2aPHBZixSyTO5y9plB';

    // Enviar o token para o serviço do Google para verificação
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $chave_secreta,
        'response' => $token
    );
    
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    // Analisar a resposta do serviço do Google
    $response = json_decode($result);
    if($response->success) {
        // O token é válido, então você pode prosseguir com a ação desejada, como enviar a mensagem via WhatsApp
        echo json_encode(array('success' => true, 'message' => 'Token do reCAPTCHA válido.'));
    } else {
        // O token é inválido, retorne uma mensagem de erro
        echo json_encode(array('success' => false, 'message' => 'Token do reCAPTCHA inválido.'));
    }
} else {
    // Token do reCAPTCHA não foi recebido, retorne uma mensagem de erro
    echo json_encode(array('success' => false, 'message' => 'Token do reCAPTCHA não recebido.'));
}
?>
