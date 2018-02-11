// Defining base pathes
var basePaths = {
    modules: './modules/'
};


// Defining requirements
var gulp         = require( 'gulp' );
var plumber      = require( 'gulp-plumber' );
var sass         = require( 'gulp-sass' );
var watch        = require( 'gulp-watch' );
var batch        = require( 'gulp-batch' );

var modules = [
    'video-player'
];

gulp.task('build', function () {
    console.log('Compiling Sass...');
});

/**
 * Run: gulp watch
 *
 * This is where the action happens.
 *
 * Run this to watch the development files in /src, generate the minified versions
 * where appropriate, and update Browser Sync.
 *
 * Starts the watcher for all .scss and js files, and minimizes images.
 */
gulp.task( 'watch', function() {
    var modulesLength = modules.length;

    for (var i = 0; i < modulesLength; i++) {
        var path = basePaths.modules + modules[ i ] + '/scss/*.scss';

        gulp.src( path )
        .pipe(
            watch( path, batch( function (events, done) {
                gulp.start('build', done);
            })
        ))
        .pipe(
            plumber( {
                errorHandler: function( err ) {
                    console.log( err );
                    this.emit( 'end' );
                }
            } )
        )
        .pipe( sass() )
        .pipe(
            gulp.dest( basePaths.modules + modules[ i ] + '/css' )
        );
    }
} );