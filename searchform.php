<?php
/**
 * @package WordPress
 * @subpackage Adapt Theme
 */

// get search type
$search_type = ( get_query_var('post_type') ) ? get_query_var('post_type') : 'items';

?>
<form id="searchbar" method="get" action="<?php echo home_url( '/' ); ?>">
	<div class="header-options row1">
		<div class="header-cell header">
			<h4>Search</h4>
		</div>
		<div class="header-cell radio">
			<input type="radio" name="post_type" value="items" <?php if( $search_type == 'items' ) echo 'checked="checked"'; ?>>
		</div>
		<div class="header-cell text">
			Store
		</div>
		<div class="header-cell radio">
			<input type="radio" name="post_type" value="any" <?php if( $search_type == 'any' ) echo 'checked="checked"'; ?>>
		</div>
		<div class="header-cell text">
			Site
		</div>
	</div>
	<div class="header-options row2">
		<div class="header-cell input">
			<input type="text" size="16" name="s" value="<?php _e( 'SEARCH', 'adapt' ); ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" id="search" />
		</div>
		<div class="header-cell icon">
			<input type="submit" id="search-button" value="Search" />	
		</div>
	</div>
	<div class="clear"></div>
</form>