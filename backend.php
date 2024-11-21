<?php

// create a "hexapp" database with: CREATE DATABASE hexapp;

$retorno = [
    "erro" => "S",
    "alerta" => "",
    "dados" => ""
];

$host = "localhost";
$username = "root";
$password = "";
$dbname = "hexapp";

$db = mysqli_connect($host, $username, $password, $dbname);

if (!$db) {
    $retorno['alerta'] = mysqli_connect_error();
    mysqli_close($db);
    echo json_encode($retorno);
    exit;
}

switch($_GET['c']) {
    case "Iniciar":
        $template_tabela = "<tr><td>{{ID}}</td> <td>{{NOME}}</td> <td><button onclick='ApagarCliente({{ID}})'>X</button></td></tr>";
        $tabela_clientes = "";

        $template_options = "<option value='{{ID}}'>{{NOME}}</option>";
        $options = "<option selected disabled value='0'>Selecione um cliente</option>";

        $select_clientes = "SELECT * FROM hexapp.clientes";
        $exec = mysqli_query($db, $select_clientes);

        if (mysqli_num_rows($exec) > 0) {
            while ($res = mysqli_fetch_assoc($exec)) {
                $temp = $template_tabela;
                $temp = str_replace("{{ID}}", $res['id'], $temp);
                $temp = str_replace("{{NOME}}", $res['nome'], $temp);

                $tabela_clientes .= $temp;

                $temp = $template_options;
                $temp = str_replace("{{ID}}", $res['id'], $temp);
                $temp = str_replace("{{NOME}}", $res['nome'], $temp);

                $options .= $temp;
            }
        }

        $template_tabela = "<tr><td>{{ID}}</td> <td>{{NOME}}</td> <td>{{CONTATO}}</td> <td><button onclick='ApagarContato({{ID}})'>X</button></td></tr>";
        $tabela_contatos = "";

        $select_contatos = "SELECT a.id, b.nome, a.contato FROM hexapp.contatos a LEFT JOIN hexapp.clientes b ON a.cliente_id = b.id";
        $exec = mysqli_query($db, $select_contatos);

        if (mysqli_num_rows($exec) > 0) {
            while ($res = mysqli_fetch_assoc($exec)) {
                $temp = $template_tabela;
                $temp = str_replace("{{ID}}", $res['id'], $temp);
                $temp = str_replace("{{NOME}}", $res['nome'], $temp);
                $temp = str_replace("{{CONTATO}}", $res['contato'], $temp);

                $tabela_contatos .= $temp;
            }
        }

        $retorno['erro'] = 'N';
        $retorno['dados'] = [
            'clientes' => $tabela_clientes,
            'options' => $options,
            'contatos' => $tabela_contatos
        ];

        break;

    case "CriarTabelas":
        mysqli_query($db,'START TRANSACTION');

        $create_clientes = "CREATE TABLE hexapp.clientes (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(255) NOT NULL);";
        $exec = mysqli_query($db, $create_clientes);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            $retorno['alerta'] = mysqli_error($db);
            break;
        }

        $create_contatos = "CREATE TABLE hexapp.contatos (id INT AUTO_INCREMENT PRIMARY KEY, cliente_id INT, contato VARCHAR(255) NOT NULL, FOREIGN KEY (cliente_id) REFERENCES hexapp.clientes(id));";
        $exec = mysqli_query($db, $create_contatos);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            $retorno['alerta'] = mysqli_error($db);
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    case "LimparTabelas":
        mysqli_query($db,'START TRANSACTION');

        $drop_contatos = "DELETE * FROM hexapp.contatos";
        $exec = mysqli_query($db, $drop_contatos);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        $drop_clientes = "DELETE * FROM hexapp.clientes";
        $exec = mysqli_query($db, $drop_clientes);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    case "PovoarTabelas":
        mysqli_query($db,'START TRANSACTION');

        $insert_clientes = "INSERT INTO hexapp.clientes (nome) VALUES ('Fulano'), ('Sicrano'), ('Beltrano');";
        $exec = mysqli_query($db, $insert_clientes);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        $insert_contatos = "INSERT INTO hexapp.contatos (cliente_id, contato) VALUES (1, 'email@email.com'), (2, '99999-9999'), (3, 'meu@email.com'), (2, '9999-9999');";
        $exec = mysqli_query($db, $insert_contatos);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    case "InserirCliente":
        mysqli_query($db,'START TRANSACTION');

        $nome = $_POST['nome'];

        $insert_clientes = "INSERT INTO hexapp.clientes (nome) VALUES ('$nome');";
        $exec = mysqli_query($db, $insert_clientes);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    case "ApagarCliente":
        mysqli_query($db,'START TRANSACTION');

        $id = $_POST['id'];

        $delete_contatos = "DELETE FROM hexapp.contatos WHERE cliente_id = $id;";
        $exec = mysqli_query($db, $delete_contatos);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        $delete_clientes = "DELETE FROM hexapp.clientes WHERE id = $id;";
        $exec = mysqli_query($db, $delete_clientes);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    case "InserirContato":
        if ($_POST['id'] <= 0) {
            break;
        }

        mysqli_query($db,'START TRANSACTION');

        $id = $_POST['id'];
        $contato = $_POST['contato'];

        $insert_contatos = "INSERT INTO hexapp.contatos (cliente_id, contato) VALUES ($id, '$contato');";
        $exec = mysqli_query($db, $insert_contatos);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    case "ApagarContato":
        mysqli_query($db,'START TRANSACTION');

        $id = $_POST['id'];

        $delete_contato = "DELETE FROM hexapp.contatos WHERE id = $id;";
        $exec = mysqli_query($db, $delete_contato);

        if (mysqli_error($db)) {
            mysqli_query($db,'ROLLBACK');
            break;
        }

        mysqli_query($db,'COMMIT');
        $retorno['erro'] = 'N';
        break;

    default:
        break;
}

mysqli_close($db);
echo json_encode($retorno);