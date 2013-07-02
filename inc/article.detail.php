<?php $article = getArticle("full", $meta['id_article']); ?>

<div id="article">
    <div class="border top"></div>
    <article>
        <h1><?php echo $article[0]["title_sk"]; ?></h1>
        <?php echo $article[0]["content_sk"]; ?>
    </article>
</div>