// Define module directory names.
var modules = [
    'video-player',
];

// Defining requirements
var gulp    = require( 'gulp' );
var plumber = require( 'gulp-plumber' );
var sass    = require( 'gulp-sass' );
var watch   = require( 'gulp-watch' );
var batch   = require( 'gulp-batch' );

/**
 * Run: gulp watch
 *
 * This is where the action happens.
 *
 * Watches the modules directory for a {module slug}/scss/{module slug}.scss file,
 * then writes css to its css directory -- {module slug}/css/{module slug}.css.
 */
gulp.task( 'watch', function() {
    var modulesLength = modules.length;

    for ( var i = 0; i < modulesLength; i ++ ) {
        var path = './modules/' + modules[ i ] + '/scss/*.scss';

        gulp.src( path )
        .pipe( watch( path, batch( function( events, done ) {
                gulp.start( 'build', done );
            } )
        ) )
        .pipe(
            plumber( {
                errorHandler: function( err ) {
                    console.log( err );
                    this.emit( 'end' );
                }
            } )
        )
        .pipe(
            sass( {
                outputStyle: 'expanded'
            } )
        )
        .pipe(
            gulp.dest( './modules/' + modules[ i ] + '/css' )
        );
    }
} );

gulp.task( 'build', function() {
    console.log( 'compiling sass...' );
} );