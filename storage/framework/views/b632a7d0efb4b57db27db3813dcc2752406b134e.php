<?php $__env->startSection('title'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .index_contrainer{
            position:absolute;
            left:0;
            right:0;
            bottom:0;
            top:0;
            background-color:#F5F5F5;
        }
        .tips_box{
            text-align: center;
            width: 130px;
            height: 130px;
            background: #eee;
            border-radius: 5em;
            margin: 0 auto;
            margin-top: 200px;
        }
        .tips_icon{
            line-height: 130px;
            font-size: 44px;
            color: #fff;
        }
        .tips_text{
            color:#9B9B9B;
            text-align: center;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="index_contrainer">
        <div class="tips_box">
            <div class="tips_icon">
                <img src="/images/m/common/files.png">
            </div>
        </div>
        <p class="tips_text">暂无相关数据</p>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('webi.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>