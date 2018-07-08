'use strict';
var argv = require('minimist')(process.argv.slice(2)),
    pump = require('pump'),
    gulp = require('gulp'),
    cache = require('gulp-cache'),
    watch = require('gulp-watch'),
    gutil = require('gulp-util'),
    gulpif = require('gulp-if'),
    gulpifelse = require('gulp-if-else'),
    sass = require('gulp-sass'),
    livereload = require('gulp-livereload'),
    prefix = require('gulp-autoprefixer'),
    minifyCss = require('gulp-minify-css'),
    cleanCss = require('gulp-clean-css'),
    replace = require('gulp-replace'),
    zip = require('gulp-zip'),
    concat = require('gulp-concat'),
    minifyHtml = require('gulp-minify-html'),
    runCmd = require('gulp-run'),
    imagemin = require('gulp-imagemin'),
    uglify = require('gulp-uglify'),
    useref = require('gulp-useref'),
    filter = require('gulp-filter'),
    concat = require('gulp-concat'),
    defineModule = require('gulp-define-module'),
    declare = require('gulp-declare'),
    handlebars = require('gulp-handlebars'),
    del = require('del'),
    express = require('express'),
    path = require('path'),
    opn = require('opn'),
    info = require('./package.json');

// Configuration

var Config = {
    port: 8080,
    livereload_port: 35729,
    cache: (typeof argv.cache !== 'undefined' ? !!argv.cache : false),
    imagemin: {
        optimizationLevel: 3,
        progressive: true,
        interlaced: true
    },
    paths: {
        src: {
            root: './webpackage/wp-content/themes',
            extra: [
                //'src/foo/**/*',
                //'src/bar/**/*'
            ]
        },
        build: {
            root: './wp-content/themes',
            extra: [
                //'AWS-BitBucket/foo/',
                //'AWS-BitBucket/bar/'
            ]
        }
    }
}








