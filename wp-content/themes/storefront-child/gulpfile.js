import gulp from 'gulp';
import dartSass from 'sass';
import gulpSass from 'gulp-sass';
import cleanCSS from 'gulp-clean-css';
import uglify from 'gulp-uglify';
import rename from 'gulp-rename';
import autoprefixer from 'gulp-autoprefixer';
import sourcemaps from 'gulp-sourcemaps';

// Настройка gulp-sass с использованием Dart Sass
const sass = gulpSass(dartSass);

// Пути к файлам
const paths = {
    styles: {
        src: 'assets/scss/**/*.scss',
        dest: 'assets/css/'
    },
    scripts: {
        src: 'assets/js/**/*.js',
        dest: 'assets/js/min/'
    }
};

// Компиляция SCSS → CSS + минификация
export function styles() {
    return gulp.src(paths.styles.src)
        .pipe(sourcemaps.init()) // Карты исходников (для отладки)
        .pipe(sass().on('error', sass.logError)) // Компиляция SCSS → CSS
        .pipe(autoprefixer({ overrideBrowserslist: ['last 2 versions'], cascade: false })) // Автопрефиксы
        .pipe(cleanCSS({ compatibility: 'ie8' })) // Минификация CSS
        .pipe(rename({ suffix: '.min' })) // Переименовываем в .min.css
        .pipe(sourcemaps.write('.')) // Записываем карты исходников
        .pipe(gulp.dest(paths.styles.dest));
}

// Минификация JS
export function scripts() {
    return gulp.src(paths.scripts.src)
        .pipe(uglify()) // Минифицируем JS
        .pipe(rename({ suffix: '.min' })) // Переименовываем в .min.js
        .pipe(gulp.dest(paths.scripts.dest));
}

// Следить за изменениями в файлах и автоматически пересобирать
export function watchFiles() {
    gulp.watch(paths.styles.src, styles);
    gulp.watch(paths.scripts.src, scripts);
}

// Основная задача: сборка всех файлов
export const build = gulp.parallel(styles, scripts);

// Экспортируем задачи для CLI
export default gulp.series(build, watchFiles);