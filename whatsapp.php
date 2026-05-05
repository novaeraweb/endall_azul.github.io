<?php  $url = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], '/')+1);?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <a id="open-modal" rel="noopener" rel="noreferrer" class="whats">
        <?php if (strpos($url, "servicos/") !== false OR strpos($url, "equipamentos/") !== false){?>
            <img src="../images/whatsapp.png" alt="Entre em contato via WhatsApp">
        <?php } else { ?>
            <img src="images/whatsapp.png" alt="Entre em contato via WhatsApp">
        <?php } ?>
    </a>

<!-- Pop-up modal -->
<div id="modal" class="modal">
  <div id="modal-content" class="modal-content">
    <span class="close">&times;</span>
    <div class="g-recaptcha" style="margin-left:0 auto" data-callback="onRecaptchaSuccess" data-sitekey="6Lf3K6caAAAAAOPxQp3axKLQaiTheUcffGImFPbP"></div>
    <!-- Botão para enviar o formulário -->
    <button id="submit-button" onclick="gtag_report_conversion('https://www.endall.com.br')" style="margin-top:20px;background-color:#50CD68;">Enviar mensagem para o WhatsApp</button>
  </div>
</div>
<div class="yesphone">
    <script type="text/javascript">
        // Abrir o pop-up quando o link é clicado
        document.getElementById("open-modal").addEventListener("click", function(event) {
          event.preventDefault(); // Impede o comportamento padrão do link
          document.getElementById("modal").style.display = "block";
        });

        // Fechar o pop-up quando o usuário clicar no botão de fechar
        document.getElementsByClassName("close")[0].addEventListener("click", function() {
          document.getElementById("modal").style.display = "none";
        });

        // Lidar com o envio do formulário
        document.getElementById("submit-button").addEventListener("click", function() {
          // Obtém o token do reCAPTCHA
          var token = grecaptcha.getResponse();

          // Verifica se o token foi obtido
          if (token) {
            // Abre o link para o WhatsApp em uma nova aba
            window.open("https://api.whatsapp.com/send?phone=5519999088253&amp;text=Ol%C3%A1%2C%20me%20chame%20no%20WhatsApp! Contato através do site.", "_blank");
            window.location.reload();
            // Fecha o pop-up modal
            document.getElementById("modal").style.display = "none";
          } else {
            // Se o token não foi obtido, exiba uma mensagem de erro
            alert("Por favor, complete o reCAPTCHA antes de enviar.");
          }
        });
        function onRecaptchaSuccess() {
  // Torna o botão de envio visível
  document.getElementById("submit-button").style.display = "block";
}
    </script>
</div>


<div class="nophone">
    <script type="text/javascript">
        // Abrir o pop-up quando o link é clicado
        document.getElementById("open-modal").addEventListener("click", function(event) {
          event.preventDefault(); // Impede o comportamento padrão do link
          document.getElementById("modal").style.display = "block";
          document.getElementById("modal-content").style.display = "block";
        });

        // Fechar o pop-up quando o usuário clicar no botão de fechar
        document.getElementsByClassName("close")[0].addEventListener("click", function() {
          document.getElementById("modal").style.display = "none";
        });

        // Lidar com o envio do formulário
        document.getElementById("submit-button").addEventListener("click", function() {
          // Obtém o token do reCAPTCHA
          var token = grecaptcha.getResponse();

          // Verifica se o token foi obtido
          if (token) {
            // Abre o link para o WhatsApp em uma nova aba
            window.open("https://web.whatsapp.com/send?phone=5519999088253&amp;text=Ol%C3%A1%2C%20me%20chame%20no%20WhatsApp! Contato através do site.", "_blank");
            window.location.reload();
            // Fecha o pop-up modal
            document.getElementById("modal").style.display = "none";
          } else {
            // Se o token não foi obtido, exiba uma mensagem de erro
            alert("Por favor, complete o reCAPTCHA antes de enviar.");
          }
        });
    </script>
</div>