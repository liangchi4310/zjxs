<?php /*a:4:{s:61:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\small\goods\add.html";i:1607679073;s:60:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\layout\default.html";i:1607568254;s:57:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\common\meta.html";i:1607568254;s:59:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\common\script.html";i:1607568254;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo htmlentities($config['language']); ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico"/>
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo app('request')->env('app_debug')?'':'.min'; ?>.css?v=<?php echo htmlentities(config('site.version')); ?>"
      rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
<script src="/assets/js/html5shiv.js"></script>
<script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config: <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>

                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">

                                </ol>
                            </div>
                            <!-- END RIBBON -->

                            <div class="content">
                                <form id="edit-form" class="form-horizontal form-ajax" role="form" data-toggle="validator" method="POST" action="">
    <div class="form-group">
        <label for="name" class="control-label col-xs-12 col-sm-2">商品名称:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="name" name="row[name]" value="" data-rule="required;title" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">商品分类:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="form-inline" data-toggle="cxselect" data-selects="pcid,category_id">
                <select class="pcid form-control" id="first" name="row[pcid]" data-url="ajax/goodcategory?pid=0" data-query-name="id" data-rule="required"></select>
                <select class="category_id form-control" name="row[category_id]" data-url="ajax/goodcategory" data-query-name="pid" data-rule="required"></select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">所属店铺:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_select('row[shop_id]', $shopslist, null, ['class'=>'form-control', 'required'=>'']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="c-pimages" class="control-label col-xs-12 col-sm-2">商品主图:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-pimages" data-rule="" class="form-control" size="50" name="details[images_url]" type="text" value="">
                <div class="input-group-addon no-border no-padding">
                    <span>
                        <button type="button" id="plupload-pimages" class="btn btn-danger plupload" data-input-id="c-pimages"
                                data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-pimages">
                            <i class="fa fa-upload"></i> 上传
                        </button>
                    </span>
                </div>
            </div>
            <span class="msg-box n-right" for="c-pimages"></span>
            <ul class="row list-inline plupload-preview" id="p-pimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">规格名称:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="spec[spec_name]" value="" placeholder="例：口味等"/>
        </div>
    </div>

    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">规格属性:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="spec[spec_value]" value="" placeholder="属性多时，用 - 分割。例：麻辣-微辣-特辣" />
        </div>
    </div>

    <div class="form-group" style="margin-bottom: unset">
        <label class="control-label col-xs-12 col-sm-2">SKU属性:</label>
        <div id="prev" style="padding-left: 18%">
            <div id="skuattr" class="form-group">
                <div class="col-xs-12 col-sm-2">
                    <input type="text" class="form-control" size="40" name="rowsku[sku_title][]" value="" placeholder="属性名称" data-rule="required"/>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <input type="text" class="form-control" name="rowsku[sku_price][]" value="" placeholder="价格" data-rule="required"/>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <input type="number" class="form-control" name="rowsku[stock][]" value="" placeholder="库存" data-rule="required"/>
                </div>
                <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
            </div>
        </div>
    </div>

    <div class="form-group" style="text-align: center">
        <a href="javascript:;" id="append" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a>
    </div>

    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">市场价格:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="row[price]" value=""  />
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">优惠价格:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="row[discount_price]" value=""  />
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">商品销量:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="number" class="form-control" name="row[sales]" value="" />
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">商品出产地:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="row[parea]" value="" />
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">商品详情</label>
    </div>
    <div class="form-group">
        <label for="c-picdesc" class="control-label col-xs-12 col-sm-2">商品详情图:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-picdesc" data-rule="" class="form-control" size="50" name="details[picdesc]" type="text" value="">
                <div class="input-group-addon no-border no-padding">
                    <span>
                        <button type="button" id="plupload-picdesc" class="btn btn-danger plupload" data-input-id="c-picdesc"
                                data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-picdesc">
                            <i class="fa fa-upload"></i> 上传
                        </button>
                    </span>
                </div>
            </div>
            <span class="msg-box n-right" for="c-picdesc"></span>
            <ul class="row list-inline plupload-preview" id="p-picdesc"></ul>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">商品描述:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea name="details[introduce]" id="c-editor" cols="60" rows="5" class="form-control editor"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">是否热卖:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_hot_sale]', ['1'=>'是', '2'=>'不是']); ?>
        </div>
    </div>

    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">店长推荐:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_recommend]', ['1'=>'是', '2'=>'不是']); ?>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">新品:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_new]', ['1'=>'是', '2'=>'不是']); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">抢购商品:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_rush]', ['0'=>'默认', '1'=>'限时抢购', '2'=>'团购', '3'=>'优惠促销','4'=>'预售','5'=>'虚拟','8'=>'砍价']); ?>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">0元购:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[buy0]', ['1'=>'是', '2'=>'不是']); ?>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">状态:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['1'=>__('Normal'), '2'=>__('Hidden')]); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开始时间</label>
        <div class="col-xs-4 col-sm-4">
            <input id="c-starttime" class="form-control datetimepicker form-control"
                   data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[starttime]" type="text"
                   value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">结束时间</label>
        <div class="col-xs-4 col-sm-4">
            <input id="endtime" class="form-control datetimepicker form-control"
                   data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[endtime]" type="text"
                   value="vgcxz">
        </div>
    </div>
    <div class="form-group hidden layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled">确定</button>
            <button type="reset" class="btn btn-default btn-embossed">重置</button>
        </div>
    </div>
</form>
<style>
    .radio label {
        margin-right: 50px;
    }
    .checkbox label {
        margin-right: unset;
    }
    .checkbox {
        width: 45px;
        margin-right: 0;
    }
    dd {
        padding: 0 15px;
    }
</style>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo app('request')->env('app_debug')?'':'.min'; ?>.js"
        data-main="/assets/js/require-backend<?php echo app('request')->env('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>

    </body>
</html>