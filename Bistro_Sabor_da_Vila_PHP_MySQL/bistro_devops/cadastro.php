<?php
declare(strict_types=1);
require_once __DIR__.'/config/database.php';
function e($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$erro = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string)($_POST['nome'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $senha = (string)($_POST['senha'] ?? '');
    $confirmacao = (string)($_POST['confirmacao'] ?? '');

    if ($nome === '' || strlen($nome) > 480) {
        $erro = 'Informe seu nome (até 120 caracteres).';
    } elseif (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (($caracteresSenha = preg_match_all('/./us', $senha)) === false || $caracteresSenha < 8 || strlen($senha) > 72) {
        $erro = 'A senha deve ter no mínimo 8 caracteres e no máximo 72 bytes.';
    } elseif ($senha !== $confirmacao) {
        $erro = 'As senhas não conferem.';
    } else {
        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
            $stmt->execute([$nome, $email, $senhaHash]);
            header('Location: cadastro.php?sucesso=1');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $erro = 'Já existe uma conta cadastrada com esse e-mail.';
            } else {
                error_log('Erro ao cadastrar usuário: '.$ex->getMessage());
                $erro = 'Não foi possível concluir o cadastro. Confira o banco de dados.';
            }
        }
    }
}

$sucesso = isset($_GET['sucesso']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cadastrar usuário | Bistrô Sabor da Vila</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-inner">
        <a class="brand" href="index.php"><span class="brand-icon">SV</span><span>Bistrô <b>Sabor da Vila</b><small>PAINEL DE COMANDAS</small></span></a>
        <nav><a href="index.php">Comandas</a><a href="produtos.php">Produtos</a><a class="nav-active" href="cadastro.php">Cadastrar usuário</a><a href="avaliar.php">Avaliar</a><a href="comentarios.php">Ver comentários</a></nav>
    </div>
</header>
<main class="wrap">
    <section class="hero"><div><span class="eyebrow">NOVA CONTA</span><h1>Cadastrar usuário</h1><p>Crie uma conta com seu nome, e-mail e senha.</p></div><div class="hero-mark">＋</div></section>
    <?php if ($erro): ?><div class="alert error"><?=e($erro)?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alert success">Usuário cadastrado com sucesso.</div><?php endif; ?>
    <section class="registration-panel panel">
        <form method="post" class="admin-form">
            <label for="nome">Nome
                <input id="nome" name="nome" type="text" required maxlength="120" autocomplete="name" value="<?=e($nome)?>">
            </label>
            <label for="email">E-mail
                <input id="email" name="email" type="email" required maxlength="254" autocomplete="email" value="<?=e($email)?>">
            </label>
            <label for="senha">Senha <span class="muted">(mínimo de 8 caracteres)</span>
                <input id="senha" name="senha" type="password" required minlength="8" maxlength="72" autocomplete="new-password">
            </label>
            <label for="confirmacao">Confirme a senha
                <input id="confirmacao" name="confirmacao" type="password" required minlength="8" maxlength="72" autocomplete="new-password">
            </label>
            <button class="button primary full" type="submit">Cadastrar usuário</button>
        </form>
        <p class="hint">A senha é armazenada como hash seguro; ela não fica salva em texto puro.</p>
    </section>
    <footer>Projeto didático • UC2 — Práticas DevOps em Projetos Web <span><a href="index.php">Voltar às comandas</a></span></footer>
</main>
</body>
</html>
