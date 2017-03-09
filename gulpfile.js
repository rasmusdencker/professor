var gulp  = require('gulp'),
    spawn  = require('child_process').spawn,
    watch = require('gulp-watch');

var queue = [];

gulp.task('tdd', function () {
    runTests().then(work);

    return watch([
        'tests/**/*.php',
        'src/**/*.php'
    ], queueTest);
});

function queueTest(){
    queue.push(runTests);
}

function runTests(){
    return new Promise(function(resolve, reject){
        clearTerminal();
        spawn("phpunit", [], {stdio: "inherit"})
            .on('close', resolve);
    });
}

function work(){
    var job = queue.shift();

    if(!job) {
        setTimeout(work, 100);
        return;
    }

    job().then(work);
}

function clearTerminal(){
    console.log('\033c');
}