<section class="feed" id="<?php echo nameToId($name) ?>">
	<hgroup>
		<h2 class="feed-title"><a href="<?php echo $feed->link ?>" title="<?php $feed->title ?>"><?php echo $feed->title ?></a></h2>
		<h4 class="feed-description"><?php echo $feed->description ?></h4>
	</hgroup>
	<ul class="entries">
	<?php foreach ($feed->entries as $entry) {
		include 'template-entry.php';
	}; ?>
	</ul>
</section>