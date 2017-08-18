"use strict";

const gulp = require('gulp'),
	fs = require('fs'),
	path = require('path'),
	del = require('del'),
	runSequence = require('run-sequence'),
	replace = require('gulp-replace'),
	watchPath = require('gulp-watch-path'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	minifycss = require('gulp-clean-css'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	combiner = require('stream-combiner2'),
	sourcemaps = require('gulp-sourcemaps'),
	production = gutil.env['production'], //是否为线上环境
	onlyWatch = gutil.env['onlyWatch'], //只watch，不对所有文件进行操作
	renewVersion = gutil.env['renewVersion'], //是否更新版本号
	noDel = gutil.env['noDel'], //不清空所有文件
	watch = gutil.env['watch'];//是否开启文件监视
    // livereload = require('gulp-livereload');  //实时重载


const PathSrc = './resources/assets/',
	PathOut = './public/',
	DefPath = {  //文件的输入和输出路径
		src: PathSrc,
		out: PathOut,
		sassSrc: path.join(PathSrc, 'sass/**/*.scss'),
		cssSrc: path.join(PathSrc, 'css/**/*.css'),
		fontSrc: path.join(PathSrc, 'css/fonts/**/*'),
		sassPath: PathSrc + 'sass',
		cssPath: PathSrc + 'css',
		jsPath: PathSrc + 'js',
		// jsMapPath: '../map/js/',
		// cssMapPath: '../map/css/',
		jsSrc: path.join(PathSrc, 'js/**/*.js'),
		cssOut: path.join(PathOut, 'css'),
		jsOut: path.join(PathOut, 'js'),
		fontOut: path.join(PathOut, 'css/fonts'),
		envPath: ['./.env', './.env.dev', './.env.example', './.env.official', './.env.official.ro']
	},
	DefConfige = {  //配置
		sass: {
			outputStyle: 'compact'
		},
		autoprefixer: {
			browsers: ['Chrome >= 35', 'ie >= 9', 'last 5 Safari versions', 'ff >= 40'],
	        cascade: false, //是否美化属性值
	        remove:true //是否去掉不必要的前缀 默认：true
		},
		uglify: {
	   		mangle: true, //默认不混淆变量名
	   		compress: false
	    },
	    minifycss: {
            advanced: true,//类型：Boolean 默认：true [是否开启高级优化（合并选择器等）]
            compatibility: '*',//默认：''or'*' [启用兼容模式；'ie7'：IE7兼容模式，'ie8'：IE8兼容模式，'*'：IE9+兼容模式]
            keepBreaks: false,//默认：false [是否保留换行]
            keepSpecialComments: '*'//保留所有特殊前缀
	    }
	},
	batObj = {
		uglifyJs: (jsSrc, jsOut) => [
				gulp.src(jsSrc),
	        sourcemaps.init(),
	        uglify(DefConfige.uglify),
	        sourcemaps.write({includeContent: true}),
	        gulp.dest(jsOut)
		],
		minifyCss: (cssSrc, cssOut) => [
	        gulp.src(cssSrc),
	    	sourcemaps.init(),
	        autoprefixer(DefConfige.autoprefixer),
	        minifycss(DefConfige.minifycss),
	        sourcemaps.write({includeContent: true}),
	        gulp.dest(cssOut)
	    ],
		sassCss: (sassSrc, sassOut) => [
			gulp.src(sassSrc),
	    	sourcemaps.init(),
	        sass(DefConfige.sass),
	        autoprefixer(DefConfige.autoprefixer),
	        minifycss(DefConfige.minifycss),
	        sourcemaps.write({includeContent: true}),
	        gulp.dest(sassOut)
	    ]
	};


function handleError (err) {  //gulp出现错误时的回调函数
    var colors = gutil.colors;
    gutil.log(colors.red('Error!'));
    console.log('\n');
    gutil.log( colors.yellow('message: ') + colors.blue( JSON.stringify(err) ) );
    console.log('\n');
    // gutil.log('fileName: ' + colors.blue(err.fileName));
    // gutil.log('cause: ' + colors.red(err.cause.message));
    // gutil.log('message: ' + err.message);
    // gutil.log('plugin: ' + colors.yellow(err.plugin));
}

function _batFun(batArr) {
	if(production) { //如果是线上环境，则不产生map映射
		batArr.splice(1,1);
		batArr.splice(-2,1);
	}
	var combined = combiner.obj(batArr);
    combined.on('error', handleError);  //显示压缩时的错误
    return combined;
}


function _uglifyJs(jsSrc, jsOut) {  //处理js
	var batArr = batObj.uglifyJs(jsSrc, jsOut);
	return _batFun(batArr);
}

function _sassCss(sassSrc, sassOut) {  //处理sass
	var batArr = batObj.sassCss(sassSrc, sassOut);
	return _batFun(batArr);
}

function _minifyCss(cssSrc, cssOut) {  //处理css
	var batArr = batObj.minifyCss(cssSrc, cssOut);
	return _batFun(batArr);
}

function watchlog(event, paths) { //watch文件时，打印log
    //console.dir(paths);
	gutil.log(gutil.colors.green(event.type) + ' ' + paths.srcPath);
    gutil.log(gutil.colors.green('Dist ') + paths.distPath);
}

function newTimestamp() {
	var oDate = new Date(),
		version = ""
			+ oDate.getFullYear()
			+ (oDate.getMonth() + 1)
			+ oDate.getDate()
			+ oDate.getHours()
			+ oDate.getMinutes()
			+ oDate.getSeconds();
	return version;
}

gulp.task('uglifyjs', function() {
    return _uglifyJs(DefPath.jsSrc, DefPath.jsOut);
});
gulp.task('watchjs', function() {
	return gulp.watch(DefPath.jsSrc, function (event) {
	    var paths = watchPath(event, DefPath.src, DefPath.out);
	    watchlog(event, paths);
        //_uglifyJs(paths.srcPath, paths.distDir);
        return _uglifyJs(paths.srcPath, paths.distDir);
	});
});

gulp.task('sasscss', function () {
    return _sassCss(DefPath.sassSrc, DefPath.cssOut);
});

gulp.task('sasscssnew', function () {
    return _sassCss(DefPath.cssSrc, DefPath.cssOut);
});

gulp.task('watchsass',function () {
    return gulp.watch(DefPath.sassSrc, function (event) {
        var paths = watchPath(event, DefPath.src + 'sass/', DefPath.out + 'css/');
	    watchlog(event, paths);

        return _sassCss(paths.srcPath, paths.distDir);
    });
});

gulp.task('watchsassnew',function () {
    return gulp.watch(DefPath.cssSrc, function (event) {
        var paths = watchPath(event, DefPath.src + 'css/', DefPath.out + 'css/');
        watchlog(event, paths);

        return _sassCss(paths.srcPath, paths.distDir);
    });
});

gulp.task('minifycss', function () {
    return _minifyCss(DefPath.cssSrc, DefPath.cssOut);
});
gulp.task('watchcss', function () {
    return gulp.watch(DefPath.cssSrc, function (event) {
        var paths = watchPath(event, DefPath.src, DefPath.out);
	    watchlog(event, paths);

        return _minifyCss(paths.srcPath, paths.distDir);
    })
});

gulp.task('delFile', function() {
	gutil.log( gutil.colors.red('开始清除文件，重新生成没有映射的js与css') );
	return del(['./public/css/**/*','./public/js/**/*']);
});

gulp.task('copyFont', function() { //字体文件拷贝
	gulp.src(DefPath.fontSrc)
		.pipe(gulp.dest(DefPath.fontOut));
});

gulp.task('renewVersion', function() {
	var time = newTimestamp() + '';
	gutil.log( gutil.colors.cyan('全局时间戳：') + time );
	gulp.src(DefPath.envPath)
		.pipe(replace( /(timestamp\s*=v\s*)(\w+)/, '$1'+time ))
		.pipe(gulp.dest('./'));

});

/**
 * task - 'default'
 * executes 'live-monitor'
 */
// gulp.task('default', ['live-monitor']);

/**
 * task - 'laravel-views'
 * monitor laravel views
 */
// gulp.task('laravel-views', function() {
//     gulp.src('resources/views/admin/guide/index.blade.php')
//         .pipe(livereload());
// });

/**
 * task - 'live-monitor'
 * monitors everything
 */
// gulp.task('live-monitor', function() {
//     livereload.listen();
//     gulp.watch('resources/views/**/*.blade.php', ['laravel-views']);
// });


let defTask = [/* build */
    'uglifyjs', 'minifycss','copyFont','sasscss',
    /* watch */
    //,'watchjs', 'watchcss',
    //'watchsass',
];

let watchArr = ['watchjs', 'watchcss','watchsass'];
if( renewVersion ){
	defTask.push('renewVersion');//更新env下的时间戳
}
if( watch ) { //如果开启监视，在任务列表添加监视任务
	gutil.log( gutil.colors.cyan('开启监听，文件保存时自动压缩。') );
	gutil.log( gutil.colors.yellow('如果自动保存后没有进行压缩，请手动 Ctrl+S 进行文件保存。') );

	defTask = defTask.concat(watchArr);
}
if( onlyWatch ) {
	gutil.log( gutil.colors.cyan('开启监听，文件保存时自动压缩。') );
	gutil.log( gutil.colors.yellow('如果自动保存后没有进行压缩，请手动 Ctrl+S 进行文件保存。') );

	defTask = watchArr;
}

if( production ){ //如果是线上环境先移除掉已生成的js和css，不生成map映射
	gutil.log( gutil.colors.cyan('准备线上环境') );
	if(!noDel) {
		gulp.task('default', function(callback) {
			runSequence('delFile', //清除原先的压缩文件及map映射
					 	 defTask);
		});
	} else {
		gulp.task('default', defTask);
	}
} else {
	gulp.task('default', defTask);
}

gulp.task('test', function() {
    var colors = gutil.colors;
	gutil.log( '环境：'+colors.green(JSON.stringify(gutil.env)) );
	gutil.log( '默认任务列表：'+colors.blue( JSON.stringify(defTask) ) );
});