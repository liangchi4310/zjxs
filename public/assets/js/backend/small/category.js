define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'small.category/index',
                    add_url: 'small.category/add',
                    edit_url: 'small.category/edit',
                    del_url: '',
                    multi_url: '',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'sort',
                escape: false,
                pagination: false,
                search: false,
                commonSearch: false,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID'},
                        {field: 'cname', title: '分类名称'},
                        // {field: 'cicon', title: 'cicon' ,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'type', title: '栏目类型',formatter:function (value) {
                                if(value == 1){
                                    return '<span class="text-success"> 广告</span>';
                                }else if(value == 2){
                                    return '<span class="text-success"> 文章</span>';
                                }else if(value == 3){
                                    return '<span class="text-success"> 产品</span>';
                                }
                            },operate:false
                        },
                        {field: 'status', title: '状态',formatter:function (value) {
                                if(value == 1){
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 正常</span>';
                                }else{
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 禁止</span>';
                                }
                            },operate:false
                        },
                        {field: 'createtime', title: '添加时间',formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), events: Table.api.events.operate, formatter: function (value, row, index) {
                                return Table.api.formatter.operate.call(this, value, row, index, table);
                            }}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});