<li class="feed-entry author-<?php echo nameToId($entry->author) ?>" data-url="<?php echo $entry->link ?>" data-site-url="<?php echo $entry->siteUrl ?>">
	<h3 class="feed-entry-title">
		<a href="<?php echo getLink($entry->link); ?>" rel="external nofollow" target="_blank" title="<?php $entry->title ?>"><?php echo $entry->title ?></a>
		<a class="feed-entry-link" style="background-image: url(http://g.etfv.co/<?php echo $entry->siteUrl ?>)" title="Ir al home del sitio original" rel="external nofollow" href="<?php echo $entry->siteUrl ?>"><?php echo $entry->siteUrl ?></a>
	</h3>
	<h5 class="feed-entry-meta">Por <?php echo $entry->author ?> | <?php echo timeAgo(strtotime($entry->publishedDate)) ?></h5>
	<div class="feed-entry-content"><?php echo $entry->contentSnippet ?></div>
</li>