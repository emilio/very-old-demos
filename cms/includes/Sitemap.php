<?php
class Sitemap {
	public static function generate() {
		$max_comments = (int) get_posts()->where('status', '=', 'publish')->order_by('comment_count', 'desc')->first('comment_count')->comment_count;

		if( $max_comments === 0 ) {
			$max_comments = 1; // Evitar división por cero si no hay comentarios
		}

		$posts = get_posts()->where('status', '=', 'publish')->order_by('published_at','desc')->get();
		ob_start();
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"; ?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		<url>
			<loc><?php echo Url::get() ?></loc>
			<lastmod><?php echo date('c'); ?></lastmod>
			<changefreq>daily</changefreq>
			<priority>1.0</priority>
		</url>
		<?php foreach ($posts as $post): set_current_post($post);
		?><url>
			<loc><?php the_post_url() ?></loc>
			<lastmod><?php 
				if( $lastcomment = Comment::where('post_id', '=', $post->id)->and_where('type', '=', 'comment')->and_where('approved', '=', 1)->order_by('created_at', 'desc')->first() ) {
					$lastmod = $lastcomment->created_at;
				} else {
					$lastmod = $post->updated_at;
				}
				$time_created = strtotime($post->created_at);
				echo date('c', strtotime($lastmod)); ?></lastmod>
			<changefreq><?php if( $time_created > (time() + 7 * 60 * 60 * 24) ) {
				echo 'weekly';
			} else {
				echo 'monthly';
			} ?></changefreq>
			<priority><?php 
				$priority = $post->comment_count / $max_comments; 
				echo max($priority, 0.4);
			?></priority>
		</url>
		<?php endforeach // acabados los posts ?>
		<?php if( defined('INCLUDE_DEMOS_IN_SITEMAP') && INCLUDE_DEMOS_IN_SITEMAP ): 
			foreach (glob(BASE_PATH . 'demos/*', GLOB_ONLYDIR) as $dir):
				$url = Url::get() . 'demos/' . $dir . '/';
				if( file_exists($file = $dir . 'index.php') || file_exists($file = $dir . 'index.html') ) {
					$lastmod = date('c', filemtime($file));
				} else {
					$lastmod = null;
				}
				$changefreq = 'never';
				$priority = 0.6; 
			?><url>
				<loc><?php echo $url ?></loc>
				<?php if( $lastmod ): ?>
				<lastmod><?php echo $lastmod ?></lastmod>
				<?php endif; ?>
				<changefreq><?php echo $changefreq ?></changefreq>
				<priority><?php echo $priority ?></priority>
			</url>
			<?php
				endforeach;
			endif;?>
	</urlset><?php
		$sitemap = ob_get_clean();
		return $sitemap;
	}
}