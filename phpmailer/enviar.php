<?php header('Content-Type: text/html; charset=utf-8'); 
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

//Defino a Chave do meu site
$secret_key = '6Lf3K6caAAAAAOaSJwiu_Y2aPHBZixSyTO5y9plB';

//Pego a validação do Captcha feita pelo usuário
$recaptcha_response = $_POST['g-recaptcha-response'];

// Verifico se foi feita a postagem do Captcha 
if(isset($recaptcha_response)){
        
    // Valido se a ação do usuário foi correta junto ao google
    $answer = 
        json_decode(
            file_get_contents(
                'https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.
                '&response='.$_POST['g-recaptcha-response']
            )
        );

    // Se a ação do usuário foi correta executo o restante do meu formulário
    if($answer->success) {



        $assunto = $_POST['assunto'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $mensagem = $_POST['mensagem'];

        // Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
            require("class.phpmailer.php");
            require("class.smtp.php");

            // Inicia a classe PHPMailer
            $mail = new PHPMailer();
             
            // Define os dados técnicos da Mensagem
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
            $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
            // Define os dados do servidor e tipo de conexão
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->IsSMTP(); // Define que a mensagem será SMTP
            $mail->Host = "smtp.umbler.com ";
            $mail->Port = 587;
            $mail->SMTPAuth = true; // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
            $mail->Username = 'site@endall.com.br'; // Usuário do servidor SMTP (endereço de email)
            $mail->Password = 'UT_qZY9!y.8n2'; // Senha do servidor SMTP (senha do email usado)
            $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );


            // Define o remetente
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->From = $email; // Seu e-mail
            $mail->Sender = 'site@endall.com.br'; // Seu e-mail
            $mail->FromName =  $nome; // Seu nome
             
            // Define os destinatário(s)
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->AddAddress('allan@endall.com.br'); 
            $mail->AddBCC('contato@novaeraweb.com.br'); // Cópia Oculta


            // Define a mensagem (Texto e Assunto)
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->Subject  = "Lead do Site | Endall Inspeções e Serviços"; // Assunto da mensagem 
            $mail->Body = '
            Lead captado através do site:<br><br>
            Nome: '.$nome.'<br>
            Email: '.$email.'<br>
            Assunto: '.$assunto.'<br><br>
            Mensagem:<br>'.$mensagem.'<br>
            ';

            $mail->AltBody = '
            Lead captado através do site:<br><br>
            Nome: '.$nome.'<br>
            Email: '.$email.'<br>
            Mensagem: '.$mensagem.'<br>
            ';

            //Enviando o email 
            //==================================================== 
            if ($enviado = $mail->Send()){ 
                    header("Location: ../index.php?enviado=true");
                    } 
                else{ 
                    echo "</b>Falha no envio do E-Mail!</b>"; 
                    echo "<b>Informações do erro:</b> " . $mail->ErrorInfo;
                  }

}
// Caso o Captcha não tenha sido validado 
    //retorno uma mensagem de erro. 
    else {
        header("Location: ../index.php?confirma=true");
    }
}