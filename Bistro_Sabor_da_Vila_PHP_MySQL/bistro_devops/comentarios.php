<?php
declare(strict_types=1);
require_once __DIR__.'/config/database.php';
function e($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$avaliacoes = $pdo->query('SELECT nota, comentario, criado_em FROM avaliacoes ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Comentários | Bistrô Sabor da Vila</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-inner">
        <a class="brand" href="index.php"><span class="brand-icon">SV</span><span>Bistrô <b>Sabor da Vila</b><small>PAINEL DE COMANDAS</small></span></a>
        <nav><a href="index.php">Comandas</a><a href="produtos.php">Produtos</a><a href="cadastro.php">Cadastrar usuário</a><a href="avaliar.php">Avaliar</a><a class="nav-active" href="comentarios.php">Ver comentários</a></nav>
    </div>
</header>
<main class="wrap">
    <section class="hero"><div><span class="eyebrow">OPINIÕES DOS CLIENTES</span><h1>Avaliações</h1><p>Confira as notas e os comentários enviados.</p></div><div class="hero-mark">★</div></section>
    <div class="reviews-heading"><span class="count"><?=count($avaliacoes)?> avaliação(ões)</span><a class="button primary" href="avaliar.php">＋ Fazer avaliação</a></div>
    <?php if (!$avaliacoes): ?>
        <div class="panel empty large">Ainda não há avaliações.<br><span>Seja a primeira pessoa a compartilhar sua opinião.</span></div>
    <?php else: ?>
        <section class="reviews-list" aria-label="Comentários e avaliações">
            <?php foreach ($avaliacoes as $avaliacao): ?>
            <article class="review-card">
                <div class="review-top">
                    <div class="review-stars" aria-label="Nota <?= (int)$avaliacao['nota'] ?> de 5">
                        <?php for ($estrela = 1; $estrela <= 5; $estrela++): ?><span class="<?=$estrela <= (int)$avaliacao['nota']?'star-filled':'star-empty'?>">★</span><?php endfor; ?>
                    </div>
                    <time class="order-date" datetime="<?=e(date('c', strtotime($avaliacao['criado_em'])))?>"><?=e(date('d/m/Y H:i', strtotime($avaliacao['criado_em'])))?></time>
                </div>
                <p class="review-comment"><?=nl2br(e($avaliacao['comentario']))?></p>
            </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <footer>Projeto didático • UC2 — Práticas DevOps em Projetos Web <span><a href="index.php">Voltar às comandas</a></span></footer>
</main>
</body>
</html>
