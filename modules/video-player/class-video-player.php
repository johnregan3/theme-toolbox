<?php
/**
 * Class Video_Player
 *
 * Creates a video player with multiple resolutions available.
 * For documentation, see video-player.md, found in this directory.
 *
 * @package Theme_Toolbox
 * @since   0.1.0
 * @author  John Regan <john@johnregan3.com>
 */

namespace Theme_Toolbox;

require_once './class-video-metaboxes.php';

/**
 * Class Video_Player
 *
 * @package Theme_Toolbox
 * @since   0.1.0
 */
class Video_Player extends Singleton {

	/**
	 * Initialize.
	 *
	 * @since 0.1.0
	 */
	protected function _init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue Scripts.
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @since 0.1.0
	 */
	public function enqueue() {
		wp_register_script( 'js-cookie', dirname( __FILE__ ) . '/js/js-cookie.min.js', array( 'jquery' ), '2.2.0' );
		wp_register_script( 'tt-video-player', dirname( __FILE__ ) . '/js/video-player.js', array( 'jquery', 'js-cookie' ), '0.1.0' );
		wp_add_inline_script( 'tt-video-player', 'ttVideoPlayer.init()', 'after' );
	}

	/**
	 * Render the Video Player.
	 *
	 * Ideally, markup would be separated into a template file.
	 *
	 * @since 0.1.0
	 *
	 * @param int $post_id A \WP_Post ID.
	 *
	 * @return void
	 */
	public function render( $post_id = 0 ) {
		if ( empty( $post_id ) || ! is_numeric( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( empty( $post_id ) ) {
			return;
		}

		$video_qualities = get_post_meta( $post_id, 'video_qualities', true );
		if ( empty( $video_qualities ) || ! is_array( $video_qualities ) ) {
			return;
		}
		?>
		<figure id="video-container" class="b-player expanded" data-fullscreen="false">
			<video id="video" controls preload="metadata" autoplay="autoplay" x-webkit-airplay="allow">
				<source src="<?php echo esc_url( $this->get_default_video_url( $video_qualities ) ); ?>" type="video/mp4">
			</video>
			<div id="video-controls" class="b-player__controls__wrap">
				<div id="progress-bar" class="b-player__progress-bar" title="<?php esc_html_e( 'Video Progress', 'theme-toolbox' ); ?>">
					<div class="buffered">
						<span id="buffered-amount"></span>
					</div>
					<div id="progress-element">
						<span id="progress-amount"></span>
					</div>
				</div>
				<div class="b-player__controls">
					<div id="playpause" class="action playpause pause" title="<?php esc_html_e( 'Play/Pause', 'theme-toolbox' ); ?>">
						<div class="hidden"><?php esc_html_e( 'Play/Pause', 'theme-toolbox' ); ?></div>
					</div>

					<div id="duration" class="action b-player__controls__duration">
						<span id="duration-progress" title="<?php esc_html_e( 'Time Elapsed', 'theme-toolbox' ); ?>">0:00</span>/<span id="duration-total" title="<?php esc_html_e( 'Total Duration', 'theme-toolbox' ); ?>">0:00</span>
					</div>

					<div id="mute" class="action mute" title="<?php esc_html_e( 'Toggle Mute', 'theme-toolbox' ); ?>">
						<div class="hidden"><?php esc_html_e( 'Mute/Unmute', 'theme-toolbox' ); ?></div>
					</div>

					<div id="volume-bar" class="b-player__controls__volume" title="<?php esc_html_e( 'Click or Drag to Set Volume', 'theme-toolbox' ); ?>">
						<div id="volume-progress" class="b-player__controls__volume__progress"></div>
					</div>

					<div class="b-player__controls__right">
						<div id="settings" class="action gear b-player__settings" title="<?php esc_html_e( 'Video Quality', 'theme-toolbox' ); ?>">
							<span class="hidden"><?php esc_html_e( 'Video Quality', 'theme-toolbox' ); ?></span>
							<div id="settings-overlay" class="b-player__settings__overlay hidden">
								<ul>
									<?php foreach ( Video_Metaboxes::get_instance()->video_qualities as $quality => $label ) : ?>
										<?php if ( ! empty( $meta_qualities[ $quality ] ) && false !== filter_var( $meta_qualities[ $quality ], FILTER_VALIDATE_URL ) ) : ?>
											<li id="quality-<?php echo esc_attr( $quality ); ?>" class="b-player__settings__overlay__active" data-src="<?php echo esc_url( $meta_qualities[ $quality ] ); ?>"><?php echo esc_html( $label ); ?></li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>

						<span id="full-screen" class="action full-screen" title="<?php esc_html_e( 'Toggle Fullscreen', 'theme-toolbox' ); ?>">
							<span class="hidden"><?php esc_html_e( 'Full Screen', 'theme-toolbox' ); ?></span>
						</span>
					</div>
				</div>
			</div>
			<div id="play-button" class="b-player__play-button hidden"></div>
			<div id="loader" class="b-player__loader hidden">
				<em></em>
				<em></em>
				<em></em>
				<em></em>
			</div>
		</figure>
		<?php
	}

	public function get_default_video_url( $quality_meta ) {

		foreach( Video_Metaboxes::get_instance()->video_qualities as $quality => $label ) {
			if ( ! empty( $quality_meta[ $quality ] ) && false !== filter_var( $quality_meta[ $quality ], FILTER_VALIDATE_URL ) ) {
				return $quality_meta[ $quality ];
			}
		}
		return '';
	}

}