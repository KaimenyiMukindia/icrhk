<div class="search-box-outer">
	<div class="dropdown">
		<button class="search-box-btn dropdown-toggle" type="button" id="dropdownMenu4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-search"></span></button>
		<ul class="dropdown-menu pull-right search-panel" aria-labelledby="dropdownMenu4">
			<li class="panel-outer">
				<div class="form-container">
					<form method="post" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<div class="form-group">
							<input type="search" name="s" value="" placeholder="<?php esc_attr_e( 'Search....', 'goodsoul' ); ?>" required="">
							<button type="submit" value="<?php esc_attr_e( 'Search Now!', 'goodsoul' ); ?>" class="search-btn"><span class="fa fa-search"></span></button>
						</div>
					</form>
				</div>
			</li>
		</ul>
	</div>
</div>
