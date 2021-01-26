<?php /*a:4:{s:63:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\small\article\add.html";i:1611644628;s:60:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\layout\default.html";i:1607568254;s:57:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\common\meta.html";i:1607568254;s:59:"D:\phpstudy_pro\WWW\zjxsh\app\admin\view\common\script.html";i:1607568254;}*/ ?>
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
        <label  class="control-label col-xs-12 col-sm-2">分类名称:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_select('row[cat_id]', $artcate, null, ['class'=>'form-control', 'required'=>'']); ?>
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">文章标题:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="title" name="row[title]" value="" data-rule="required;title" />
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">文章简介:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="intro" name="row[intro]" value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">文章封面图:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-arturl" data-rule="" class="form-control" size="50" name="row[arturl]" type="text" value="">
                <div class="input-group-addon no-border no-padding">
                    <span>
                        <button type="button" id="plupload-arturl" class="btn btn-danger plupload" data-input-id="c-arturl"
                                data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-arturl">
                        <i class="fa fa-upload"></i> 上传
                        </button>
                    </span>
                </div>
                <span class="msg-box n-right" ></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-arturl"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">文章作者:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="author" name="row[author]" value=""  />
        </div>
    </div>
    <div class="form-group">
        <label  class="control-label col-xs-12 col-sm-2">文章来源:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="copyform" name="row[copyform]" value="" />
        </div>
    </div>
    <div class="form-group">
        <label for="c-content" class="control-label col-xs-12 col-sm-2">内容:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-content" class="form-control summernote" rows="5" name="row[content]" cols="50"><p>我是测试内容</p></textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">排序:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="ordersort" name="row[ordersort]" value=""  />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">是否推荐:</label>
        <div class="col-xs-12 col-sm-8">
            <label><input id="row[iselite]-normal" name="row[iselite]" type="radio" value="1"> 推荐</label>
            <label><input id="row[iselite]-hidden" name="row[iselite]" checked="checked" type="radio" value="0"> 不推荐</label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">状态:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>__('Normal'), 'hidden'=>__('Hidden')]); ?>
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