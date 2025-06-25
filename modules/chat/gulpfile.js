const gulp = require('gulp');
// const { series } = require('gulp');

const rename = require('gulp-rename');
const sass = require('gulp-sass')(require('sass'));
const bulk = require('gulp-sass-bulk-importer');
const prefixer = require('gulp-autoprefixer');
const clean = require('gulp-clean-css');
const concat = require('gulp-concat');
const map = require('gulp-sourcemaps');

// const uglify = require('gulp-uglify');
const uglify = require('gulp-terser');
const babel = require('gulp-babel');
const ngAnnotate = require('gulp-ng-annotate');

const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const webpackConfig = require('./webpack.config.js');

const onError = function (err) {
    console.log(err);
};

function webpackTask() {
    return gulp.src('assets/src/import.js')
        .pipe(webpackStream(webpackConfig).on('error', onError), webpack)
        .pipe(gulp.dest('assets/dist/js'));
}

function styleTask() {
    return gulp.src('assets/src/scss/app.scss')
        .pipe(sass({
            outputStyle: 'compressed'
        }).on('error', onError))
        .pipe(prefixer({
            overrideBrowserslist: ['last 8 versions'],
            browsers: [
                'Android >= 4',
                'Chrome >= 20',
                'Firefox >= 24',
                'Explorer >= 11',
                'iOS >= 6',
                'Opera >= 12',
                'Safari >= 6',
            ],
        }))
        .pipe(clean({
            level: 2
        }))
        .pipe(concat('chat.min.css'))
        .pipe(gulp.dest('assets/dist/css'));
}

function moduleJSTask() {
    return gulp.src('assets/src/js/chat.js')
        .pipe(ngAnnotate())
        .pipe(babel({
            presets: ['@babel/env']
        }).on('error', onError))
        .pipe(uglify().on('error', onError))
        .pipe(rename('chat.min.js'))
        .pipe(gulp.dest('assets/src/js'));
}

function pluginsJSTask() {
    return gulp.src(plugins)
        .pipe(concat('chat.min.js'))
        .pipe(gulp.dest('assets/dist/js'));
}

exports.styleTask = styleTask
exports.moduleJSTask = moduleJSTask
exports.pluginsJSTask = pluginsJSTask
exports.webpackTask = webpackTask

exports.default = gulp.series(styleTask, moduleJSTask, webpackTask);
