

<?php $__env->startSection('title'); ?>

HETA DATAIN|DASHBOARD

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>


.btn-primary {
  padding: 8px 12px;
  font-size: 10px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: black;
  border: none;
  border-radius: 15px;
  box-shadow: 0 5px #999;
}

.btn-primary:hover {background-color: #79eecb}

.btn-primary:active {
  background-color: black;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

.font-weight-bold mb-0{
  font-size : 18px !important
}

.card-body{
  font-size: 16px !important
}

</style>

<!-- Card 1 -->
<div class="row"> 

  <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-4">

      <div class="card" >

      <div class="card-header white">
        <p class="text-uppercase small mb-2">Project Name </p>
        <h5 class="font-weight-bold mb-0">
        <?php echo e($value['project_name']); ?>

        </h5>
      </div>

      <div class="card-body">
        <p>METERS: <?php echo e($value['detail'][0]->device_count); ?>

         | <span>ON: <?php echo e($value['detail'][0]->on_device_count); ?> </span>
         | <span>RECENTLY OFF: <?php echo e($value['detail'][0]->yellow_device_count); ?> </span>
         | <span>OFF: <?php echo e($value['detail'][0]->off_device_count); ?> </span>
        </p>

        <p>CLIENTS: <?php echo e($value['detail'][0]->client_count); ?> 
         | <span>ON: <?php echo e($value['detail'][0]->on_client_count); ?> </span>
         | <span>RECENTLY OFF: <?php echo e($value['detail'][0]->yellow_client_count); ?> </span>
         | <span>OFF: <?php echo e($value['detail'][0]->off_client_count); ?> </span>
        </p>
    
      </div>

      <div class="card-footer white">
        <Next type="Next" class="btn-primary">
          <a href="<?php echo e(url('/deviceinfo/'.$value['id'])); ?>" class="text-sm text-gray-700 underline">Details</a>
        </Next>
        <span style="font-size: 100%; float: right "> DATE: <?php echo e($value['detail'][0]->date); ?> </span>

      </div>

      </div>
    </div>

  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Style.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Model\resources\views/Admin/dashboard.blade.php ENDPATH**/ ?>