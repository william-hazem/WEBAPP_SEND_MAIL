<?php 

    require_once "secrets.php";
    
    require 'libs/PHPMailer/Exception.php';
    require 'libs/PHPMailer/OAuth.php';
    require 'libs/PHPMailer/PHPMailer.php';
    require 'libs/PHPMailer/POP3.php';
    # servidor SMTP - envio de correspodência eletrônica
    require 'libs/PHPMailer/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    // echo '<pre>';
    // $dados = implode("\08", $_POST);
    // print_r(explode("\08", $dados));
    // echo '</pre>';

    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';
    class Mensagem {
        private $destino = null;
        private $assunto = null;
        private $mensagem = null;
        public $status = array(
            'codigo_status' => null, 'descricao_status' => ''
        );

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        public function messagemValida() {
            if(empty($this->destino) || empty($this->assunto) || empty($this->mensagem) )
                return false;
            return true;
        }

    }
    $mensagem = new Mensagem();
    $mensagem->__set('destino', $_POST['destino']);
    $mensagem->__set('assunto', $_POST['assunto']);
    $mensagem->__set('mensagem', $_POST['mensagem']);

    if(!$mensagem->messagemValida()) {
        header('Location: index.php?erro=invalido');
    }
    // else {
    //     echo 'Mensagem não é válida';
    //     # interrompe o script
    //     die(); 
    // }

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $username;                     //SMTP username
        $mail->Password   = $password;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($email, 'William\'s bot ');
        $mail->addAddress($mensagem->__get('destino'));     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        $mail->addReplyTo('noreply@william.com', 'No-Reply');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mensagem->__get('assunto');
        $mail->Body    = $mensagem->__get('mensagem');
        $mail->AltBody = 'Eu sou um email Auto';

        $mail->send();

        $mensagem->status['codigo_status'] = 1;
        $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso';
    } catch (Exception $e) {
        
        $mensagem->status['codigo_status'] = 1;
        $mensagem->status['descricao_status'] = 'E-mail não pode ser entregue. Error: ' . $mail->ErrorInfo;
    }

?>


<html>
    <head>
		<meta charset="utf-8" />
    	<title>App Mail Send</title>

    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	</head>

    <body>

        <div class="container">
            <div class="py-3 text-center">
				<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
				<h2>Send Mail</h2>
				<p class="lead">Seu app de envio de e-mails particular!</p>
			</div>

            <div class="row">
                <div class="col-md-12">
                    <?php if($mensagem->status['codigo_status'] == 1) { ?>
                        <div class="container">
                            <h1 class="display-4 text-success">Sucesso</h1>
                            <p><?= $mensagem->status['descricao_status']?></p>
                            <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                        </div>
                    <?php } ?>

                    <?php if($mensagem->status['codigo_status'] == 2) { ?>
                        <div class="container">
                            <h1 class="display-4 text-danger">Ops!</h1>
                            <p><?= $mensagem->status['descricao_status']?></p>
                            <a href="index.php" class="btn btn-danger btn-lg mt-5 text-white">Voltar</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </body>
</html>