<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Exemplo de reCAPTCHA invisível</title>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<a href="#" id="whatsapp-button">Enviar mensagem via WhatsApp</a>

<script>
document.getElementById("whatsapp-button").addEventListener("click", function(event) {
    event.preventDefault(); // Impede o comportamento padrão do link

    // Ativa o reCAPTCHA invisível
    grecaptcha.execute('6LfiGc4pAAAAAC7lDZfG2e1AtQ13a9DYTCrfG3A8', { action: 'submit' })
    .then(function(token) {
        // Envia o token para validação no servidor
        enviarTokenParaValidacao(token);
    });
});

// Função para enviar o token para validação no servidor
function enviarTokenParaValidacao(token) {
    // Envie o token para o servidor usando AJAX ou outra técnica de comunicação
    var data = new URLSearchParams();
    data.append('token', token);


    // Aqui vamos apenas simular o envio de uma requisição POST usando fetch
    fetch('verificar_token.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'token=' + token
    })
    .then(response => response.json())
    .then(data => {
        // Verifica a resposta do servidor
        if (data.success) {
            // Se o token for válido, redirecione para o link do WhatsApp
            window.location.href = "https://api.whatsapp.com/send?phone=SEU_NUMERO_DE_TELEFONE";
        } else {
            // Se houver um erro de validação do token, exiba uma mensagem de erro
            console.error("Erro ao validar o token do reCAPTCHA:", data.message);
            alert("Erro ao enviar1 mensagem. Por favor, tente novamente mais tarde.");
        }
    })
    .catch(error => {
        // Em caso de erro na requisição
        console.error("Erro na requisição:", error);
        alert("Erro ao enviar mensagem. Por favor, tente novamente mais tarde.");
    });
}
</script>
<script type="text/javascript">
        function onClick(e) {
            e.preventDefault();
            grecaptcha.ready(function () {
                grecaptcha.execute('public-sitekey-here', { action: 'submit' }).then(function (token) {
                    // Add your logic to submit to your backend server here.
                        console.log('refreshed token:', token);
                        document.getElementById("token").value = token;
                });
            });
        }
    </script>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?render=6LfiGc4pAAAAAC7lDZfG2e1AtQ13a9DYTCrfG3A8"></script>
</body>
</html>
