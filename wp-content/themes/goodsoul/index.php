<?php
	$blog_page_header      = goodsoul_get_options( 'blog_page_header' );
	$blog_page_header_text = goodsoul_get_options( 'blog_page_header_text' );
	$blog_page_header_img  = goodsoul_get_options( 'blog_page_header_img' );
	$blog_page_breadcrumbs = goodsoul_get_options( 'blog_page_breadcrumbs' );

	$gqv_blos_tyle = get_query_var('blog_style');
	$blog_style_redux = goodsoul_get_options( 'blog_style' );
	if($gqv_blos_tyle){
		$blog_style = $gqv_blos_tyle;
	}elseif(!empty($blog_style_redux)){
		$blog_style = $blog_style_redux;
	}else{
		$blog_style = '3';
	}
?>
<?php get_header(); ?>
	<?php
		switch ( $blog_style ) {
			case '1':
				get_template_part( 'blog', 'gridview' );
				break;
			case '2':
					get_template_part( 'blog', 'masonary' );
				break;
			case '3':
				get_template_part( 'blog', 'withsidebar' );
				break;
			default:
		}
	?>
<?php get_footer(); ?>
