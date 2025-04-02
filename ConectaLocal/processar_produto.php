<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $id_comercio = $_POST['id_comercio']; 
    $nome_produto = $_POST['nome_produto'];
    $descricao_produto = $_POST['descricao_produto']; 
    $data_criacao = date("Y-m-d"); 
    $data_encerramento = $_POST['data_encerramento']; 


    $target_dir = "uploads/Ofertas/"; 
    $imageFileType = strtolower(pathinfo($_FILES["imagem_produto"]["name"], PATHINFO_EXTENSION));

    $target_file = $target_dir . uniqid('produto_', true) . '.' . $imageFileType;
    
    $uploadOk = 1;

    $check = getimagesize($_FILES["imagem_produto"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "O arquivo não é uma imagem.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "jfif") {
        echo "Desculpe, apenas arquivos JPG, JPEG, PNG e JFIF são permitidos.";
        $uploadOk = 0;
    }

    // Verifica se houve algum erro
    if ($uploadOk == 0) {
        echo "Desculpe, seu arquivo não foi enviado.";
    } else {
        // Move o arquivo para o diretório de destino
        if (move_uploaded_file($_FILES["imagem_produto"]["tmp_name"], $target_file)) {
            // Conectar ao banco de dados
            $conn = new mysqli("localhost", "root", "", "mercado");

            // Verificar conexão
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            // Inserir os dados no banco de dados
            $sql = "INSERT INTO produtos_rel (id_comercio, nome_produto, descricao_produto, data_criacao, data_encerramento, imagem_produto)
                    VALUES ('$id_comercio', '$nome_produto', '$descricao_produto', '$data_criacao', '$data_encerramento', '$target_file')";
            if ($conn->query($sql) === TRUE) {
                header("Location: index.html");
                exit;
            } else {
                echo "Erro ao cadastrar o produto: " . $conn->error;
            }

            // Fechar a conexão
            $conn->close();
        } else {
            echo "Desculpe, houve um erro ao enviar seu arquivo.";
        }
    }
}
?>
