<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
    $tipo = $_POST['tipo'];
    $clinica_id = $_POST['clinica_id'] ?? null;  // Pegamos o 'clinica_id' do formulário, se existir

    // Se for veterinário e ele está criando uma clínica
    if ($tipo == 'veterinario' && !empty($_POST['clinica_nome'])) {
        $clinica_nome = $_POST['clinica_nome'];
        $clinica_endereco = $_POST['clinica_endereco'];
        
        // Inserir a nova clínica no banco de dados
        $sql_clinica = "INSERT INTO clinicas (nome, endereco) VALUES (?, ?)";
        $stmt_clinica = $pdo->prepare($sql_clinica);
        $stmt_clinica->execute([$clinica_nome, $clinica_endereco]);

        // Pega o ID da nova clínica criada
        $clinica_id = $pdo->lastInsertId();
    }

    // Se o usuário for tutor, ele deve selecionar uma clínica
    if ($tipo == 'tutor' && empty($clinica_id)) {
        echo "Erro: o tutor precisa selecionar uma clínica!";
        exit();
    }

    // Verifique se o clinica_id é válido (existe na tabela clinicas)
    if ($clinica_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clinicas WHERE id = ?");
        $stmt->execute([$clinica_id]);
        $clinica_count = $stmt->fetchColumn();
        
        if ($clinica_count == 0) {
            echo "Erro: O clinica_id não corresponde a nenhuma clínica existente!";
            exit();
        }
    }

    // Inserir o usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo, clinica_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $senha, $tipo, $clinica_id]);

    echo "Usuário cadastrado com sucesso!";
}
?>



<form method="POST" action="cadastro.php">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" required>

    <label for="tipo">Tipo de Usuário:</label>
    <select name="tipo" id="tipo" required>
        <option value="tutor">Tutor</option>
        <option value="veterinario">Veterinário</option>
    </select>

    <div id="clinica-section">
        <!-- Se for tutor, ele verá essa seção -->
        <label for="clinica">Clínica (para tutores):</label>
        <select name="clinica_id" id="clinica">
            <option value="">Selecione uma clínica</option>
            <?php
            // Exibir clínicas cadastradas
            $clinicas = $pdo->query("SELECT id, nome FROM clinicas")->fetchAll();
            foreach ($clinicas as $clinica) {
                echo "<option value='{$clinica['id']}'>{$clinica['nome']}</option>";
            }
            ?>
        </select>
        
        <!-- Se for veterinário, ele verá essa seção para criar sua própria clínica -->
        <div id="nova-clinica-section" style="display: none;">
            <h3>Cadastrar Nova Clínica</h3>
            <label for="clinica_nome">Nome da Clínica:</label>
            <input type="text" name="clinica_nome" id="clinica_nome">
            <label for="clinica_endereco">Endereço:</label>
            <input type="text" name="clinica_endereco" id="clinica_endereco">
        </div>
    </div>

    <button type="submit">Cadastrar</button>
</form>

<script>
    // Mostrar/esconder seções com base no tipo de usuário selecionado
    document.getElementById('tipo').addEventListener('change', function() {
        var tipo = this.value;
        var clinicaSection = document.getElementById('clinica-section');
        var novaClinicaSection = document.getElementById('nova-clinica-section');
        if (tipo === 'veterinario') {
            clinicaSection.style.display = 'block';
            novaClinicaSection.style.display = 'block'; // Exibe campos para criar nova clínica
        } else if (tipo === 'tutor') {
            clinicaSection.style.display = 'block';
            novaClinicaSection.style.display = 'none'; // Esconde campos de nova clínica
        }
    });
    
</script>