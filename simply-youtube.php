<?php
/*
Plugin Name: Simply Youtube
Plugin URI: http://rollybueno.info/simply-youtube/
Description: Simply Youtube displays video thumbnails from specific channel instead of player with bunch of frames for personalize look.
Version: 1.3.1
Author: rollybueno
Author URI: http://rollybueno.info
License: A "Slug" license name e.g. GPL2

   Copyright 2012 Rolly G. Bueno Jr.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/

function simply_youtube_script()
{
	/**
	 * Check if jquery included
	*/
	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js', array(), '1.8.1');
	wp_enqueue_script( 'jquery' );
	
  	/**
	 * jquery prettyphoto js
	 * v 3.1.4
	*/
    	wp_enqueue_script( 'jquery-prettyphoto-3.1.4', plugins_url( '/js/jquery.prettyPhoto.js',__FILE__ ), array(), '3.1.4' );
}
add_action('wp_enqueue_scripts', 'simply_youtube_script');


function simply_youtube_style()
{
	/**
	 * prettyPhoto css
	*/
	wp_register_style( 'simply-youtube-prettyphoto', plugins_url( '/css/prettyPhoto.css', __FILE__ ), array(), '1.0' );
        wp_enqueue_style( 'simply-youtube-prettyphoto' );
        
        /**
	 * simplyYoutube css
	*/
	wp_register_style( 'simply-youtube', plugins_url( '/css/simplyYoutube.css', __FILE__ ), array(), '1.0' );
        wp_enqueue_style( 'simply-youtube' );
}
add_action( 'wp_enqueue_scripts', 'simply_youtube_style' );

class simply_Youtube extends WP_Widget 
{
	/** constructor */
	function __construct() {
		parent::__construct( 
			'simply_youtube', /* Base ID */ 
			'Simply Youtube', /* Name */ 
			array( 'description' => 'Simply Youtube displays video thumbnails from specific channel instead of player with bunch of frames for personalize look.' ) 
		);
	}
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
	
		extract( $args );
		
		$title = $instance['page_title'];
			
		echo $before_widget;
		echo $before_title . $title . $after_title;
		
		$uploads = get_uploads( $instance[ 'channel' ], $instance[ 'number_of_videos' ] );
		
		echo '<div style="width: ' . $instance[ 'width' ] . 'px;" style="text-align: justify; margin-left: auto; margin-right: auto;">';
		for( $j=1; $j < count( $uploads ); $j++ ):
			$desc = '<div class="yt-vid">';
			echo '<div class="yt-description"><p id="title">' . htmlentities( $uploads[$j]['title'], ENT_QUOTES ) . '</div></p>';
			$desc .= '<a href="'. $uploads[$j]['url'] . '&controls=0" rel="prettyPhoto" title="' . htmlentities( $uploads[$j]['title'], ENT_QUOTES ) . '">';
			$desc .= '<div class="yt-content"><p class="yt-title">' . $uploads[$j]['title'] . '</p>';	
			$desc .= '<b>Views</b>: ' . number_format( $uploads[$j]['views'] ) . '<br>';
			$desc .= '<b>Likes</b>: ' . number_format( $uploads[$j]['likes'] ) . '<br>';
			$desc .= '<b>Dislikes</b>: ' . number_format( $uploads[$j]['dislikes'] ) . '<br/><br/>';
			$desc .= htmlentities( $uploads[$j]['description'], ENT_QUOTES ) . '<br/><br/>';
			$desc .= '</div>';
			echo $desc;			
			echo '<div class="imgwrap" style="background: url(\'' . $uploads[$j]['thumbnail'] . '\') no-repeat; background-size: ' . $instance[ 'width' ] . 'px ' . $instance[ 'height' ] . 'px; width: ' . $instance[ 'width' ] . 'px; height: ' . $instance[ 'height' ] . 'px;"><div class="imgframe" style=" background: url(\' ' . plugins_url( 'simply-youtube/images/frames/frame-' . $instance[ 'frame' ] . '.png' ) . '\'); background-size: ' . $instance[ 'width' ] . 'px ' . $instance[ 'height' ] . 'px; width: ' . $instance[ 'width' ] . 'px; height: ' . $instance[ 'height' ] . 'px; "></div></div></a>';			
			echo '</div>';
		endfor;
		
