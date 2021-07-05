

<?php $__env->startSection('title'); ?>

HETA DATAIN|DASHBOARD

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>


.btn-primary {
  padding: 8px 12px;
  font-size: 20px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: black;
  border: none;
  border-radius: 15px;
  box-shadow: 0 9px #999;
}

.btn-primary:hover {background-color: cadetblue}

.btn-primary:active {
  background-color: black;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

.font-weight-bold mb-0{
  font-size : 30px;
}

.card-body{
  font-size: 25px; 
}

</style>

<!-- Card 1 -->
<div class="row"> 
  
  <div class="col-md-12">

    <div class="card" >

          <div class="card-header white">
            <p class="text-uppercase small mb-2">Project Name </p>
            <h5 class="font-weight-bold mb-0">
          <?php echo e($data['project_name']); ?>

            </h5>
          </div>

      <div class="card-body">
     
        <table class="table">
            <thead>
              <tr>
                <th scope="col">DEVICE ID</th>
                <th scope="col">CLIENT ID</th>
                <th scope="col">FEEDER NAME</th>
                <th scope="col">DATE-TIME</th>

              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $data['detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr class="<?php echo e($value->color); ?>">
                <th scope="row"><?php echo e($value->device_id); ?></th>
                <td><?php echo e($value->client_id); ?></td>
                <td><?php echo e($value->device_name); ?></td>
                <td><?php echo e($value->dt_time); ?></td>
              </tr> 
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>   
            </tbody>
        </table>
      </div>
    </div>
  </div>

</div>



<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Style.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Model\resources\views/Admin/deviceinfo.blade.php ENDPATH**/ ?>