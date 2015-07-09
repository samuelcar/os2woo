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
        to_import: _.size(importer.products),
        working: false,
        product_index: 0,
        products: importer.products,
        imported: 0,
        finished: false,
        time: '00:00:00',
        error_list: []
    },
    computed: {
        imported_per: function () {
            return ((this.imported_total * 100) / this.os_total).toFixed(2)
        }
    },
    methods: {
        work: function () {
            this.working = true;
            var total = this.product_index ? 1 : (this.to_import < 5 ? this.to_import : 5);
            var vm = this;
            for (var i = 0; i < total; i++) {
                $.post('/products', {product_id: this.products.shift()}, function (data) {
                    vm.imported++;
                    if (data.success == "1") {
                        vm.imported_total++;
                        vm.to_import--;
                        $.notify({
                            message: data.message
                        }, {
                            placement: {
                                from: 'bottom'
                            }
                        });
                    }else if(data.success == "0"){
                        $.notify({
                            message: data.message
                        }, {
                            type: 'danger',
                            delay: 0,
                            placement: {
                                from: 'bottom'
                            }
                        });
                    }

                    if (vm.to_import === 0 || vm.product_index == vm.imported) {
                        vm.working = false;
                        vm.finished = true
                    } else if (vm.working && !vm.finished) {
                        vm.work();
                    }
                }, "json");
                this.product_index++;
            }
        },
        stop: function () {
            this.working = false;
        }
    }
});