		echo '</div>';
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance[ 'page_title' ] = strip_tags($new_instance['page_title']);
		$instance[ 'channel' ] = strip_tags($new_instance['channel']);
		$instance[ 'number_of_videos' ] = strip_tags($new_instance['number_of_videos']);
		$instance[ 'frame' ] = strip_tags($new_instance['frame']);
		$instance[ 'width' ] = strip_tags($new_instance['width']);
		$instance[ 'height' ] = strip_tags($new_instance['height']);
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ):
			$page_title = esc_attr( $instance[ 'page_title' ] );
			$channel = esc_attr( $instance[ 'channel' ] );
			$number_of_videos = esc_attr( $instance[ 'number_of_videos' ] );
			$frame = esc_attr( $instance[ 'frame' ] );
			$width = esc_attr( $instance[ 'width' ] );
			$height = esc_attr( $instance[ 'height' ] );
		else:
			$page_title = __( 'Simply Youtube', 'text_domain' );
			$channel = __( 'AvrilLavigne', 'text_domain' );
			$number_of_videos = __( '5', 'text_domain' );
			$frame = __( '7', 'text_domain' );
			$width = __( '250', 'text_domain' );
			$height = __( '188', 'text_domain' );
		endif;
		
		?>	
			<p>
			<label for="<?php echo $this->get_field_id('page_title'); ?>"><?php _e('Widget Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('page_title'); ?>" name="<?php echo $this->get_field_name('page_title'); ?>" type="text" value="<?php echo $page_title; ?>" />
			</p>
				
			<p>
			<label for="<?php echo $this->get_field_id('channel'); ?>"><?php _e('Channel:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('channel'); ?>" name="<?php echo $this->get_field_name('channel'); ?>" type="text" value="<?php echo $channel; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('number_of_videos'); ?>"><?php _e('Videos:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('number_of_videos'); ?>" name="<?php echo $this->get_field_name('number_of_videos'); ?>" type="text" value="<?php echo $number_of_videos; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('frame'); ?>"><?php _e('Frame:'); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('frame'); ?>" name="<?php echo $this->get_field_name('frame'); ?>">	
			 <?php
			  for( $i=1; $i<=10; $i++ ):
			   echo '<option value ="' . $i . '" ' . selected( $frame, $i ) . '>' . $i . '</option>';
			  endfor;
			?>
			</select>
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
			</p>
		<?php
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget("simply_Youtube");' ) );
/**
 * call prettyphoto function
 * in theme footer
*/
function simply_Youtube_footer()
{
?>
	<script type="text/javascript" charset="utf-8">
	  jQuery(document).ready(function(){
	    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
	    	autoplay: false,
	    	social_tools: false, 
	    	theme: 'facebook',
	    	flash_markup: '<object width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
	    	});
	  });
	</script>
<?php
}

add_action( 'wp_footer', 'simply_Youtube_footer' );
/**
 * using simplexml for gdata
 * giving us unreliable output
 * need to use curl before convert to xml
*/
function syt_curl( $yt_gdata_url ) 
{
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $yt_gdata_url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	$curl_r = curl_exec( $ch );
	curl_close( $ch );
	return $curl_r;
}

/**
 * @get_uploads retrieve user's
 * uploads from Youtube yt API version 2
*/
function get_uploads( $user, $maxDetails )
{
	/**
	 * get data using curl
	*/
	$curl = syt_curl( 'http://gdata.youtube.com/feeds/api/users/' . $user . '/uploads?start-index=1&max-results=' . $maxDetails . '&v=2&prettyprint=true' );
	
	/**
	 * Convert output to
	 * array/xml.
	*/
	$xml = simplexml_load_string( $curl );
	/**
	 * Assign schemas and mrss for retrieving
	 * child nodes in array
	*/
	$schemas2007 = 'http://gdata.youtube.com/schemas/2007';
	$mrss = 'http://search.yahoo.com/mrss/';
	
	/**
	 * Retrieve information from source URL
	 * with given required function params
	*/
	$i = 0;
	$uploads = array();
	while( $i < count( $xml->entry ) ):
		
		/**
		 * Check nodes if exist
		*/
		if( $xml->entry[$i]->children( $schemas2007 )->statistics ):
			$views =  (string) $xml->entry[$i]->children( $schemas2007 )->statistics->attributes()->viewCount;
		endif;
		
		if( $xml->entry[$i]->children( $schemas2007 )->rating ):
			$likes = (string) $xml->entry[$i]->children( $schemas2007 )->rating->attributes()->numLikes;
		endif;
		
		if( $xml->entry[$i]->children( $schemas2007 )->rating ):
			$dislikes = (string) $xml->entry[$i]->children( $schemas2007 )->rating->attributes()->numDislikes;
		endif;
		
		$uploads[$i] = array( 'id' => (string) $xml->entry[$i]->id,
					'url' => (string) $xml->entry[$i]->children( $mrss )->group->player->attributes()->url,
					'title' => (string) $xml->entry[$i]->title,
					'description' => (string) $xml->entry[$i]->children( $mrss )->group->description,
					'thumbnail' => (string) $xml->entry[$i]->children( $mrss )->group->thumbnail[2]->attributes()->url,
					'duration' => (string) $xml->entry[$i]->children( $mrss )->children( $schemas2007 )->duration->attributes()->seconds,
					'views' => $views,
					'likes' => $likes,
					'dislikes' => $dislikes ) ;		
		$i++;
	endwhile;
	
		return $uploads;
}
?>