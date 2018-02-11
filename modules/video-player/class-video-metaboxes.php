<?php
/**
 * Class Video_Metaboxes
 *
 * Creates and Saves a metabox for the Video Player module.
 *
 * This metabox creates text inputs to save Video file URLs hosted
 * externally (Vimeo, Amazon S3, etc).
 *
 * Note that this only renders and saves on Pages (not posts).
 *
 * @package Theme_Toolbox
 * @since   0.1.0
 * @author  John Regan <john@johnregan3.com>
 */

namespace Theme_Toolbox;

require_once './class-video-metaboxes.php';

/**
 * Class Video_Metaboxes
 *
 * @package Theme_Toolbox
 * @since   0.1.0
 */
class Video_Metaboxes extends Singleton {

	/**
	 * Array of video qualities and labels.
	 *
	 * The values are in slug => label format.
	 *
	 * @var array Array of video qualities and labels.
	 */
	public $video_qualities = array();

	/**
	 * Array of Nonce data.
	 *
	 * @var array
	 */
	public $nonce = array(
		'name'   => 'tt-video-player',
		'action' => 'save-data',
	);

	/**
	 * Initialize the class.
	 *
	 * @since 0.1.0
	 */
	protected function _init() {
		$this->video_qualities = array(
			'tenEighty' => __( '1080p', 'theme-toolbox' ), // Uses snakeCase for JS processing.
			'hd'        => __( 'HD', 'theme-toolbox' ),
			'sd'        => __( 'SD', 'theme-toolbox' ),
			'low'       => __( 'Low', 'theme-toolbox' ),
		);
		add_action( 'add_meta_boxes', array( $this, 'init_metabox' ) );
		add_action( 'save_post', array( $this, 'save_video_metabox' ) );
	}

	/**
	 * Initialize the Metabox.
	 *
	 * @since 0.1.0
	 *
	 * @action add_meta_boxes
	 */
	public function init_metabox() {
		add_meta_box(
			'ttvideoplayer',
			'<span class="tt-metabox-header">' . __( 'Theme Toolbox Video Player URLs', 'theme-toolbox' ) . '</span>',
			array( $this, 'render_metabox' ),
			'page', // This only displays on pages.
			'advanced',
			'high'
		);
	}

	/**
	 * Render the Video URL Metabox.
	 *
	 * @param object $post A \WP_Post object.
	 *
	 * @since 0.1.0
	 */
	public function render_metabox( $post ) {
		$values  = get_post_meta( $post->ID, 'video_qualities', true );
		$length  = get_post_meta( $post->ID, 'video_length', true );
		$length  = ! empty( $length ) ? $length : '';

		// Don't forget the nonce!
		wp_nonce_field( $this->nonce['name'], $this->nonce['action'] );
		?>
		<p>
			<label for="tt-video-length"><?php echo esc_html_e( 'Video Length', 'theme-toolbox' ); ?></label><br />
			<input type="text" name="tt-video-length" value="<?php echo esc_html( $length ); ?>" />
		</p>

		<div id="video-url-container">
			<?php foreach ( $this->video_qualities as $quality => $label ) : ?>
				<?php $value = ! empty( $values[ $quality ] ) ? $values[ $quality ] : ''; ?>
				<?php $label = 'tt-video-quality[' . $quality . ']'; ?>
				<p>
					<label for="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></label><br />
					<input type="text" class="widefat" name="<?php echo esc_attr( $label ); ?>" value="<?php echo esc_url( $value ); ?>" />
				</p>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Save the Video Source URL Metabox.
	 *
	 * @since  0.1.0
	 *
	 * @action save_post
	 *
	 * @param int $post_id A \WP_Post ID.
	 *
	 * @return void
	 */
	public function save_metabox( $post_id ) {
		/*
		 * Nonce check.
		 *
		 * Note that all input vars ($_POST, $_GET, etc) must be unslashed and sanitized before processing.
		 */
		if ( ! isset( $_POST[ $this->nonce['action'] ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $this->nonce['action'] ] ) ), $this->nonce['name'] ) ) { // WPCS: Input var okay.
			return;
		}

		// Bail on Autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Bail if doing AJAX.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Bail if Post Revision.
		if ( false !== wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Check user capabilities.
		if ( ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Save the video length field.
		$length = '';
		if ( ! empty( $_POST['tt-video-length'] ) ) { // WPCS: Input var okay.
			$length = sanitize_text_field( wp_unslash( $_POST['tt-video-length'] ) );
		}

		// Save the video length into the "video_length" post meta field.
		update_post_meta( $post_id, 'video_length', $length );

		/*
		 * Load the URLs into an array by video quality value.
		 *
		 * Example:
		 * array(
		 *   'hd' => 'http://hdurl.com',
		 *   'sd' => 'http://sdurl.com',
		 * )
		 */
		$videos = array();
		foreach( $this->video_qualities as $quality => $label ) {
			// Ensure we have a URL.
			if ( ! empty( $_POST['tt-video-quality'][ $quality ] ) && false !== filter_var( $_POST['tt-video-quality'][ $quality ], FILTER_VALIDATE_URL ) ) { // WPCS: Input var okay.
				$videos[ $quality ] = sanitize_text_field( wp_unslash( $_POST['tt-video-quality'][ $quality ] ) );
			} else {
				$videos[ $quality ] = '';
			}
		}

		// Save the array into the "video_qualities" post meta field.
		update_post_meta( $post_id, 'video_qualities', $videos );
	}
}