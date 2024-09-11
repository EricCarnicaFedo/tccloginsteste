<?php
include('db.php');
session_start();

if ($_SESSION['tipo'] != 'tutor') {
    header("Location: login.php");
    exit();
}

$tutor_id = $_SESSION['usuario_id'];

// Selecionando os pets e suas consultas do tutor logado
$pets = $pdo->prepare("
    SELECT pets.id, pets.nome, pets.especie, consultas.descricao, consultas.data
    FROM pets
    LEFT JOIN consultas ON pets.id = consultas.pet_id
    WHERE pets.tutor_id = ?
");
$pets->execute([$tutor_id]);
$pets = $pets->fetchAll();
?>

<h1>Seus Pets e Consultas</h1>

<ul>
    <?php foreach ($pets as $pet): ?>
        <li>
            <strong><?= $pet['nome'] ?> (<?= $pet['especie'] ?>)</strong><br>
            <?php if ($pet['descricao']): ?>
                Consulta: <?= $pet['descricao'] ?> em <?= $pet['data'] ?>
            <?php else: ?>
                Nenhuma consulta registrada
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
