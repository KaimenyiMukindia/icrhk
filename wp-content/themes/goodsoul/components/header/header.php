<?php
	$header_tob_bar_style                = goodsoul_get_options( 'header_tob_bar_style' );
	$goodsoul_theme_metabox_header_style = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_header_style', true );
if ( ! empty( $goodsoul_theme_metabox_header_style ) ) :
	if ( $goodsoul_theme_metabox_header_style == 1 ) :
		get_template_part( 'components/header/header-list/header-one' );
		elseif ( $goodsoul_theme_metabox_header_style == 2 ) :
			get_template_part( 'components/header/header-list/header-two' );
		elseif ( $goodsoul_theme_metabox_header_style == 3 ) :
			get_template_part( 'components/header/header-list/header-three' );
		elseif ( $goodsoul_theme_metabox_header_style == 4 ) :
			get_template_part( 'components/header/header-list/header-four' );
		elseif ( $goodsoul_theme_metabox_header_style == 5 ) :
			get_template_part( 'components/header/header-list/header-five' );
		elseif ( $goodsoul_theme_metabox_header_style == 6 ) :
			get_template_part( 'components/header/header-list/header-six' );
		endif;
	else :
		if ( $header_tob_bar_style == 1 ) :
			get_template_part( 'components/header/header-list/header-one' );
		elseif ( $header_tob_bar_style == 2 ) :
			get_template_part( 'components/header/header-list/header-two' );
		elseif ( $header_tob_bar_style == 3 ) :
			get_template_part( 'components/header/header-list/header-three' );
		elseif ( $header_tob_bar_style == 4 ) :
			get_template_part( 'components/header/header-list/header-four' );
		elseif ( $header_tob_bar_style == 5 ) :
			get_template_part( 'components/header/header-list/header-six' );
		elseif ( $header_tob_bar_style == 6 ) :
				get_template_part( 'components/header/header-list/header-five' );
		else :
			get_template_part( 'components/header/header-list/header-one' );
		endif;
	endif;
