<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: index.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mercado";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT id_comercio, nome_fantasia, cnpj, endereco, telefone_comercio, status_funcionamento FROM comercio WHERE status_funcionamento IN ('pendente', 'ativo', 'inativo')";
$result = $conn->query($sql);

if ($result === false) {
    echo "Erro na consulta: " . $conn->error;
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Solicitações de Comércio</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F4F6F9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: 40px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #3F5A96;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3F5A96;
            color: #fff;
            border-radius: 8px;
            text-transform: uppercase;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            color: white;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-approve {
            background-color: #4CAF50;
        }

        .btn-approve:hover {
            background-color: #45a049;
        }

        .btn-reject {
            background-color: #F44336;
        }

        .btn-reject:hover {
            background-color: #e53935;
        }

        .btn:focus {
            outline: none;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pendente {
            background-color: #FFA726;
            color: white;
        }

        .status-ativo {
            background-color: #66BB6A;
            color: white;
        }

        .status-inativo {
            background-color: #EF5350;
            color: white;
        }

        /* Adaptações para telas menores */
        @media (max-width: 864px) {
            .container {
                padding: 20px;
            }

            table, .actions {
                font-size: 14px;
            }

            .btn {
                font-size: 12px;
                padding: 8px 16px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Solicitações de Comércio</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['nome_fantasia']; ?></td>
                        <td><?php echo $row['cnpj']; ?></td>
                        <td><?php echo $row['endereco']; ?></td>
                        <td><?php echo $row['telefone_comercio']; ?></td>
                        <td>
                            <span class="status status-<?php echo strtolower($row['status_funcionamento']); ?>">
                                <?php echo ucfirst($row['status_funcionamento']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <?php if ($row['status_funcionamento'] == 'pendente') { ?>
                                <form action="processa_solicitacao.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_comercio" value="<?php echo $row['id_comercio']; ?>">
                                    <button type="submit" name="acao" value="aprovar" class="btn btn-approve">Aprovar</button>
                                </form>
                                <form action="processa_solicitacao.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_comercio" value="<?php echo $row['id_comercio']; ?>">
                                    <button type="submit" name="acao" value="recusar" class="btn btn-reject">Recusar</button>
                                </form>
                            <?php } else { ?>
                                <button class="btn" disabled>Já Processado</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
