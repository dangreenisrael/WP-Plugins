<?php
/**
 * Plugin Name: WooCommerce - List Products by Tags
 * Description: List WooCommerce products by tags using a shortcode, ex: [woo_products_by_tags tags="shoes,socks"]
 * Version: 1.0
 * Author: Dan Green via Remi Corson
 * Dan Green's URI: http://tlvwebdevelopment.com
 * Remi Corson's URI: http://remicorson.com
 *
 * Initial GIST: https://gist.github.com/corsonr/5933479#file-gistfile1-phtml
 * 
 * Requires at least: 3.5
 * Tested up to: 3.7
 *
 *
 */
 
/*
 * List WooCommerce Products by tags
 *
 * ex: [woo_products_by_tags tags="shoes,socks"]
 */
function woo_products_by_tags_shortcode( $atts, $content = null ) {
  
	// Get attribuets
	extract(shortcode_atts(array(
		"tags" => ''
	), $atts));
	
	ob_start();
 
	// Define Query Arguments
	$args = array( 
				'post_type' 	 => 'product', 
				'posts_per_page' => 5, 
				'product_tag' 	 => $tags
				);
	
	// Create the new query
	$loop = new WP_Query( $args );
	
	// Get products number
	$product_count = $loop->post_count;
	
	// If results
	if( $product_count > 0 ) :
	
		echo '<ul class="products ribbon">';
		
			// Start the loop
			while ( $loop->have_posts() ) : $loop->the_post(); global $product;
				global $post;
				++$i;
				if ($i == 5) $last = "last";
				$ID = $post->ID;	
				$title = $post->post_title;
				$price = $product->get_price_html();
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'single-post-thumbnail' );
				$image = $image[0];
				$link = get_permalink($ID);
				
				echo "
				<li class='post-$ID product type-product status-publish hentry product featured instock $last'>
				
							
					<a href='$link' title='$title'>
						
				        <div class='thumbnail'>
				        	<img src='$image' class='attachment-post-thumbnail wp-post-image' alt='$title' />			        
				         <div class='thumb-shadow'></div>
				    			
			    		<strong class='below-thumb'> 
			    		$title
			    		</strong>    	
				    </div>
				
						
					<span class='price'><span class='from'></span><span class='amount'>$price</span></span>
					
					</a>
					
					
					
					<div class='buttons'>
				        	    <a href='$link' rel='nofollow' data-product_id='$ID' class='add-to-cart add_to_cart_button product_type_variable'>Select options</a></div>			
				</li>";
					
			endwhile;
			
			
		
		echo '</ul><!--/.products-->';
	
	else :
	
		_e('No product matching your criteria.');
	
	endif; // endif $product_count > 0
	
	return ob_get_clean();
 
}
 
add_shortcode("woo_products_by_tags", "woo_products_by_tags_shortcode");