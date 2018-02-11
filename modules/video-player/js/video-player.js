/* globals jQuery Cookies */
// Based heavily on https://developer.mozilla.org/en-US/Apps/Fundamentals/Audio_and_video_delivery/cross_browser_video_player.

var ttVideoPlayer = (function( $, Cookies ) {
    'use strict';

    var self = {
        container: {},
        containerEl: {},
        loader: {},
        controls: {},
        playpause: {},
        playbutton: {},
        mute: {},
        settings: {},
        settingsOverlay: {},
        volumeBar: {},
        progressBar: {},
        fullScreen: {},
        volumeDrag: false
    };

    self.init = function() {

        // Check if the video element is actually supported.
        var supportsVideo = ! ! document.createElement( 'video' ).canPlayType;
        if ( ! supportsVideo ) {
            return;
        }

        $( document ).ready( function() {
            if ( $( '#video' ).length < 1 ) {
                return;
            }
            self.player          = $( '#video' );
            self.video           = self.player[ 0 ];
            self.source          = $( 'source', self.player );
            self.container       = $( '#video-container' );
            self.containerEl     = self.container[ 0 ];
            self.loader          = $( '#loader' );
            self.controls        = $( '#video-controls' );
            self.playpause       = $( '#playpause' );
            self.playButton      = $( '#play-button' );
            self.mute            = $( '#mute' );
            self.settings        = $( '#settings' );
            self.settingsOverlay = $( '#settings-overlay' );
            self.volumeBar       = $( '#volume-bar' );
            self.fullScreen      = $( '#full-screen' );
            self.progressBar     = $( '#progress-bar' );

            // Hide the default controls
            self.video.controls = false;

            // Display our custom video controls.
            self.controls.show();

            // If the browser doesn't support the Fulscreen API, hide the full screen button.
            var fullScreenEnabled = ! ! ( document.fullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled || document.webkitSupportsFullscreen || document.webkitFullscreenEnabled || document.createElement( 'video' ).webkitRequestFullScreen );
            if ( ! fullScreenEnabled ) {
                self.fullScreen.hide();
            }

            self.setVideoQuality();

            self.listen();

            // If autoplay is disabled, ensure the play button shows.
            setTimeout( function() {
                if ( ( self.video.paused || true !== self.player.prop( 'autoplay' ) ) ) {
                    self.playpause.addClass( 'play' ).removeClass( 'pause refresh' );
                    self.playButton.removeClass( 'hidden' );
                }
            }, 2000 );
        } );
    };

    self.setVideoQuality = function() {
        var quality, hasSource = {
            low: ( $( '#quality-low' ).length > 0 ),
            sd: ( $( '#quality-sd' ).length > 0 ),
            hd: ( $( '#quality-hd' ).length > 0 ),
            tenEighty: ( $( '#quality-tenEighty' ).length > 0 )
        };

        var cookieQuality = Cookies.get( 'entreXVideoQuality' );
        if ( hasSource[ cookieQuality ] ) {
            quality = cookieQuality;
        } else if ( hasSource.low && window.devicePixelRatio < 2 && $( window ).width() < 500 ) {
            // if not retina AND small device, load low.
            quality = 'low';
        } else if ( hasSource.sd && window.devicePixelRatio < 2 ) {
            // if not retina AND NOT small device, load SD.
            quality = 'sd';
        } else if ( hasSource.hd && $( window ).width < 375 ) {
            // if retina and small screen (iPhone ), load HD.
            quality = 'hd';
        } else if ( hasSource.tenEighty ) {
            // if retina and desktop (above 375px).
            self.loadVideo( 'tenEighty' );
        } else if ( hasSource.hd ) {
            // Fallback.
            quality = 'hd';
        } else if ( hasSource.sd ) {
            // Fallback.
            quality = 'sd';
        } else if ( hasSource.low ) {
            // Fallback.
            quality = 'low';
        }

        if ( quality ) {
            self.loadVideo( quality );
        }
    };

    self.listen = function() {

        self.player.on( 'loadedmetadata', function() {
            // Set the duration value.
            $( '#duration-total' ).html( self.prettyTime( self.video.duration ) );
        } );

        self.playButton.on( 'click', function() {
            self.playpause.trigger( 'click' );
        } );

        // Show the loader animation.
        self.player.on( 'waiting', function() {
            self.loader.removeClass( 'hidden' );
        } );

        self.player.on( 'playing', function() {
            self.loader.addClass( 'hidden' );
            self.playButton.addClass( 'hidden' );
        } );

        self.playpause.on( 'click', function() {
            if ( self.video.paused || self.video.ended ) {
                self.video.play();
                self.playpause.addClass( 'pause' ).removeClass( 'play refresh' );
                self.playButton.addClass( 'hidden' );
            } else {
                self.video.pause();
                self.playpause.addClass( 'play' ).removeClass( 'pause refresh' );
                self.playButton.removeClass( 'hidden' );
            }
        } );

        self.settings.on( 'click', function( e ) {
            if ( $( e.target ).is( 'div' ) ) {
                self.settingsOverlay.toggleClass( 'hidden' );
            }
        } );

        // Switch video resolution.
        $( 'li', self.settingsOverlay ).click( function() {
            var idString = $( this ).attr( 'id' );
            var regex    = /(quality-(\w+))/g;
            var found    = regex.exec( idString );
            if ( 'string' === typeof found[ 2 ] ) {
                self.loadVideo( found[ 2 ] );
                self.settingsOverlay.addClass( 'hidden' );
            }
        } );

        self.mute.on( 'click', function() {
            if ( self.video.muted ) {
                $( '#volume-progress' ).css( 'width', self.video.volume * 100 + '%' );
            } else {
                $( '#volume-progress' ).css( 'width', '0' );
            }
            self.video.muted = ! self.video.muted;
            $( this ).toggleClass( 'muted' );
        } );

        // Volume Slider: http://jsfiddle.net/onigetoc/r44bzmc1/
        self.volumeBar.on( 'mousedown', function( e ) {
            self.volumeDrag  = true;
            self.video.muted = false;
            self.updateVolume( e.pageX );
        } );

        // This is on document so the volume bar will stop sliding anywhere.
        $( document ).on( 'mouseup', function( e ) {
            if ( self.volumeDrag ) {
                self.volumeDrag = false;
                self.updateVolume( e.pageX );
            }
        } );

        self.controls.on( 'mousemove', function( e ) {
            if ( self.volumeDrag ) {
                self.updateVolume( e.pageX );
            }
        } );

        self.progressBar.on( 'click', function( e ) {
            var playerLeft         = self.player.offset().left;
            self.video.currentTime = ( (e.pageX - this.offsetLeft - playerLeft ) / this.offsetWidth ) * self.video.duration;
        } );

        // Update the progress bar and timer.
        self.player.on( 'timeupdate', function() {
            if ( self.video.duration > 0 ) {
                // Timer.
                // https://developer.mozilla.org/en-US/Apps/Fundamentals/Audio_and_video_delivery/buffering_seeking_time_ranges
                $( '#duration-progress' ).html( self.prettyTime( self.video.currentTime ) );

                // Progress bar.
                self.updateProgress();
                for ( var i = 0; i < video.buffered.length; i ++ ) {
                    if ( self.video.buffered.start( self.video.buffered.length - 1 - i ) < self.video.currentTime ) {
                        $( '#buffered-amount' ).css( { 'width': (video.buffered.end( self.video.buffered.length - 1 - i ) / self.video.duration) * 100 + '%' } );
                        break;
                    }
                }
            }
        } );

        self.player.on( 'ended', function() {
            self.playpause.addClass( 'refresh' ).removeClass( 'pause' );
            self.playButton.removeClass( 'hidden' );
        } );

        self.fullScreen.on( 'click', function() {
            self.handleFullscreen();
        } );

        // Listen for fullscreen change events (from other controls, e.g. right clicking on the video itself)
        $( document ).on( 'fullscreenchange', function() {
            self.setFullscreenData( ! ! (document.fullScreen || document.fullscreenElement) );
        } );

        $( document ).on( 'webkitfullscreenchange', function() {
            self.setFullscreenData( ! ! document.webkitIsFullScreen );
        } );

        $( document ).on( 'mozfullscreenchange', function() {
            self.setFullscreenData( ! ! document.mozFullScreen );
        } );

        $( document ).on( 'msfullscreenchange', function() {
            self.setFullscreenData( ! ! document.msFullscreenElement );
        } );
    };

    // Set the video container's full screen state.
    self.setFullscreenData = function( state ) {
        self.container.attr( 'data-fullscreen', ! ! state );
    };

    // Check if the document is currently in full screen mode.
    self.isFullScreen = function() {
        return ! ! (document.fullScreen || document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || document.fullscreenElement);
    };

    self.handleFullscreen = function() {
        // Exit full screen.
        if ( self.isFullScreen() ) {
            // (Note: this can only be called on document)
            if ( document.exitFullscreen ) {
                document.exitFullscreen();
            } else if ( document.mozCancelFullScreen ) {
                document.mozCancelFullScreen();
            } else if ( document.webkitCancelFullScreen ) {
                document.webkitCancelFullScreen();
            } else if ( document.msExitFullscreen ) {
                document.msExitFullscreen();
            }
            self.setFullscreenData( false );
        }
        else {
            // Enter full screen mode.
            // Note: This can be called on document, but here the specific element is
            // used as it will also ensure that the element's children ( e.g. the custom controls ) go fullscreen also.
            if ( self.containerEl.requestFullscreen ) {
                self.containerEl.requestFullscreen();
            } else if ( self.containerEl.mozRequestFullScreen ) {
                self.containerEl.mozRequestFullScreen();
            } else if ( self.containerEl.webkitRequestFullScreen ) {
                self.video.webkitRequestFullScreen();
            } else if ( self.containerEl.msRequestFullscreen ) {
                self.containerEl.msRequestFullscreen();
            }
            self.setFullscreenData( true );
        }
    };

    self.loadVideo = function( size ) {
        var wasPlaying = ! self.video.paused && ! self.video.ended;
        if ( 'string' !== typeof size ) {
            return;
        }

        if ( $( 'li#quality-' + size, self.settingsOverlay ).length < 1 ) {
            return;
        }

        // Check if this size is already playing.
        if ( self.source.hasClass( size ) ) {
            return;
        }

        // Get the url we need and cache the current video position.
        var selected     = $( 'li#quality-' + size, self.settingsOverlay ),
            url          = selected.attr( 'data-src' ),
            originalTime = 0;

        if ( 'number' === typeof self.video.currentTime ) {
            originalTime = self.video.currentTime;
        }

        $( 'li', self.settingsOverlay ).each( function() {
            $( this ).removeClass( 'b-player__settings__overlay__active' );
        } );
        selected.addClass( 'b-player__settings__overlay__active' );

        // Set the preferred video quality.
        Cookies.set( 'entreXVideoQuality', size );

        self.video.pause();

        // Load up the new video URL.
        self.source.attr( 'src', url ).removeClass( 'tenEighty hd sd low' ).addClass( size );
        self.video.load();
        self.updateProgress();

        if ( document.readyState === 'complete' ) {

            if ( wasPlaying || true === self.player.prop( 'autoplay' ) ) {
                self.video.play();
            }

            // Jump back to the former video position.
            self.video.currentTime = parseInt( originalTime, 10 );
        }

    };

    self.updateProgress = function() {
        $( '#progress-amount' ).css( { 'width': Math.floor( ( self.video.currentTime / self.video.duration ) * 100 ) + '%' } );
    };

    // http://jsfiddle.net/onigetoc/r44bzmc1/
    self.updateVolume = function( x, vol ) {
        var percentage;
        if ( vol ) {
            percentage = vol * 100;
        } else {
            var position = x - self.volumeBar.offset().left;
            percentage   = 100 * position / self.volumeBar.width();
        }

        if ( isNaN( percentage ) || percentage > 100 ) {
            percentage = 100;
        }
        if ( percentage < 0 ) {
            percentage = 0;
        }

        $( '#volume-progress' ).css( 'width', percentage + '%' );
        self.video.volume = percentage / 100;
    };

    // Outputs "1:01" or "4:03:59" or "123:03:59".
    // https://stackoverflow.com/questions/3733227/javascript-seconds-to-minutes-and-seconds
    self.prettyTime = function( time ) {
        var ret  = '',
            hrs  = ~ ~ (time / 3600),
            mins = ~ ~ ((time % 3600) / 60),
            secs = Math.floor( time % 60 );

        if ( hrs > 0 ) {
            ret += '' + hrs + ':' + (mins < 10 ? '0' : '');
        }

        ret += '' + mins + ':' + (secs < 10 ? '0' : '');
        ret += '' + secs;
        return ret;
    };

    return self;

})( jQuery, Cookies );
