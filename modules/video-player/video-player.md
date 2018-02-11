# Theme Toolbox - Video Player

***Copy and Hack This Module!***

## Overview

Creates an HTML5 video player outside of the WP Core player, designed for customization by developers.

Much effort has been made to ensure JS and CSS support by modern browsers *(IE 11+ specifically, because I know that's what you're wondering about)*

## Features

* Custom Styling of the browser's HTML5 `video` element.
* Play, pause, rewind and fast-forward functionality
* Play button overlay on pause
* Progress bar
* Time elapsed/video length display
* Full-screen option
* Loading animation
* Video quality selection (*e.g., 1080p, HD, SD, Low*)
  * By default, the quality is determined by screen resolution.
  * If that video quality value is changed by the user, the selection is stored in a JS cookie for the next pages viewed.

## Files

***`class-video-player.php`***

The main Video Player file.  Includes a method to render the video player.  Ideally, large blocks of markup like this should be loaded through a template file.

***`class-video-metaboxes.php`***

Renders and saves the video file URL inputs on _Pages_.  Pretty standard WP Metabox functionality, but with a bit of OOP flair.

***`js/coookie.js`***

JavaScript API for handling cookies.  See [JS Cookie](https://github.com/js-cookie/js-cookie).

***`js/video-player.js`***

JS handler for the player.  If you're new to working with HTML5 Video, I highly recommend checking this out.

***`css/video-player.scss`***

Custom styles for the rendered markup, including the `video` element.

***`css/video-player.css`***

CSS version of `css/video-player.scss`.

## Notes

* The metabox inputs use file URLs (not the WP Media Uploader) so that externally-hosted videos can be used (e.g., hosted on a CDN).