gulp.task('migrate', () => {
    return gulp.src(Config.paths.src.root + '/**/*')
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('migratePHP', () => {
    const phpFilter = filter('**/*.php');
    return gulp.src(Config.paths.src.root + '/**/*')
        .pipe(phpFilter)
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('migrateAfterBuild', () => {
    return gulp.src('./**/*')
        .pipe(gulp.dest('./build'));
});

gulp.task('buildSCSS', ['migrate'], () => {
    return gulp.src([Config.paths.build.root + '/**/*/main.scss', Config.paths.build.root + '/**/*/index.scss', Config.paths.build.root + '/**/*/global.scss', Config.paths.build.root + '/**/*/home.scss', Config.paths.build.root + '/**/*/style.scss'])
        .pipe(sass({
            errLogToConsole: true,
        }))
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('liveReloadSCSS', () => {
    return gulp.src([Config.paths.src.root + '/**/*/main.scss', Config.paths.src.root + '/**/*/index.scss', Config.paths.src.root + '/**/*/global.scss', Config.paths.src.root + '/**/*/home.scss', Config.paths.src.root + '/**/*/style.scss'])
        .pipe(sass({
            errLogToConsole: true,
        }))
        .pipe(gulp.dest(Config.paths.build.root));
});


gulp.task('cleanCSS', ['buildSCSS'], () => {
    return gulp.src([Config.paths.build.root + '/**/*.css', '!' + Config.paths.build.root + '/**/style.css'])
        .pipe(cleanCss({ debug: true, rebase: false }, function(details) {
            console.log(details.name + ': ' + details.stats.originalSize);
            console.log(details.name + ': ' + details.stats.minifiedSize);
        }))
        .pipe(gulp.dest(Config.paths.build.root));
});


gulp.task('uglifyJS', ['cleanCSS'], () => {
    return gulp.src([Config.paths.src.root + '/**/*/main.js', Config.paths.src.root + '/**/*/labory.js', Config.paths.src.root + '/**/*/global.js', Config.paths.src.root + '/**/*/functions.js', Config.paths.src.root + '/**/*/ajax.js'])
        .pipe(uglify())
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('liveReloadJS', () => {
    return gulp.src([Config.paths.src.root + '/**/*/main.js', Config.paths.src.root + '/**/*/labory.js', Config.paths.src.root + '/**/*/global.js', Config.paths.src.root + '/**/*/functions.js', Config.paths.src.root + '/**/*/ajax.js'])
    .pipe(uglify())
    .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('replacePHP_dev', () => {
    const phpFilter = filter('**/*.php');
    return gulp.src(Config.paths.build.root + '/**/*')
        .pipe(phpFilter)
        // .pipe(replace('http://fj-dev.nativesdev.com.au', 'http://localhost'))
        .pipe(replace('http://localhost', 'http://fj-dev.nativesdev.com.au'))
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('replaceSQL_dev', () => {
    const sqlFilter = filter('**/*.sql');
    return gulp.src('./wp-db/fashionJournalDb.sql')
        // .pipe(replace('http://fj-dev.nativesdev.com.au', 'http://localhost'))
        .pipe(replace('http://localhost', 'http://fj-dev.nativesdev.com.au'))
        .pipe(gulp.dest('./wp-db'));
});

gulp.task('replaceHTML_dev', () => {
    const htmlFilter = filter('**/*.html');
    return gulp.src(Config.paths.build.root + '/**/*')
        .pipe(htmlFilter)
        .pipe(replace('http://localhost/', 'http://fj-dev.nativesdev.com.au'))
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('replaceCSS_dev', () => {
    const cssFilter = filter('**/*.css');
    return gulp.src(Config.paths.build.root + '/**/*')
        .pipe(cssFilter)
        .pipe(replace('http://localhost/', 'http://fj-dev.nativesdev.com.au'))
        .pipe(gulp.dest(Config.paths.build.root));
});

///

gulp.task('exportSQL', () => {
    return runCmd('cd wp-db & mysqldump -u root wordpress > fashionJournalDb.sql').exec();
});

gulp.task('importSQL', () => {
    return runCmd('cd wp-db & mysql -u root wordpress < fashionJournalDb.sql').exec();
});

gulp.task('zip', () => {
    gulp.src('./*')
        .pipe(zip('my-app.zip'))
        .pipe(gulp.dest('./build'));
});

///

gulp.task('replacePHP_prod', () => {
    const phpFilter = filter('**/*.php');
    return gulp.src(Config.paths.build.root + '/**/*')
        .pipe(phpFilter)
        // .pipe(replace('http://fj-dev.nativesdev.com.au', 'http://localhost'))
        .pipe(replace('http://localhost', 'http://fj.nativesdev.com.au'))
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('replaceSQL_prod', () => {
    const sqlFilter = filter('**/*.sql');
    return gulp.src('./wp-db/fashionJournalDb.sql')
        // .pipe(replace('http://fj-dev.nativesdev.com.au', 'http://localhost'))
        .pipe(replace('http://localhost', 'http://fj.nativesdev.com.au'))
        .pipe(gulp.dest('./wp-db'));
});

gulp.task('replaceHTML_prod', () => {
    const htmlFilter = filter('**/*.html');
    return gulp.src(Config.paths.build.root + '/**/*')
        .pipe(htmlFilter)
        .pipe(replace('http://localhost/', 'http://fj.nativesdev.com.au'))
        .pipe(gulp.dest(Config.paths.build.root));
});

gulp.task('replaceCSS_prod', () => {
    const cssFilter = filter('**/*.css');
    return gulp.src(Config.paths.build.root + '/**/*')
        .pipe(cssFilter)
        .pipe(replace('http://localhost/', 'http://fj.nativesdev.com.au'))
        .pipe(gulp.dest(Config.paths.build.root));
});

///

// MOTHER LEVEL

gulp.task('clear', () => {
    del([Config.paths.build.root + '/**/*'], { force: true }).then(paths => {
        console.log('Deleted files and folders:\n', paths.join('\n'));
    });;
});

gulp.task('rebuild-appspec', () => {
    return gulp.src('appspec.yml')
        .pipe(replace('hooks', '#hooks'))
        .pipe(replace('AfterInstall', '#AfterInstall'))
        .pipe(replace('timeout: 3600', '#timeout: 3600'))
        .pipe(replace('- location:', '#- location:'))
        .pipe(gulp.dest('./'));
});

gulp.task('compile', ['uglifyJS']);

gulp.task('export', ['exportSQL']);

gulp.task('import', ['importSQL']);

gulp.task('init', ['importSQL']);

gulp.task('replace-dev', ['replacePHP_dev', 'replaceSQL_dev', 'replaceHTML_dev', 'replaceCSS_dev']);

gulp.task('replace-prod', ['replacePHP_prod', 'replaceSQL_prod', 'replaceHTML_prod', 'replaceCSS_prod']);

gulp.task('release', ['zip']);


// AWS SERVER TASKS


gulp.task('deploy', ['replaceLocalhost']);




// // Images
// gulp.task('images:clean', function(next) {
//     del(Config.paths.build.images + '/**', next);
// });
// gulp.task('images', ['images:clean'], function() {
//     return gulp.src(Config.paths.src.images + '/**/*')
//         .pipe(gulpifelse(
//             Config.cache,
//             function() {
//                 return cache(imagemin(Config.imagemin)) // if
//             },
//             function() {
//                 return imagemin(Config.imagemin) // else
//             }
//         ))
//         .pipe(gulp.dest(Config.paths.build.images + '/'));
// });

// // Templates
// gulp.task('templates', function() {
//     // return gulp.src(Config.paths.src.tmpl + '/**/*')
//     //   .pipe(handlebars())
//     //   .pipe(defineModule('plain'))
//     //   .pipe(declare({
//     //     namespace: 'tmpl'
//     //   }))
//     //   .pipe(concat('templates.js'))
//     //   .pipe(gulp.dest(Config.paths.src.js + '/'));
// });

// // HTML, JavaScript, CSS
// gulp.task('html:clean', function(next) {
//     del([Config.paths.build.root + '/**/*.html', Config.paths.build.root + '/**/*.css', Config.paths.build.root + '/**/*.js'], next);
// });
gulp.task('html', ['html:clean'], function() {
    var jsFilter = filter('**/*.js'),
        cssFilter = filter('**/*.css'),
        htmlFilter = filter('**/*.html');

    var assets = useref.assets();

    return gulp.src([Config.paths.src.root + '/**/*.html', '!' + Config.paths.src.lib + '/**/*'])
        .pipe(assets)
        .pipe(jsFilter)
        .pipe(uglify())
        .pipe(jsFilter.restore())
        .pipe(cssFilter)
        .pipe(minifyCss())
        .pipe(cssFilter.restore())
        .pipe(assets.restore())
        .pipe(useref())
        .pipe(htmlFilter)
        .pipe(minifyHtml())
        .pipe(htmlFilter.restore())
        .pipe(gulp.dest(Config.paths.build.root));
});

// Server
// gulp.task('server', function() {
//     var server = express()
//         .use(express.static(path.resolve(Config.paths.src.root)))
//         .listen(Config.port);
//     gutil.log('Server listening on port ' + Config.port);
// });

// LiveReload
gulp.task('livereload', function() {
    livereload.listen(Config.livereload_port, function(err) {
        if (err) gutil.log('Livereload error:', err);
    })
});

// Watches
gulp.task('watch', function() {
    watch(Config.paths.src.root + '/**/*.scss', function() {
        gulp.start('liveReloadSCSS');
    });
    watch(Config.paths.src.root + '/**/*.js', function() {
        gulp.start('liveReloadJS');
    });
    watch(Config.paths.src.root + '/**/*.php', function() {
        gulp.start('migratePHP');
    });
    gulp.watch([
        Config.paths.build.root + '/**/*.scss',
        Config.paths.build.root + '/**/*.php',
    ], function(evt) {
        livereload.changed(evt.path);
    });
});

// gulp.task('clear', function(done) {
//     return cache.clearAll(done);
// });


// gulp.task('clean', ['fonts:clean', 'images:clean', 'html:clean', 'extra:clean']);

// gulp.task('default', ['server', 'livereload', 'templates', 'styles', 'watch'], function() {
//     if (argv.o) opn('http://localhost:' + Config.port);
// });
