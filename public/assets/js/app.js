var csrf_token = $('meta[name="csrf_token"]').attr('content');
$.ajaxPrefilter(function (options) {
    if (options.type.toLowerCase() === "post") {
        options.data += options.data ? "&" : "";
        options.data += "_token=" + csrf_token;
    }
});

var interval;

new Vue({
    el: 'body',
    data: {
        os_total: importer.os_total,
        imported_total: importer.imported_total,
        to_import: importer.products.length,
        working: false,
        product_index: 0,
        products: importer.products,
        imported: 0,
        finished: false,
        time: '00:00:00'
    },
    computed: {
        imported_per: function () {
            return Math.round((this.imported_total * 100) / this.os_total)
        },
        session_per: function () {
            return Math.round((this.imported * 100) / this.to_import)
        }

    },
    methods: {
        work: function () {
            this.working = true;
            var total = this.product_index ? 1 : (this.to_import < 5 ? this.to_import : 5);
            var vm = this;
            for (var i = 0; i < total; i++) {
                $.post('/products', {product_id: this.products[this.product_index]}, function (data) {
                    vm.imported++;
                    if (vm.product_index == vm.to_import) {
                        vm.working = false;
                        vm.finished = true
                    } else if ( vm.working && !vm.finished) {
                        vm.work();
                    }
                });
                this.product_index++;
            }
        },
        stop: function () {
            this.working = false;
        }
    }
});
