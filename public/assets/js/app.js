/**********************CSRF**********************/
var csrf_token = $('meta[name="csrf_token"]').attr('content');
$.ajaxPrefilter(function (options) {
    if (options.type.toLowerCase() === "post") {
        options.data += options.data ? "&" : "";
        options.data += "_token=" + csrf_token;
    }
});
/********************** END -- CSRF **********************/

/**********************STOPWATCH**********************/
var clsStopwatch = function () {
    var startAt = 0;
    var lapTime = 0;
    var now = function () {
        return (new Date()).getTime();
    };
    this.start = function () {
        startAt = startAt ? startAt : now();
    };
    this.stop = function () {
        lapTime = startAt ? lapTime + now() - startAt : lapTime;
        startAt = 0;
    };
    this.reset = function () {
        lapTime = startAt = 0;
    };
    this.time = function () {
        return lapTime + (startAt ? now() - startAt : 0);
    };
};

function pad(num, size) {
    var s = "0000" + num;
    return s.substr(s.length - size);
}

function formatTime(time) {
    var h = m = s = ms = 0;
    var newTime = '';
    h = Math.floor(time / (60 * 60 * 1000));
    time = time % (60 * 60 * 1000);
    m = Math.floor(time / (60 * 1000));
    time = time % (60 * 1000);
    s = Math.floor(time / 1000);
    ms = time % 1000;
    newTime = pad(h, 2) + ':' + pad(m, 2) + ':' + pad(s, 2);
    /* + ':' + pad(ms, 2);*/
    return newTime;
}

/**********************END STOPWATCH**********************/

var x = new clsStopwatch();
var clocktimer;

new Vue({
    el: 'body',
    data: {
        os_total: importer.os_total,
        imported_total: importer.imported_total,
        to_import: _.size(importer.resource),
        working: false,
        resource_index: 0,
        resource: importer.resource,
        imported: 0,
        finished: false,
        time: '00:00:00',
        error_list: [],
        success_list: []
    },
    computed: {
        imported_per: function () {
            return ((this.imported_total * 100) / this.os_total).toFixed(2)
        },
        session_per: function () {
            return ((this.imported * 100) / this.to_import).toFixed(2)
        }
    },
    methods: {
        work: function () {
            if (!this.working) {
                if (this.finished) {
                    this.imported = 0;
                    this.finished = false;
                }
                this.working = true;
            }
            var total = this.working ? 1 : (this.to_import < 5 ? this.to_import : 5);
            var vm = this;

            clocktimer = setInterval(function () {
                vm.time = formatTime(x.time());
            }, 1);
            x.start();

            for (var i = 0; i < total; i++) {
                vm.to_import--;
                $.post(importer.url, {resource_id: this.resource.shift()}, function (data) {
                    vm.imported++;
                    if (data.success == "1") {
                        vm.imported_total++;
                        vm.success_list.unshift(data.message);
                    } else if (data.success == "0") {
                        vm.error_list.unshift(data.message);
                    }
                    if (vm.to_import === 0 || vm.resource.length == 0) {
                        vm.stop();
                        vm.finished = true
                    } else if (vm.working && !vm.finished) {
                        vm.work();
                    }
                }, "json");
            }
        },
        stop: function () {
            this.working = false;
            x.stop();
            clearInterval(clocktimer);
        }
    }
});

