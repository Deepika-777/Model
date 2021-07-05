

<?php $__env->startSection('title'); ?>

HETA DATAIN|DASHBOARD

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>
/*
.success-tooltip + .popover {
    background-color: rgb(107, 241, 107);
}

.primary-tooltip + .popover {
    background-color: rgba(124, 206, 236, 0.53);
}

.warning-tooltip + .popover {
    background-color: rgba(201, 238, 98, 0.53);
}

.danger-tooltip + .tooltip > .tooltip-inner {
    background-color: rgba(248, 103, 98, 0.53);
}*/
 

.btn-primary {
  padding: 8px 12px;
  font-size: 12px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: black;
  border: none;
  border-radius: 15px;
  box-shadow: 0 5px #999;
}

.btn-primary:hover {background-color: #F0EC11 }
.btn-primary:active {
  background-color: black;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

.font-weight-bold mb-0{
  font-size : 30px;
}

.card-body{
  font-size: 20px !important;
}
.mypill{
	margin : 0.1rem !important;
	border-radius: 0.2rem !important;
  width : 75px;
  }

</style>

<!-- Card 1 -->
<div class="row"> 

  <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-12">

      <div class="card" >

      <div class="card-header white">
        <p class="text-uppercase small mb-2">Project Name </p>
        <h5 class="font-weight-bold mb-0">
        <?php echo e($value['project_name']); ?>

        </h5>
      </div>

      <div class="card-body">
      <?php $__currentLoopData = $value['detail']['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2=>$value2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <span class="badge badge-pill mypill <?php echo e($value2->color); ?> <?php echo e($value2->status); ?>" title="" 
        data-toggle="popover" data-trigger="click" 
        data-content="<?php echo e($value2->text); ?> <?php echo e($value2->dt_time); ?> " 
        data-original-title="<?php echo e($value2->device_name); ?> [Client ID : <?php echo e($value2->client_id); ?>]">
          <?php echo e($value2->device_id); ?>

        </span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div class="card-footer white">
        <Next type="Next" class="btn-primary" >
   
            <a href="<?php echo e(url('/deviceinfo/'.$value['id'])); ?>" class="text-sm text-gray-700 underline">Details</a>
        </Next>
       

      </div>

      </div>
    </div>

  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
  $('[data-toggle="popover"]').popover();

  $(document).ready(function(){

$('[data-toggle="popover"]').popover();   
$('[data-toggle="click"]').popover();  
$('body').on('click', function (e) {
  //did not click a popover toggle or popover
console.log(e);
  if ($(e.target).data('toggle') !== 'popover'
      && $(e.target).parents('.popover.in').length === 0) { 
      $('[data-toggle="popover"]').popover('hide');
  }
});

});
</script>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('Style.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Model\resources\views/Admin/status.blade.php ENDPATH**/ ?>