<h1>¿Qué es <?php echo $search_term ?>?</h1>

<div class="row">
	<div class="adsense adsense-1"></div>

	<div class="module module-wikipedia">
		<h2 class="module-title"><?php echo $search_term ?> (Wikipedia)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->wikipedia->search as $result): ?>
				<li class="module-result">
					<h3 class="module-result-title">
						<a target="_blank" href="http://es.wikipedia.org/wiki/<?php echo str_replace(' ', '_', $result->title) ?>" title="<?php echo $result->title ?> en WikiPedia"><?php echo $result->title ?></a>
					</h3>
					<p class="module-result-snippet"><?php echo $result->snippet ?></p>
			<?php endforeach; ?> 
		</ul>
	</div><!-- /.module-wikipedia -->

</div>
<div class="row">
	<div class="module module-googleimages">
		<h2 class="module-title"><?php echo $search_term ?> (Google imágenes)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->googleimages->results as $result): ?>
				<li class="module-result">
					<a target="_blank" href="<?php echo $result->url ?>">
						<img height="<?php echo $result->tbWidth ?>" width="<?php echo $result->tbWidth ?>" src="<?php echo $result->tbUrl ?>" alt="<?php echo $result->title ?>" title="<?php echo $result->title ?>">
					</a>
			<?php endforeach; ?> 
		</ul>
	</div><!-- /.module-googleimages -->

</div>

<div class="row">
	<div class="adsense adsense-2"></div>
	<div class="module module-googlenews">
		<h2 class="module-title"><?php echo $search_term ?> (Google noticias)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->googlenews->results as $result): ?>
				<li class="module-result">
					<h3 class="module-result-title">
						<a target="_blank" href="<?php echo $result->url ?>" title="<?php echo htmlspecialchars($result->titleNoFormatting) ?>"><?php echo $result->title ?></a>
					</h3>
					<p class="module-result-snippet"><?php echo $result->content ?></p>
			<?php endforeach; ?> 
		</ul>
	</div><!-- /.module-googlenews -->
</div>

<div class="row">
	<div class="module module-youtube">
		<h2 class="module-title"><?php echo $search_term ?> (YouTube)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->youtube->items as $result): ?>
				<li class="module-result">
					<a href="http://youtu.be/<?php echo $result->id->videoId ?>" target="_blank" title="<?php echo htmlspecialchars($result->snippet->title) ?>">
						<img src="<?php echo $result->snippet->thumbnails->default->url ?>" alt="<?php echo htmlspecialchars($result->snippet->title) ?>">
					</a>
			<?php endforeach; ?>
		</ul>
	</div><!-- /.module-youtube -->

	
</div>


<div class="row">
	<div class="adsense adsense-3"></div>
	<div class="module module-googleblogs">
		<h2 class="module-title"><?php echo $search_term ?> (Blogs)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->googleblogs->results as $result): ?>
				<li class="module-result">
					<?php if( isset($result->image) ): ?>
						<img alt="<?php echo $result->displayName ?>" src="<?php echo $result->image->url ?>" class="module-result-thumbnail">
					<?php endif; ?>
					<h3 class="module-result-title">
						<a target="_blank" href="<?php echo $result->postUrl ?>" title="<?php echo htmlspecialchars($result->titleNoFormatting)  ?> en <?php echo $result->blogUrl ?>"><?php echo $result->title ?></a>
					</h3>
					<p class="module-result-snippet"><?php echo $result->content ?></p>
			<?php endforeach; ?> 
		</ul>
	</div><!-- /.module-googleblogs -->
</div>

<div class="row">
	
	<div class="module module-googleplus span4">
		<h2 class="module-title"><?php echo $search_term ?> (Google+)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->googleplus->items as $result): ?>
				<li class="module-result">
					<h3 class="module-result-title">
						<a target="_blank" href="<?php echo $result->url ?>" title="<?php echo $result->displayName ?> en Google+"><?php echo $result->displayName ?></a>
					</h3>
					<!-- No snippet -->
			<?php endforeach; ?> 
		</ul>
	</div><!-- /.module-googleplus -->


	<div class="module module-twitter span4">
		<h2 class="module-title"><?php echo $search_term ?> (Twitter)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->twitter->statuses as $tweet): ?>
				<li class="module-result">
					<a href="http://twitter.com/<?php echo $tweet->user->screen_name ?>" target="_blank" title="@<?php echo $tweet->user->screen_name ?>">
						<img src="<?php echo $tweet->user->profile_image_url ?>" alt="@<?php echo $tweet->user->screen_name ?>" class="module-result-thumbnail">
					</a>
					<p class="tweet-text">
						<a href="http://twitter.com/<?php echo $tweet->user->screen_name ?>" target="_blank" title="<?php echo $tweet->user->name ?>">@<?php echo $tweet->user->screen_name ?></a>:
						<?php 
							/** Escape */
							$tweet->text = htmlspecialchars($tweet->text);
							/** Linkify tweets */
							$tweet->text = preg_replace('/http:\/\/t.co\/[a-zA-Z0-9]+/', '<a target="_blank" href="$0">$0</a>', $tweet->text);
							/** Mentions */
							$tweet->text = preg_replace('/@([0-9A-Za-z_]+)/', '<a target="_blank" href="http://twitter.com/$1">$0</a>', $tweet->text);
							echo $tweet->text;
						?>
						<a class="tweet-link" href="http://twitter.com/<?php echo $tweet->user->screen_name ?>/status/<?php echo $tweet->id_str ?>" target="_blank">
							<time datetime="<?php echo date('c', strtotime($tweet->created_at)) ?>"><?php echo date('d/m/Y', strtotime($tweet->created_at)) ?></time>
						</a>
					</p>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="module module-facebook span4">
		<h2 class="module-title"><?php echo $search_term ?> (Facebook)</h2>
		<ul class="module-results">
			<?php foreach ($search_results->facebook->data as $result): ?>
				<li class="module-result">
					<a href="http://facebook.com/<?php echo $result->from->id ?>" target="_blank" title="<?php echo $result->from->name ?> en Facebook">
						<?php echo $result->from->name ?>
					</a>:
					<p class="fb-status-text">
						<?php 
						echo htmlspecialchars($result->message) . '&hellip;'
						?>
						<a class="fb-status-link" href="http://facebook.com/<?php echo $result->id ?>" target="_blank">
							<time datetime="<?php echo date('c', strtotime($result->created_time)) ?>"><?php echo date('d/m/Y', strtotime($result->created_time)) ?></time>
						</a>
					</p>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

