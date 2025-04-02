<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $data_criacao = date("Y-m-d"); // Pega a data atual no formato "YYYY-MM-DD"
    $data_encerramento = $_POST['data_encerramento'];

    // Configurações para o upload de imagem
    $target_dir = "uploads/encartes/";
    $target_file = $target_dir . basename($_FILES["imagem_encarte"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verifica se o arquivo é uma imagem real
    $check = getimagesize($_FILES["imagem_encarte"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "O arquivo não é uma imagem.";
        $uploadOk = 0;
    }

    // Verificar se a imagem já existe
    if (file_exists($target_file)) {
        echo "Desculpe, o arquivo já existe.";
        $uploadOk = 0;
    }

    // Limitar tipos de arquivos aceitos
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jfif") {
        echo "Desculpe, apenas arquivos JPG, JPEG, PNG e JFIF são permitidos.";
        $uploadOk = 0;
    }

    // Verifica se houve algum erro
    if ($uploadOk == 0) {
        echo "Desculpe, seu arquivo não foi enviado.";
    } else {
        if (move_uploaded_file($_FILES["imagem_encarte"]["tmp_name"], $target_file)) {
            // Conectar ao banco de dados
            $conn = new mysqli("localhost", "root", "", "mercado");

            // Verificar conexão
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            // Inserir os dados no banco de dados
            $sql = "INSERT INTO encartes (imagem_encarte, data_criacao, data_encerramento)
                    VALUES ('$target_file', '$data_criacao', '$data_encerramento')";
            if ($conn->query($sql) === TRUE) {
                header("Location: index.html");
                exit;
            } else {
                echo "Erro ao cadastrar o encarte: " . $conn->error;
            }

            // Fechar a conexão
            $conn->close();
        } else {
            echo "Desculpe, houve um erro ao enviar seu arquivo.";
        }
    }
}
?>
