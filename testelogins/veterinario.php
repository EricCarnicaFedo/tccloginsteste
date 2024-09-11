<?php
include('db.php');
session_start();

// Verificar se o usuário é um veterinário
if ($_SESSION['tipo'] != 'veterinario') {
    echo "Acesso negado!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_tutor = $_POST['nome_tutor'];
    $email_tutor = $_POST['email_tutor'];
    $nome_pet = $_POST['nome_pet'];
    $data_consulta = $_POST['data_consulta'];
    $descricao_consulta = $_POST['descricao_consulta'];

    // Verificar se o tutor já existe
    $sql = "SELECT id FROM usuarios WHERE nome = ? AND email = ? AND tipo = 'tutor'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome_tutor, $email_tutor]);
    $tutor = $stmt->fetch();

    if ($tutor) {
        $tutor_id = $tutor['id'];
    } else {
        echo "Tutor não encontrado! Por favor, adicione o tutor primeiro.";
        exit();
    }

    // Inserir o pet
    $sql = "INSERT INTO pets (nome, tutor_id) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome_pet, $tutor_id]);
    $pet_id = $pdo->lastInsertId(); // ID do pet recém-criado

    // Inserir a consulta
    $sql = "INSERT INTO consultas (data, descricao, pet_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data_consulta, $descricao_consulta, $pet_id]);

    echo "Pet e Consulta cadastrados com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Veterinário</title>
</head>
<body>
    <h1>Área do Veterinário</h1>

    <!-- Formulário Único para Adicionar Tutor, Pet e Consulta -->
    <h2>Adicionar Pet e Consulta</h2>
    <form method="POST">
        <fieldset>
            <legend>Dados do Tutor</legend>
            <input type="text" name="nome_tutor" placeholder="Nome do Tutor" required>
            <input type="email" name="email_tutor" placeholder="Email do Tutor" required>
        </fieldset>

        <fieldset>
            <legend>Dados do Pet</legend>
            <input type="text" name="nome_pet" placeholder="Nome do Pet" required>
        </fieldset>

        <fieldset>
            <legend>Dados da Consulta</legend>
            <input type="datetime-local" name="data_consulta" required>
            <textarea name="descricao_consulta" placeholder="Descrição da Consulta" required></textarea>
        </fieldset>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
