<?php
declare(strict_types=1);
require_once __DIR__.'/config/database.php';
function e($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$erro = '';
$nota = '';
$comentario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = (string)($_POST['nota'] ?? '');
    $comentario = trim((string)($_POST['comentario'] ?? ''));
    $notaValidada = filter_var($nota, FILTER_VALIDATE_INT);
    $tamanhoComentario = preg_match_all('/./us', $comentario);

    if ($notaValidada === false || $notaValidada < 1 || $notaValidada > 5) {
        $erro = 'Escolha uma nota de 1 a 5 estrelas.';
    } elseif ($comentario === '' || $tamanhoComentario === false || $tamanhoComentario > 2000) {
        $erro = 'Escreva um comentário com até 2000 caracteres.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO avaliacoes (nota, comentario) VALUES (?, ?)');
            $stmt->execute([$notaValidada, $comentario]);
            header('Location: avaliar.php?sucesso=1');
            exit;
        } catch (PDOException $ex) {
            error_log('Erro ao salvar avaliação: '.$ex->getMessage());
            $erro = 'Não foi possível salvar sua avaliação. Confira o banco de dados.';
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
    <title>Avaliar | Bistrô Sabor da Vila</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-inner">
        <a class="brand" href="index.php"><span class="brand-icon">SV</span><span>Bistrô <b>Sabor da Vila</b><small>PAINEL DE COMANDAS</small></span></a>
        <nav><a href="index.php">Comandas</a><a href="produtos.php">Produtos</a><a href="cadastro.php">Cadastrar usuário</a><a class="nav-active" href="avaliar.php">Avaliar</a><a href="comentarios.php">Ver comentários</a></nav>
    </div>
</header>
<main class="wrap">
    <section class="hero"><div><span class="eyebrow">SUA OPINIÃO</span><h1>Avalie sua experiência</h1><p>Escolha uma nota e conte pra gente o que achou.</p></div><div class="hero-mark">★</div></section>
    <?php if ($erro): ?><div class="alert error"><?=e($erro)?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alert success">Obrigado! Sua avaliação foi salva.</div><?php endif; ?>
    <section class="registration-panel panel">
        <form method="post" class="admin-form">
            <fieldset class="rating-fieldset">
                <legend>Nota de 1 a 5 estrelas</legend>
                <div class="star-rating">
                    <?php for ($valor = 5; $valor >= 1; $valor--): ?>
                    <input id="nota-<?=$valor?>" type="radio" name="nota" value="<?=$valor?>" required <?=((int)$nota === $valor)?'checked':''?>>
                    <label for="nota-<?=$valor?>" aria-label="<?=$valor?> <?= $valor === 1 ? 'estrela' : 'estrelas' ?>">★</label>
                    <?php endfor; ?>
                </div>
            </fieldset>
            <label for="comentario">Comentário
                <textarea id="comentario" name="comentario" rows="5" maxlength="2000" required placeholder="Escreva seu comentário..."><?=e($comentario)?></textarea>
            </label>
            <button class="button primary full" type="submit">Enviar avaliação</button>
        </form>
        <p class="hint">Sua avaliação será exibida na página de comentários.</p>
        <a class="button secondary full" href="comentarios.php">Ver comentários</a>
    </section>
    <footer>Projeto didático • UC2 — Práticas DevOps em Projetos Web <span><a href="index.php">Voltar às comandas</a></span></footer>
</main>
</body>
</html>
